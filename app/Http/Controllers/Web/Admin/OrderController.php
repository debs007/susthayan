<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use App\Models\Franchise;
use App\Models\Order;
use App\Services\Inventory\StockService;
use App\Services\Invoicing\InvoiceService;
use App\Services\Orders\OrderFulfillmentService;
use App\Services\Payment\PaymentConfirmationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        private readonly StockService $stock,
        private readonly InvoiceService $invoices,
    ) {}


    /** Unlike franchise.orders.index (scoped to one store), this spans every franchise - the actual cross-store overview that didn't exist anywhere before. */
    public function index(Request $request): View
    {
        $orders = Order::with(['user:id,name,mobile', 'franchise:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('order_type'), fn ($q) => $q->where('order_type', $request->string('order_type')))
            ->when($request->filled('franchise_id'), fn ($q) => $q->where('franchise_id', $request->integer('franchise_id')))
            ->when($request->boolean('unassigned'), fn ($q) => $q->whereNull('franchise_id')->where('order_type', 'product'))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';
                $query->where(fn ($q) => $q->where('id', $request->string('search'))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('mobile', 'like', $term)));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Enriched detail - every line item, the full payment history (not
     * just the latest one - a failed attempt followed by a successful
     * retry should both be visible here), and whichever
     * booking/appointment detail applies to this specific order_type,
     * same shape OrderResource already exposes to the app.
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'franchise',
            'address',
            'items.product',
            'payments',
            'deliveryAssignments.deliveryAgent',
            'labTestBooking.labTest',
            'labTestBooking.labCenter',
            'labTestBookings.labTest',
            'labTestBookings.labCenter',
            'appointmentBooking.doctor',
            'appointmentBooking.hospital',
        ]);

        $franchises = Franchise::where('status', 'active')->orderBy('name')->get();

        return view('admin.orders.show', compact('order', 'franchises'));
    }

    /**
     * The actual counterpart to orders no longer picking a franchise at
     * checkout - this is the first point a franchise is checked against
     * this order's items at all, since placeOrder() never checked stock
     * (there was no franchise to check it against yet).
     */
    public function assign(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['franchise_id' => ['required', 'integer', 'exists:franchises,id']]);

        if ($order->franchise_id !== null) {
            return back()->with('error', 'This order already has a franchise assigned.');
        }

        $franchiseId = $request->integer('franchise_id');
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $available = $this->stock->available($franchiseId, $item->product_id);
            if ($available < $item->quantity) {
                return back()->with('error', "Insufficient stock at that franchise for order item #{$item->product_id} (needs {$item->quantity}, has {$available}).");
            }
        }

        try {
            DB::transaction(function () use ($order, $franchiseId) {
                $order->update(['franchise_id' => $franchiseId]);
                // The actual reservation - this couldn't happen at payment
                // confirmation, since no franchise was known yet at that
                // point. This is the first moment one is.
                $this->stock->reserveForOrder($order);
            });
        } catch (InsufficientStockException $e) {
            // Rare - stock moved between the check just above and the
            // reservation itself. The transaction already rolled back the
            // franchise assignment, so the order is still safely unassigned.
            return back()->with('error', 'Stock changed just now and is no longer sufficient - please try again.');
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order assigned to franchise.');
    }

    /**
     * General-purpose status override for admin - distinct from the
     * franchise-scoped, sequential flow franchise staff use
     * (OrderFulfillmentService, still the one enforcing valid preparing ->
     * ... -> delivered/picked_up transitions). Admin needs the ability to
     * step outside that sequence: manually confirm a stuck payment, or
     * cancel at any cancellable point.
     */
    public function updateStatus(Request $request, Order $order, OrderFulfillmentService $fulfillment, PaymentConfirmationService $paymentConfirmation): RedirectResponse
    {
        $newStatus = $request->string('new_status')->value();

        if ($newStatus === 'confirmed') {
            if ($order->status !== OrderStatus::PendingPayment) {
                return back()->with('error', 'Only a pending-payment order can be manually confirmed.');
            }

            // Same bypass mechanism checkout itself uses when payments are
            // off - a real payment record, run through the same
            // markSuccessful() every genuine payment goes through, rather
            // than a second, separate way of reaching "confirmed".
            $payment = CustomerPayment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'gateway' => 'admin_override',
                'status' => PaymentStatus::Initiated,
            ]);
            $paymentConfirmation->markSuccessful($payment);

            return back()->with('success', 'Order marked as confirmed.');
        }

        if (in_array($newStatus, ['preparing', 'ready_for_dispatch', 'out_for_delivery', 'delivered', 'picked_up'], true)) {
            try {
                $fulfillment->transition($order, $newStatus);
            } catch (RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            } catch (InsufficientStockException $e) {
                return back()->with('error', 'Insufficient stock to fulfil this order: '.$e->getMessage());
            }

            return back()->with('success', 'Order status updated.');
        }

        if ($newStatus === 'cancelled') {
            if (! $order->status->isCancellable()) {
                return back()->with('error', "An order at \"{$order->status->value}\" can no longer be cancelled.");
            }

            DB::transaction(function () use ($order) {
                // Only a confirmed-or-later order ever had stock reserved
                // (via admin's own assign() action) - releasing for a
                // still-pending_payment order is a safe no-op either way,
                // since nothing was ever reserved for it.
                if ($order->franchise_id !== null) {
                    $this->stock->releaseForOrder($order);
                }

                $order->update(['status' => OrderStatus::Cancelled]);
            });

            return back()->with('success', 'Order cancelled.');
        }

        return back()->with('error', 'Unrecognised status.');
    }

    public function downloadInvoice(Order $order)
    {
        abort_unless($order->invoice, 404, 'No invoice has been generated for this order yet.');

        $order->load(['franchise', 'user', 'items.product', 'address']);

        return $this->invoices->renderPdf($order, $order->invoice)->download("invoice-{$order->invoice->invoice_number}.pdf");
    }
}

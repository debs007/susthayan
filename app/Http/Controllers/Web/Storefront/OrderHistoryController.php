<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Invoicing\InvoiceService;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices) {}

    /** Excludes pending_payment - same reasoning as the admin dashboard's recent-orders widget: an abandoned, never-paid checkout attempt isn't a real order from the customer's own point of view. */
    public function index(): View
    {
        $orders = Order::where('user_id', auth('web')->id())
            ->whereNotIn('status', ['pending_payment'])
            ->with(['items.product', 'labTestBooking.labTest', 'appointmentBooking.doctor', 'invoice'])
            ->latest()
            ->paginate(15);

        return view('storefront.orders.index', compact('orders'));
    }

    public function downloadInvoice(Order $order)
    {
        abort_unless($order->user_id === auth('web')->id(), 403);
        abort_unless($order->invoice, 404, 'No invoice has been generated for this order yet.');

        $order->load(['franchise', 'user', 'items.product', 'address']);

        return $this->invoices->renderPdf($order, $order->invoice)->download("invoice-{$order->invoice->invoice_number}.pdf");
    }
}

<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\CaptureSupplierInvoiceRequest;
use App\Http\Requests\Franchise\CreatePurchaseOrderRequest;
use App\Http\Requests\Franchise\ReceiveGoodsRequest;
use App\Http\Requests\Franchise\RecordSupplierPaymentRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Services\Purchasing\GoodsReceiptService;
use App\Services\Purchasing\InvoiceMatchingService;
use App\Services\Purchasing\PurchaseOrderService;
use App\Services\Purchasing\SupplierPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $purchaseOrders,
        private readonly GoodsReceiptService $goodsReceipts,
        private readonly InvoiceMatchingService $invoices,
        private readonly SupplierPaymentService $payments,
    ) {}

    public function index(Request $request): View
    {
        $purchaseOrders = PurchaseOrder::where('franchise_id', $request->user()->franchise_id)
            ->with(['supplier', 'items'])
            ->latest()
            ->paginate(20);

        return view('franchise.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('franchise.purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(CreatePurchaseOrderRequest $request): RedirectResponse
    {
        $po = $this->purchaseOrders->create(
            creator: $request->user(),
            franchiseId: $request->user()->franchise_id,
            supplierId: $request->validated('supplier_id'),
            items: $request->validated('items'),
            expectedDate: $request->validated('expected_date'),
        );

        return redirect()->route('franchise.purchase-orders.show', $po)->with('success', "PO #{$po->id} created - awaiting owner approval.");
    }

    // franchise.scope confirms this PO belongs to the caller's franchise.
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load([
            'supplier', 'items.product', 'goodsReceipts.items.product', 'invoices.payments',
        ]);
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('franchise.purchase-orders.show', compact('purchaseOrder', 'products'));
    }

    // role:Franchise Owner is applied on this route in routes/web.php - same separation of duties as the API.
    public function approve(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrders->approve($purchaseOrder, $request->user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return back()->with('success', "PO #{$purchaseOrder->id} approved.");
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrders->reject($purchaseOrder, $request->user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['approval' => $e->getMessage()]);
        }

        return back()->with('success', "PO #{$purchaseOrder->id} rejected.");
    }

    public function receiveGoods(ReceiveGoodsRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->goodsReceipts->receive(
                $request->user(),
                $purchaseOrder,
                $request->validated('items'),
                $request->validated('received_date'),
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['grn' => $e->getMessage()])->withInput();
        }

        return redirect()->route('franchise.purchase-orders.show', $purchaseOrder)->with('success', 'Goods received - stock updated.');
    }

    public function captureInvoice(CaptureSupplierInvoiceRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->invoices->capture(
            creator: $request->user(),
            supplierId: $request->validated('supplier_id'),
            purchaseOrderId: $request->validated('purchase_order_id'),
            goodsReceiptId: $request->validated('goods_receipt_id'),
            invoiceNumber: $request->validated('invoice_number'),
            invoiceDate: $request->validated('invoice_date'),
            invoiceAmount: $request->validated('invoice_amount'),
            gstAmount: $request->validated('gst_amount'),
            dueDate: $request->validated('due_date'),
        );

        return redirect()->route('franchise.purchase-orders.show', $purchaseOrder)->with('success', 'Invoice captured and auto-matched against the GRN.');
    }

    public function recordPayment(RecordSupplierPaymentRequest $request, PurchaseOrder $purchaseOrder, SupplierInvoice $invoice): RedirectResponse
    {
        try {
            $this->payments->record(
                $invoice,
                $request->validated('amount'),
                $request->validated('payment_date'),
                $request->validated('payment_mode'),
                $request->validated('reference_number'),
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->route('franchise.purchase-orders.show', $purchaseOrder)->with('success', 'Payment recorded.');
    }
}

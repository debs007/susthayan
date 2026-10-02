<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\Purchasing\VendorLedgerService;
use Illuminate\View\View;

class VendorLedgerController extends Controller
{
    public function __construct(private readonly VendorLedgerService $service) {}

    /** Network-wide outstanding, every franchise combined - same service the API uses, just rendered as a page instead of JSON. */
    public function outstanding(): View
    {
        $outstanding = $this->service->outstanding();
        $recentPurchaseOrders = PurchaseOrder::with(['supplier', 'franchise'])->latest()->limit(15)->get();

        return view('admin.vendors.outstanding', compact('outstanding', 'recentPurchaseOrders'));
    }

    public function ledger(Supplier $supplier): View
    {
        $ledger = $this->service->ledger($supplier);

        return view('admin.vendors.ledger', compact('supplier', 'ledger'));
    }

    /** Read-only - admin has oversight here, not creation or action, same framing as the outstanding list this links from. */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load([
            'supplier', 'franchise', 'createdBy', 'approvedBy',
            'items.product',
            'goodsReceipts.items', 'goodsReceipts.receivedBy',
            'invoices.payments',
        ]);

        return view('admin.vendors.purchase-order-show', compact('purchaseOrder'));
    }
}

<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\CaptureSupplierInvoiceRequest;
use App\Http\Resources\SupplierInvoiceResource;
use App\Models\SupplierInvoice;
use App\Services\Purchasing\InvoiceMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class SupplierInvoiceController extends Controller
{
    public function __construct(private readonly InvoiceMatchingService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        // Scoped through the linked PO's franchise - an invoice with no PO
        // isn't attributable to one franchise, so it won't show here (it's
        // still visible to Admin).
        $invoices = SupplierInvoice::whereHas(
            'purchaseOrder',
            fn ($query) => $query->where('franchise_id', $request->user()->franchise_id)
        )->latest()->paginate(20);

        return SupplierInvoiceResource::collection($invoices);
    }

    public function store(CaptureSupplierInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->service->capture(
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

        return (new SupplierInvoiceResource($invoice))->response()->setStatusCode(201);
    }

    // role:Franchise Owner is applied on this route in routes/api.php
    public function approve(SupplierInvoice $supplierInvoice): JsonResponse
    {
        try {
            $invoice = $this->service->approve($supplierInvoice);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['invoice' => new SupplierInvoiceResource($invoice)]);
    }
}

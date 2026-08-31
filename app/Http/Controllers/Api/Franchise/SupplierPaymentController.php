<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\RecordSupplierPaymentRequest;
use App\Models\SupplierInvoice;
use App\Services\Purchasing\SupplierPaymentService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class SupplierPaymentController extends Controller
{
    public function __construct(private readonly SupplierPaymentService $service) {}

    public function store(RecordSupplierPaymentRequest $request, SupplierInvoice $supplierInvoice): JsonResponse
    {
        try {
            $payment = $this->service->record(
                invoice: $supplierInvoice,
                amount: $request->validated('amount'),
                paymentDate: $request->validated('payment_date'),
                paymentMode: $request->validated('payment_mode'),
                referenceNumber: $request->validated('reference_number'),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['payment' => $payment->fresh()], 201);
    }
}

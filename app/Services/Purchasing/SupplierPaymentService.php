<?php

namespace App\Services\Purchasing;

use App\Enums\SupplierInvoiceStatus;
use App\Enums\SupplierPaymentStatus;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierPaymentService
{
    public function record(
        SupplierInvoice $invoice,
        float $amount,
        string $paymentDate,
        string $paymentMode,
        ?string $referenceNumber,
    ): SupplierPayment {
        return DB::transaction(function () use ($invoice, $amount, $paymentDate, $paymentMode, $referenceNumber) {
            $invoice = SupplierInvoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if (! in_array($invoice->status, [SupplierInvoiceStatus::Approved, SupplierInvoiceStatus::Paid], true)) {
                throw new RuntimeException(
                    "Only an approved invoice can be paid (this one is \"{$invoice->status->value}\")."
                );
            }

            $alreadyPaid = (float) SupplierPayment::where('supplier_invoice_id', $invoice->id)
                ->where('status', SupplierPaymentStatus::Completed)
                ->sum('amount_paid');

            $invoiceTotal = (float) $invoice->invoice_amount + (float) $invoice->gst_amount;
            $remaining = round($invoiceTotal - $alreadyPaid, 2);

            if ($amount > $remaining + 0.01) {
                throw new RuntimeException(
                    "That payment (₹{$amount}) is more than what's left on this invoice (₹{$remaining})."
                );
            }

            $payment = SupplierPayment::create([
                'supplier_id' => $invoice->supplier_id,
                'supplier_invoice_id' => $invoice->id,
                'amount_paid' => $amount,
                'payment_date' => $paymentDate,
                'payment_mode' => $paymentMode,
                'reference_number' => $referenceNumber,
                'status' => SupplierPaymentStatus::Completed,
            ]);

            if ($amount >= $remaining - 0.01) {
                $invoice->update(['status' => SupplierInvoiceStatus::Paid]);
            }

            return $payment;
        });
    }
}

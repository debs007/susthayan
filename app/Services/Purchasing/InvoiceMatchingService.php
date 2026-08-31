<?php

namespace App\Services\Purchasing;

use App\Enums\SupplierInvoiceStatus;
use App\Models\SupplierInvoice;
use App\Models\User;
use RuntimeException;

/**
 * The "PO-GRN-Invoice matched" step from the SRS purchase flow. Deliberately
 * simple for this pass: compares the invoice's declared (ex-GST) amount
 * against what the linked GRN's lines actually add up to, within a small
 * tolerance for rounding. A real AP team would also want per-line matching
 * (rate discrepancies on individual products, not just the total) - noted
 * as a natural next refinement rather than built now.
 */
class InvoiceMatchingService
{
    private const float TOLERANCE_PERCENT = 2.0;

    public function capture(
        User $creator,
        int $supplierId,
        ?int $purchaseOrderId,
        ?int $goodsReceiptId,
        string $invoiceNumber,
        string $invoiceDate,
        float $invoiceAmount,
        float $gstAmount,
        ?string $dueDate,
    ): SupplierInvoice {
        $invoice = SupplierInvoice::create([
            'supplier_id' => $supplierId,
            'purchase_order_id' => $purchaseOrderId,
            'goods_receipt_id' => $goodsReceiptId,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'invoice_amount' => $invoiceAmount,
            'gst_amount' => $gstAmount,
            'status' => SupplierInvoiceStatus::PendingMatch,
            'due_date' => $dueDate,
        ]);

        $this->attemptMatch($invoice);

        return $invoice->fresh();
    }

    public function attemptMatch(SupplierInvoice $invoice): void
    {
        if (! $invoice->goods_receipt_id) {
            return; // nothing to match against yet - stays pending_match until a GRN is linked
        }

        $grn = $invoice->goodsReceipt()->with('items')->first();
        $grnSubtotal = (float) $grn->items->sum(fn ($item) => $item->received_qty * $item->purchase_rate);

        $variance = $grnSubtotal > 0
            ? abs((float) $invoice->invoice_amount - $grnSubtotal) / $grnSubtotal * 100
            : 100.0;

        $invoice->update([
            'status' => $variance <= self::TOLERANCE_PERCENT
                ? SupplierInvoiceStatus::Matched
                : SupplierInvoiceStatus::Disputed,
        ]);
    }

    public function approve(SupplierInvoice $invoice): SupplierInvoice
    {
        if ($invoice->status !== SupplierInvoiceStatus::Matched) {
            throw new RuntimeException(
                "Only a matched invoice can be approved (this one is \"{$invoice->status->value}\")."
            );
        }

        $invoice->update(['status' => SupplierInvoiceStatus::Approved]);

        return $invoice->fresh();
    }
}

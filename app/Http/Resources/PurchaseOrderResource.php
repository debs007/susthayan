<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\PurchaseOrder */
class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'supplier' => $this->whenLoaded('supplier', fn () => ['id' => $this->supplier->id, 'name' => $this->supplier->name]),
            'franchise_id' => $this->franchise_id,
            'po_date' => $this->po_date?->toDateString(),
            'expected_date' => $this->expected_date?->toDateString(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'total_amount' => (string) $this->total_amount,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'ordered_qty' => $item->ordered_qty,
                'expected_rate' => (string) $item->expected_rate,
            ])),
            'goods_receipts' => $this->whenLoaded('goodsReceipts', fn () => $this->goodsReceipts->map(fn ($grn) => [
                'id' => $grn->id,
                'received_date' => $grn->received_date?->toDateString(),
                'items' => $grn->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'batch_no' => $item->batch_no,
                    'expiry_date' => $item->expiry_date?->toDateString(),
                    'received_qty' => $item->received_qty,
                    'damaged_qty' => $item->damaged_qty,
                ]),
            ])),
            'invoices' => $this->whenLoaded('invoices', fn () => $this->invoices->map(fn ($invoice) => [
                'id' => $invoice->id,
                'status' => $invoice->status->value,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date?->toDateString(),
                'due_date' => $invoice->due_date?->toDateString(),
                'invoice_amount' => (string) $invoice->invoice_amount,
                'gst_amount' => (string) $invoice->gst_amount,
                'total_amount' => (string) ((float) $invoice->invoice_amount + (float) $invoice->gst_amount),
                'payments' => $invoice->relationLoaded('payments') ? $invoice->payments->map(fn ($payment) => [
                    'id' => $payment->id,
                    'amount_paid' => (string) $payment->amount_paid,
                    'payment_date' => $payment->payment_date?->toDateString(),
                    'payment_mode' => $payment->payment_mode,
                    'reference_number' => $payment->reference_number,
                    'status' => $payment->status->value,
                ]) : [],
            ])),
        ];
    }
}

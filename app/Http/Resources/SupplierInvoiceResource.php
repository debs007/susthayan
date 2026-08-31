<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\SupplierInvoice */
class SupplierInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'supplier_id' => $this->supplier_id,
            'purchase_order_id' => $this->purchase_order_id,
            'goods_receipt_id' => $this->goods_receipt_id,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'invoice_amount' => (string) $this->invoice_amount,
            'gst_amount' => (string) $this->gst_amount,
            'total_amount' => (string) ((float) $this->invoice_amount + (float) $this->gst_amount),
        ];
    }
}

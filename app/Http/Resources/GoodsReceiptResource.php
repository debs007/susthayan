<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\GoodsReceipt */
class GoodsReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchase_order_id,
            'received_date' => $this->received_date?->toDateString(),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'batch_no' => $item->batch_no,
                'expiry_date' => $item->expiry_date?->toDateString(),
                'received_qty' => $item->received_qty,
                'damaged_qty' => $item->damaged_qty,
                'purchase_rate' => (string) $item->purchase_rate,
            ])),
        ];
    }
}

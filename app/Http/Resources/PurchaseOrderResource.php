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
        ];
    }
}

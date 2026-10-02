<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\OrderItem */
class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->whenLoaded('product', fn () => $this->product->name),
            'quantity' => $this->quantity,
            'unit_price' => (string) $this->unit_price,
            'tax_percentage' => (string) $this->tax_percentage,
            'total_price' => (string) $this->total_price,
            // Set only by OrderFulfillmentController@index (the
            // franchise's own order list) via the same dynamic-property
            // pattern ProductPricingService::attach() uses for
            // resolved_stock - simply absent for any other consumer of
            // this shared resource, since "available at this franchise"
            // is meaningless outside that one context.
            'available_quantity' => $this->when(isset($this->available_quantity), fn () => $this->available_quantity),
            'is_available' => $this->when(isset($this->available_quantity), fn () => $this->available_quantity >= $this->quantity),
            // Only meaningful once the order is fulfilled - null before that.
            'batches' => $this->whenLoaded('batches', fn () => $this->batches->map(fn ($b) => [
                'batch_no' => $b->inventory?->batch_no,
                'quantity' => $b->quantity,
            ])),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'fulfillment_type' => $this->fulfillment_type->value,
            'franchise' => $this->whenLoaded('franchise', fn () => [
                'id' => $this->franchise->id,
                'name' => $this->franchise->name,
            ]),
            'customer' => $this->fulfillment_type->value === 'pos' ? [
                'walk_in_name' => $this->walk_in_customer_name,
                'walk_in_phone' => $this->walk_in_customer_phone,
            ] : null,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'subtotal_amount' => (string) $this->subtotal_amount,
            'discount_amount' => (string) $this->discount_amount,
            'tax_amount' => (string) $this->tax_amount,
            'delivery_charge' => (string) $this->delivery_charge,
            'total_amount' => (string) $this->total_amount,
            'requires_prescription' => $this->requires_prescription,
            'prescription_note' => $this->prescription_note,
            'placed_at' => $this->created_at?->toIso8601String(),
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
            'prepared_at' => $this->prepared_at?->toIso8601String(),
            'out_for_delivery_at' => $this->out_for_delivery_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
        ];
    }
}

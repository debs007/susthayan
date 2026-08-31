<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DeliveryAssignment */
class DeliveryAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'failure_reason' => $this->failure_reason,
            'order' => $this->whenLoaded('order', fn () => [
                'id' => $this->order->id,
                'total_amount' => (string) $this->order->total_amount,
                'address' => $this->order->relationLoaded('address') ? [
                    'line1' => $this->order->address?->line1,
                    'line2' => $this->order->address?->line2,
                    'city' => $this->order->address?->city,
                    'pincode' => $this->order->address?->pincode,
                    'latitude' => $this->order->address?->latitude,
                    'longitude' => $this->order->address?->longitude,
                ] : null,
                'customer_name' => $this->order->user?->name ?? $this->order->walk_in_customer_name,
                'items' => $this->order->relationLoaded('items')
                    ? OrderItemResource::collection($this->order->items)
                    : [],
            ]),
            'assigned_at' => $this->assigned_at?->toIso8601String(),
            'picked_up_at' => $this->picked_up_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
        ];
    }
}

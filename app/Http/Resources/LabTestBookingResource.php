<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabTestBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'lab_test' => $this->whenLoaded('labTest', fn () => [
                'id' => $this->labTest->id,
                'name' => $this->labTest->name,
            ]),
            'booking_type' => $this->booking_type,
            'scheduled_date' => $this->scheduled_date?->toDateString(),
            'status' => $this->status,
        ];
    }
}

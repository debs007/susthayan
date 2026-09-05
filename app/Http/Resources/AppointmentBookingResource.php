<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'doctor' => $this->whenLoaded('doctor', fn () => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->name,
                'degree' => $this->doctor->degree,
            ]),
            'hospital' => $this->whenLoaded('hospital', fn () => [
                'id' => $this->hospital->id,
                'name' => $this->hospital->name,
                'address' => $this->hospital->fullAddress(),
            ]),
            'scheduled_date' => $this->scheduled_date?->toDateString(),
            'status' => $this->status,
        ];
    }
}

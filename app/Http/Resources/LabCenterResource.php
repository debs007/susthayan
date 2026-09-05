<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabCenterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'phone' => $this->phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'offers_home_collection' => $this->offers_home_collection,
            // Only present when this resource wraps a center loaded via
            // $test->centers (the pivot) - the price is specific to a
            // center+test pair, not a property of the center alone.
            'price' => $this->whenPivotLoaded('lab_center_test', fn () => (string) $this->pivot->price),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'blood_group' => $this->blood_group,
            'height_cm' => $this->height_cm,
            'weight_kg' => $this->weight_kg,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'age' => $this->age,
            'gender' => $this->gender,
        ];
    }
}

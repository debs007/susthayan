<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'heart_rate_bpm' => $this->heart_rate_bpm,
            'blood_pressure_systolic' => $this->blood_pressure_systolic,
            'blood_pressure_diastolic' => $this->blood_pressure_diastolic,
            'spo2_percentage' => $this->spo2_percentage,
            'temperature_fahrenheit' => $this->temperature_fahrenheit,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
        ];
    }
}

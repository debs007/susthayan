<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'name' => $this->name,
            'description' => $this->description,
            'sample_type' => $this->sample_type,
            'preparation_instructions' => $this->preparation_instructions,
            'price' => (string) $this->price,
            'requires_center_visit' => $this->requires_center_visit,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}

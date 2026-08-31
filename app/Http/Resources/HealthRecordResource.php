<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'has_file' => $this->file_path !== null,
            'record_date' => $this->record_date?->toDateString(),
            'notes' => $this->notes,
        ];
    }
}

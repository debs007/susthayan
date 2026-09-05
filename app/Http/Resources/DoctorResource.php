<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'degree' => $this->degree,
            'department' => $this->whenLoaded('department', fn () => $this->department?->name),
            'years_of_experience' => $this->years_of_experience,
            'bio' => $this->bio,
            'photo_url' => $this->photo_url,
            'hospitals' => $this->whenLoaded('affiliations', fn () => $this->affiliations->map(fn ($affiliation) => [
                'affiliation_id' => $affiliation->id,
                'hospital_id' => $affiliation->hospital_id,
                'hospital_name' => $affiliation->hospital->name,
                'hospital_address' => $affiliation->hospital->fullAddress(),
                'consultation_charge' => (string) $affiliation->consultation_charge,
                'visit_days' => $affiliation->visitDays->map(fn ($day) => [
                    'day_of_week' => $day->day_of_week,
                    'start_time' => substr($day->start_time, 0, 5),
                    'end_time' => substr($day->end_time, 0, 5),
                ]),
            ])),
        ];
    }
}

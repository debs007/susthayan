<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'affiliation_id' => ['required', 'integer', 'exists:doctor_hospital_affiliations,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}

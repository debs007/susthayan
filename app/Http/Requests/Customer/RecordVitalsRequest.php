<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RecordVitalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heart_rate_bpm' => ['nullable', 'integer', 'min:20', 'max:250'],
            'blood_pressure_systolic' => ['nullable', 'integer', 'min:50', 'max:250'],
            'blood_pressure_diastolic' => ['nullable', 'integer', 'min:30', 'max:150'],
            'spo2_percentage' => ['nullable', 'integer', 'min:50', 'max:100'],
            'temperature_fahrenheit' => ['nullable', 'numeric', 'min:90', 'max:110'],
            'recorded_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $metrics = ['heart_rate_bpm', 'blood_pressure_systolic', 'blood_pressure_diastolic', 'spo2_percentage', 'temperature_fahrenheit'];

            if (collect($metrics)->every(fn ($key) => ! $this->filled($key))) {
                $validator->errors()->add('heart_rate_bpm', 'Log at least one reading.');
            }

            // Systolic/diastolic only make sense together - one without the other isn't a valid reading.
            if ($this->filled('blood_pressure_systolic') !== $this->filled('blood_pressure_diastolic')) {
                $validator->errors()->add('blood_pressure_diastolic', 'Blood pressure needs both systolic and diastolic values.');
            }
        });
    }
}

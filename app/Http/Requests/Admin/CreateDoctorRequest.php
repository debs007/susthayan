<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:150'],
            'degree' => ['required', 'string', 'max:150'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:80'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hospitals' => ['required', 'array', 'min:1'],
            'hospitals.*.selected' => ['sometimes', 'boolean'],
            'hospitals.*.charge' => ['nullable', 'numeric', 'min:0'],
            'hospitals.*.start_time' => ['nullable', 'date_format:H:i'],
            'hospitals.*.end_time' => ['nullable', 'date_format:H:i', 'after:hospitals.*.start_time'],
            'hospitals.*.days' => ['nullable', 'array'],
            'hospitals.*.days.*' => [Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $atLeastOneSelected = false;

            foreach ($this->input('hospitals', []) as $hospitalId => $row) {
                if (! ($row['selected'] ?? false)) {
                    continue;
                }
                $atLeastOneSelected = true;

                if (! is_numeric($row['charge'] ?? null)) {
                    $validator->errors()->add("hospitals.$hospitalId.charge", 'Enter a consultation charge for every hospital you check.');
                }
                if (empty($row['days'] ?? [])) {
                    $validator->errors()->add("hospitals.$hospitalId.days", 'Select at least one visit day for every hospital you check.');
                }
                if (empty($row['start_time'] ?? null) || empty($row['end_time'] ?? null)) {
                    $validator->errors()->add("hospitals.$hospitalId.start_time", 'Set visit hours for every hospital you check.');
                }
            }

            if (! $atLeastOneSelected) {
                $validator->errors()->add('hospitals', 'Select at least one hospital this doctor is affiliated with.');
            }
        });
    }

    /** Just the hospitals actually checked, shaped for the controller to create affiliations + visit days from. */
    public function selectedHospitals(): array
    {
        $result = [];
        foreach ($this->input('hospitals', []) as $hospitalId => $row) {
            if ($row['selected'] ?? false) {
                $result[$hospitalId] = [
                    'charge' => $row['charge'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'days' => $row['days'] ?? [],
                ];
            }
        }

        return $result;
    }
}

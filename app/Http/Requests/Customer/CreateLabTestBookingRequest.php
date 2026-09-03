<?php

namespace App\Http\Requests\Customer;

use App\Models\LabTest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreateLabTestBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lab_test_id' => ['required', 'integer', 'exists:lab_tests,id'],
            'franchise_id' => ['required', 'integer', 'exists:franchises,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            // Required only for home-visit tests - enforced in
            // withValidator below, since whether it's required depends on
            // which test was selected, not a fixed rule.
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $test = LabTest::find($this->input('lab_test_id'));
            if ($test && ! $test->requires_center_visit && ! $this->filled('address_id')) {
                $validator->errors()->add('address_id', 'An address is required for a home-visit test.');
            }
        });
    }
}

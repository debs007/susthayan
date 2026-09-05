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
            'lab_center_id' => ['required', 'integer', 'exists:lab_centers,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            // Required only for home-collection tests - enforced in
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
                $validator->errors()->add('address_id', 'An address is required for a home-collection test.');
            }

            // Sanity check that the selected center actually offers this
            // specific test - the app's own picker only shows qualifying
            // centers, but the API shouldn't trust that client-side
            // filtering is what actually enforces this.
            if ($test && $this->filled('lab_center_id')) {
                $offersTest = $test->centers()->where('lab_centers.id', $this->input('lab_center_id'))->exists();
                if (! $offersTest) {
                    $validator->errors()->add('lab_center_id', 'That center does not offer this test.');
                }
            }
        });
    }
}

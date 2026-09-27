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
            // Always a list now, even for a single test - one test is
            // just a one-element array, so there's one code path here
            // and in the service rather than two parallel ones.
            'lab_test_ids' => ['required', 'array', 'min:1'],
            'lab_test_ids.*' => ['integer', 'distinct', 'exists:lab_tests,id'],
            'lab_center_id' => ['required', 'integer', 'exists:lab_centers,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            // Required only if any selected test is home-collection -
            // enforced in withValidator below, since whether it's
            // required depends on which tests were selected, not a
            // fixed rule.
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $testIds = $this->input('lab_test_ids', []);
            $tests = LabTest::whereIn('id', $testIds)->get();

            $anyHomeCollection = $tests->contains(fn (LabTest $test) => ! $test->requires_center_visit);
            if ($anyHomeCollection && ! $this->filled('address_id')) {
                $validator->errors()->add('address_id', 'An address is required when any selected test offers home collection.');
            }

            // Sanity check that the selected center actually offers
            // every selected test - the app's own picker only shows
            // qualifying centers, but the API shouldn't trust that
            // client-side filtering is what actually enforces this.
            if ($tests->isNotEmpty() && $this->filled('lab_center_id')) {
                $centerId = $this->input('lab_center_id');
                foreach ($tests as $test) {
                    $offersTest = $test->centers()->where('lab_centers.id', $centerId)->exists();
                    if (! $offersTest) {
                        $validator->errors()->add('lab_center_id', "That center does not offer {$test->name}.");
                        break;
                    }
                }
            }
        });
    }
}

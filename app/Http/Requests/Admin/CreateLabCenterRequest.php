<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreateLabCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'franchise_id' => ['required', 'exists:franchises,id'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:15'],
            'offers_home_collection' => ['sometimes', 'boolean'],
            // Each checked test's price - tests.{id}.price is only
            // actually required when tests.{id}.selected is checked,
            // enforced below since a plain rule can't express "required
            // only if this specific sibling checkbox is on."
            'tests' => ['nullable', 'array'],
            'tests.*.selected' => ['sometimes', 'boolean'],
            'tests.*.price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('tests', []) as $testId => $row) {
                if (($row['selected'] ?? false) && ! is_numeric($row['price'] ?? null)) {
                    $validator->errors()->add("tests.$testId.price", 'Enter a price for every test you check.');
                }
            }
        });
    }

    /** Just the tests that were actually checked, as [testId => price] - what the controller actually needs to sync the pivot. */
    public function selectedTestPrices(): array
    {
        $prices = [];
        foreach ($this->input('tests', []) as $testId => $row) {
            if ($row['selected'] ?? false) {
                $prices[$testId] = ['price' => $row['price']];
            }
        }

        return $prices;
    }
}

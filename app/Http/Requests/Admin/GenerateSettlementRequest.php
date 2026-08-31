<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSettlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Only meaningful for the web form's single-franchise submit
            // button - the API instead binds the franchise straight from
            // the route (/franchises/{franchise}/settlements/generate),
            // so this stays optional rather than required.
            'franchise_id' => ['nullable', 'integer', 'exists:franchises,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start', 'before_or_equal:today'],
        ];
    }
}

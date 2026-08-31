<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SetProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Omit franchise_id entirely for a global default price.
            'franchise_id' => ['nullable', 'integer', 'exists:franchises,id'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0', 'lte:mrp'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'effective_from' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'selling_price.lte' => 'Selling price can\'t be more than MRP.',
        ];
    }
}

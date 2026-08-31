<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'salt_composition' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'drug_schedule' => ['required', 'in:otc,h,h1,x'],
            'prescription_required' => ['required', 'boolean'],
            'unit' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'description' => ['nullable', 'string'],
        ];
    }
}

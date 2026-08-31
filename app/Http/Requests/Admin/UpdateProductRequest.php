<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'salt_composition' => ['sometimes', 'nullable', 'string', 'max:255'],
            'manufacturer' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hsn_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'drug_schedule' => ['sometimes', 'in:otc,h,h1,x'],
            'prescription_required' => ['sometimes', 'boolean'],
            'unit' => ['sometimes', 'nullable', 'string', 'max:100'],
            'barcode' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($this->route('product'))],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

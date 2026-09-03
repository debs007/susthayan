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
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'salt_composition' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'drug_schedule' => ['required', 'in:otc,h,h1,x'],
            'prescription_required' => ['required', 'boolean'],
            'unit' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'description' => ['nullable', 'string'],
            // Same rules as UploadProductImageRequest - optional here
            // specifically (unlike that dedicated endpoint, where it's
            // required), since a product is still valid without a photo.
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=200,min_height=200'],
        ];
    }
}

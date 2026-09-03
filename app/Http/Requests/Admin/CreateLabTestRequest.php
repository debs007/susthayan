<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateLabTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lab_test_category_id' => ['required', 'exists:lab_test_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sample_type' => ['nullable', 'string', 'max:100'],
            'preparation_instructions' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'requires_center_visit' => ['sometimes', 'boolean'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ];
    }
}

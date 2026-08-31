<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'gstin' => ['sometimes', 'nullable', 'string', 'max:15'],
            'address' => ['sometimes', 'nullable', 'string'],
            'bank_account_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bank_account_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'bank_ifsc' => ['sometimes', 'nullable', 'string', 'max:15'],
            'credit_days' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:365'],
            'contact_person' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:15'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

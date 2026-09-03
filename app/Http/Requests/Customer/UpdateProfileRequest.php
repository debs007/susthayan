<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            // Deliberately separate from the OTP-verified `mobile` field,
            // which never appears in this request at all - there's no rule
            // here that could accidentally let it through.
            'alternate_mobile' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}

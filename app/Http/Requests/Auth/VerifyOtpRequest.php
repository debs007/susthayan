<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'code' => ['required', 'digits:6'],
            // Only used the first time this mobile number logs in (account creation).
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}

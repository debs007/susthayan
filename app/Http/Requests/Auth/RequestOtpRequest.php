<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RequestOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public endpoint
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Enter a valid 10-digit Indian mobile number.',
        ];
    }
}

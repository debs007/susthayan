<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** There's no self-service "forgot password" flow (staff don't have email-based reset like Breeze-style apps) - this is the only recovery path for a staff member locked out of their account. */
class ResetStaffPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}

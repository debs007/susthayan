<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Deliberately no role/password change here - see AssignRoleRequest and ResetStaffPasswordRequest, kept separate so a routine profile edit can't accidentally also move someone's role or wipe their password. */
class UpdateStaffUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'is_active' => ['sometimes', 'boolean'],
            'two_factor_enabled' => ['sometimes', 'boolean'],
        ];
    }
}

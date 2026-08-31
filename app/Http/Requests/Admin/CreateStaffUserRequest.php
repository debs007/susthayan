<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Every non-Customer role - Customer accounts are self-service via OTP
 * (see Api\Auth\CustomerAuthController) and don't need an admin to create
 * them. This is the only way any of the other 6 roles come into being;
 * there's no self-registration path for staff.
 */
class CreateStaffUserRequest extends FormRequest
{
    private const array FRANCHISE_SCOPED_ROLES = ['Franchise Owner', 'Franchise Staff', 'Pharmacist', 'Delivery Agent'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/', 'unique:users,mobile'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in([
                'Pharmacist', 'Franchise Staff', 'Franchise Owner',
                'Delivery Agent', 'Super Admin', 'Accountant',
            ])],
            'franchise_id' => [
                Rule::requiredIf(fn () => in_array($this->input('role'), self::FRANCHISE_SCOPED_ROLES, true)),
                'nullable', 'integer', 'exists:franchises,id',
            ],
            'two_factor_enabled' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $role = $this->input('role');

            // Central roles shouldn't carry a franchise_id - keeps the
            // "null franchise_id = not tied to one store" convention used
            // everywhere else (franchise.scope middleware, settlement
            // scoping, etc.) actually true rather than just usually true.
            if (in_array($role, ['Super Admin', 'Accountant'], true) && $this->filled('franchise_id')) {
                $validator->errors()->add('franchise_id', 'Super Admin and Accountant are not franchise-scoped - leave this blank.');
            }

            // Only a Super Admin should be able to create another central
            // account - an Accountant minting Super Admins would be an
            // odd privilege-escalation path. Route-level role:Super
            // Admin|Accountant lets either in generally; this narrows
            // just this one action further.
            if (in_array($role, ['Super Admin', 'Accountant'], true) && ! $this->user()->hasRole('Super Admin')) {
                $validator->errors()->add('role', 'Only a Super Admin can create a Super Admin or Accountant account.');
            }
        });
    }
}

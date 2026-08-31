<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Deliberately separate from UpdateStaffUserRequest - a routine profile
 * edit (name, email, active flag) shouldn't accidentally also move
 * someone's role, and a role change has real authorization implications
 * (franchise-scoping, privilege escalation) that a name change doesn't.
 */
class AssignRoleRequest extends FormRequest
{
    private const array FRANCHISE_SCOPED_ROLES = ['Franchise Owner', 'Franchise Staff', 'Pharmacist', 'Delivery Agent'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in([
                'Pharmacist', 'Franchise Staff', 'Franchise Owner',
                'Delivery Agent', 'Super Admin', 'Accountant',
            ])],
            'franchise_id' => [
                Rule::requiredIf(fn () => in_array($this->input('role'), self::FRANCHISE_SCOPED_ROLES, true)),
                'nullable', 'integer', 'exists:franchises,id',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $role = $this->input('role');

            if (in_array($role, ['Super Admin', 'Accountant'], true) && $this->filled('franchise_id')) {
                $validator->errors()->add('franchise_id', 'Super Admin and Accountant are not franchise-scoped - leave this blank.');
            }

            if (in_array($role, ['Super Admin', 'Accountant'], true) && ! $this->user()->hasRole('Super Admin')) {
                $validator->errors()->add('role', 'Only a Super Admin can move someone into a Super Admin or Accountant role.');
            }
        });
    }
}

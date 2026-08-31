<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** General franchise fields - bank details deliberately stay on their own endpoint/request (UpdateFranchiseBankDetailsRequest), not mixed in here. */
class UpdateFranchiseRequest extends FormRequest
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
            'drug_license_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'address' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'pincode' => ['sometimes', 'nullable', 'string', 'max:10'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:15'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'commission_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
        ];
    }
}

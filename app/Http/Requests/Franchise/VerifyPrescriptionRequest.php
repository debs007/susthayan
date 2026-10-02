<?php

namespace App\Http\Requests\Franchise;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route-level role:Pharmacist middleware handles who can reach this
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:500'],
            'medicines' => ['nullable', 'array'],
            'medicines.*' => ['required', 'string', 'max:255'],
        ];
    }
}

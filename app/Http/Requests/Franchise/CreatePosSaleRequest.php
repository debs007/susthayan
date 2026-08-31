<?php

namespace App\Http\Requests\Franchise;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreatePosSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'payment_mode' => ['required', 'in:cash,upi,card'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'walk_in_name' => ['nullable', 'string', 'max:255'],
            'walk_in_phone' => ['nullable', 'string', 'max:15'],
            'prescription_note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $customerId = $this->input('customer_id');
            $isCustomer = $customerId && User::where('id', $customerId)
                ->whereHas('roles', fn ($query) => $query->where('name', 'Customer'))
                ->exists();

            if ($customerId && ! $isCustomer) {
                $validator->errors()->add('customer_id', 'That account is not a customer.');
            }
        });
    }
}

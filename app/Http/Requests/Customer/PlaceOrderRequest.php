<?php

namespace App\Http\Requests\Customer;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // No longer used by placeOrder() - franchise is assigned by an
            // admin after the order is placed, not chosen at checkout.
            // Left nullable rather than removed so the app isn't forced to
            // stop sending it before it's updated.
            'franchise_id' => ['nullable', 'integer', 'exists:franchises,id'],
            'fulfillment_type' => ['required', 'in:delivery,pickup'],
            'address_id' => ['required_if:fulfillment_type,delivery', 'nullable', 'integer'],
        ];
    }

    /** exists:addresses,id alone doesn't check the address is actually this customer's. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $addressId = $this->input('address_id');

            if (! $addressId) {
                return;
            }

            $ownsAddress = Address::where('id', $addressId)->where('user_id', $this->user()->id)->exists();

            if (! $ownsAddress) {
                $validator->errors()->add('address_id', 'That address does not belong to your account.');
            }
        });
    }
}

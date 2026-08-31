<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class InitiateRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_item_ids' => ['required', 'array', 'min:1'],
            'order_item_ids.*' => ['integer', 'exists:order_items,id'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}

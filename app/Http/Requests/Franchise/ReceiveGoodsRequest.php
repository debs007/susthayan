<?php

namespace App\Http\Requests\Franchise;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveGoodsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'received_date' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.batch_no' => ['required', 'string', 'max:100'],
            'items.*.expiry_date' => ['required', 'date', 'after:today'],
            'items.*.received_qty' => ['required', 'integer', 'min:1'],
            'items.*.damaged_qty' => ['nullable', 'integer', 'min:0', 'lte:items.*.received_qty'],
            'items.*.purchase_rate' => ['required', 'numeric', 'min:0'],
            'items.*.mrp' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

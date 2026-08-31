<?php

namespace App\Http\Requests\Franchise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:preparing,ready_for_dispatch,out_for_delivery,delivered,picked_up'],
        ];
    }
}

<?php

namespace App\Http\Requests\Franchise;

use Illuminate\Foundation\Http\FormRequest;

class RecordSupplierPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_mode' => ['required', 'in:bank_transfer,upi,cheque,cash'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}

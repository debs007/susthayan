<?php

namespace App\Http\Requests\Franchise;

use Illuminate\Foundation\Http\FormRequest;

class CaptureSupplierInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'purchase_order_id' => ['nullable', 'integer', 'exists:purchase_orders,id'],
            'goods_receipt_id' => ['nullable', 'integer', 'exists:goods_receipts,id'],
            'invoice_number' => ['required', 'string', 'max:100'],
            'invoice_date' => ['required', 'date', 'before_or_equal:today'],
            'invoice_amount' => ['required', 'numeric', 'min:0'],
            'gst_amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
        ];
    }
}

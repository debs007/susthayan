<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReportDateRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date', 'before_or_equal:to'],
            'to' => ['nullable', 'date', 'before_or_equal:today'],
            'franchise_id' => ['nullable', 'integer', 'exists:franchises,id'],
            'format' => ['nullable', 'in:json,csv'],
        ];
    }

    /** Defaults to "this month so far" when the caller doesn't specify a range - an accounting report should never just error out for an omitted date. */
    public function from(): string
    {
        return $this->validated('from') ?? now()->startOfMonth()->toDateString();
    }

    public function to(): string
    {
        return $this->validated('to') ?? now()->toDateString();
    }
}

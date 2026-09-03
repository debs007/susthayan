<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlockTestDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'after_or_equal:today', 'unique:lab_test_blocked_dates,date,NULL,id,franchise_id,NULL'],
            'reason' => ['nullable', 'string', 'max:150'],
        ];
    }
}

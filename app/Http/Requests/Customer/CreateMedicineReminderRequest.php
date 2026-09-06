<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateMedicineReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medicine_name' => ['required', 'string', 'max:150'],
            'dosage_note' => ['nullable', 'string', 'max:200'],
            'times' => ['required', 'array', 'min:1', 'max:6'],
            'times.*' => ['date_format:H:i'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}

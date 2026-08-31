<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadHealthRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['lab_report', 'medical_document', 'vaccination', 'checkup'])],
            'title' => ['required', 'string', 'max:150'],
            // Optional, deliberately - a checkup or vaccination note might
            // have nothing to attach, unlike a lab report which almost
            // always does. Same mimes/size limit as prescription uploads.
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'record_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

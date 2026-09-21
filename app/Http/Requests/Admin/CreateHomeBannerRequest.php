<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHomeBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => ['required', 'in:mobile,web'],
            // Only web banners get a dimension guide - this is the actual
            // fix for the reported problem (mobile-shaped images looking
            // cropped/stretched on the website's much wider hero slot).
            // min_width nudges toward a suitably wide image without an
            // exact-ratio requirement strict enough to reject an upload
            // that's close but not pixel-perfect to 4:3.
            'image' => [
                'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096',
                Rule::when($this->input('platform') === 'web', ['dimensions:min_width=1000,min_height=750']),
            ],
            'coupon_id' => ['nullable', 'integer', 'exists:coupons,id'],
            'badge_text' => ['nullable', 'string', 'max:20'],
            'headline' => ['nullable', 'string', 'max:60'],
            'subtitle' => ['nullable', 'string', 'max:150'],
            'button_text' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}

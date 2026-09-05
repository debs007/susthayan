<?php

namespace App\Http\Requests\Admin;

use App\Models\HealthArticle;
use Illuminate\Foundation\Http\FormRequest;

class CreateHealthArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'youtube_url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    if (HealthArticle::extractVideoId($value) === null) {
                        $fail('Enter a valid YouTube video URL (youtube.com/watch?v=... or youtu.be/...).');
                    }
                },
            ],
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shared by both the Admin and Franchise product-image upload routes -
 * same file, not duplicated, since the validation rules don't differ by
 * who's uploading. Lives under Requests/Admin for consistency with where
 * CreateProductRequest/UpdateProductRequest already live, even though
 * Franchise also uses it.
 */
class UploadProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role check happens at the route level (role:Super Admin|Franchise Owner), same pattern as the rest of this app
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=200,min_height=200'],
        ];
    }
}

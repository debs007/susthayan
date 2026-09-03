<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    /**
     * Stores the new image on R2 and deletes the old one (if any) so
     * replaced images don't accumulate as orphaned objects in the bucket
     * forever. Returns the updated product.
     */
    public function upload(Product $product, UploadedFile $file): Product
    {
        $oldPath = $product->image_path;

        $path = $file->store('products', 'r2');

        $product->update(['image_path' => $path]);

        if ($oldPath !== null) {
            Storage::disk('r2')->delete($oldPath);
        }

        return $product->fresh();
    }

    public function remove(Product $product): Product
    {
        if ($product->image_path !== null) {
            Storage::disk('r2')->delete($product->image_path);
            $product->update(['image_path' => null]);
        }

        return $product->fresh();
    }
}

<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UploadProductImageRequest;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Deliberately narrow - a Franchise Owner can upload or remove a product's
 * photo, nothing else about the product. Full product management (name,
 * pricing, category, description) stays Admin-only. Since products are a
 * single shared catalog (no franchise_id on the products table), an image
 * uploaded here is visible to every franchise's customers, not just this
 * one - worth being aware of, not just a quiet side effect.
 */
class ProductImageController extends Controller
{
    public function __construct(private readonly ProductImageService $productImages) {}

    public function edit(Product $product): View
    {
        return view('franchise.products.edit-image', compact('product'));
    }

    public function upload(UploadProductImageRequest $request, Product $product): RedirectResponse
    {
        $this->productImages->upload($product, $request->file('image'));

        return back()->with('success', "Photo updated for \"{$product->name}\" - visible to every franchise's customers, not just yours.");
    }

    public function remove(Product $product): RedirectResponse
    {
        $this->productImages->remove($product);

        return back()->with('success', "Photo removed for \"{$product->name}\".");
    }
}

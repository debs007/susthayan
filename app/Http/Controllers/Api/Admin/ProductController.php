<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\SetProductPriceRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use GeneratesUniqueSlugs;

    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'prices'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('barcode', $term));
            })
            ->orderBy('name')
            ->paginate(30);

        return response()->json($products);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = Product::create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug(Product::class, $request->validated('name')),
            'is_active' => true,
        ]);

        return response()->json(['product' => $product], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['product' => $product->load(['category', 'prices.franchise'])]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json(['product' => $product->fresh()]);
    }

    /**
     * Set or replace the price for this product - global (no franchise_id)
     * or franchise-specific. One row per (product, franchise) pair, so
     * this is an upsert: calling it again for the same franchise updates
     * the existing price rather than creating a second one (which the
     * unique index on product_prices would reject anyway).
     */
    public function setPrice(SetProductPriceRequest $request, Product $product): JsonResponse
    {
        $price = ProductPrice::updateOrCreate(
            ['product_id' => $product->id, 'franchise_id' => $request->validated('franchise_id')],
            [
                'mrp' => $request->validated('mrp'),
                'selling_price' => $request->validated('selling_price'),
                'tax_percentage' => $request->validated('tax_percentage'),
                'effective_from' => $request->validated('effective_from'),
            ]
        );

        return response()->json(['price' => $price], 201);
    }
}

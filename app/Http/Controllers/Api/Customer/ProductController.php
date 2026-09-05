<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Catalog\ProductPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(private readonly ProductPricingService $pricing) {}

    /**
     * Search/browse (SRS 3.1: name, salt, brand + category). ?franchise_id
     * resolves price/stock for that store; omit it for a price-only,
     * stock-blind catalog browse (e.g. before a store is chosen).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $franchiseId = $request->integer('franchise_id') ?: null;

        $products = Product::query()
            ->where('is_active', true)
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('salt_composition', 'like', $term)
                        ->orWhere('manufacturer', 'like', $term);
                });
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->query('sort') === 'latest', fn ($query) => $query->latest('id'), fn ($query) => $query->orderBy('name'))
            ->paginate($request->integer('per_page') ?: 20);

        $this->pricing->attach($products->getCollection(), $franchiseId);

        return ProductResource::collection($products);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        $product->load('category');
        $this->pricing->attach(collect([$product]), $request->integer('franchise_id') ?: null);

        return new ProductResource($product);
    }
}

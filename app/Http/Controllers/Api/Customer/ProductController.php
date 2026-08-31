<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
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
            ->orderBy('name')
            ->paginate(20);

        $this->attachPriceAndStock($products->getCollection(), $franchiseId);

        return ProductResource::collection($products);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        $product->load('category');
        $this->attachPriceAndStock(collect([$product]), $request->integer('franchise_id') ?: null);

        return new ProductResource($product);
    }

    /**
     * Batch-resolves price + available stock for a page of products in two
     * queries total, rather than two queries per product.
     */
    private function attachPriceAndStock(iterable $products, ?int $franchiseId): void
    {
        $productIds = collect($products)->pluck('id');

        $prices = ProductPrice::whereIn('product_id', $productIds)
            ->where(fn ($query) => $query->where('franchise_id', $franchiseId)->orWhereNull('franchise_id'))
            ->get()
            ->groupBy('product_id');

        $stock = $franchiseId
            ? Inventory::whereIn('product_id', $productIds)
                ->where('franchise_id', $franchiseId)
                ->selectRaw('product_id, SUM(quantity - reserved_quantity) as available')
                ->groupBy('product_id')
                ->pluck('available', 'product_id')
            : collect();

        foreach ($products as $product) {
            $productPrices = $prices->get($product->id, collect());
            $product->resolved_price = $productPrices->firstWhere('franchise_id', $franchiseId)
                ?? $productPrices->firstWhere('franchise_id', null);
            $product->resolved_stock = $franchiseId ? (int) ($stock[$product->id] ?? 0) : null;
        }
    }
}

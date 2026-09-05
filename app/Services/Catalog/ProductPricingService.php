<?php

namespace App\Services\Catalog;

use App\Models\Inventory;
use App\Models\ProductPrice;

class ProductPricingService
{
    /**
     * Sets ->resolved_price and ->resolved_stock on each product -
     * ProductResource reads these two dynamic properties directly (see
     * its own docblock), not any other pricing mechanism. Batches the
     * price/stock queries once for the whole set rather than once per
     * product, which matters for a listing but is equally correct for a
     * smaller set like a coupon's product list.
     */
    public function attach(iterable $products, ?int $franchiseId): void
    {
        $productIds = collect($products)->pluck('id');

        $prices = ProductPrice::whereIn('product_id', $productIds)
            ->where(fn ($query) => $query->where('franchise_id', $franchiseId)->orWhereNull('franchise_id'))
            ->get()
            ->groupBy('product_id');

        // No specific franchise yet (browsing, before a store is chosen)
        // still sums real stock across every franchise, rather than
        // returning null unconditionally - "in stock" during browsing
        // means "available somewhere", not "available at a store nobody
        // has picked yet". A specific franchise_id (cart, checkout) still
        // narrows this to that store's own number, unchanged.
        $stock = Inventory::whereIn('product_id', $productIds)
            ->when($franchiseId, fn ($query) => $query->where('franchise_id', $franchiseId))
            ->selectRaw('product_id, SUM(quantity - reserved_quantity) as available')
            ->groupBy('product_id')
            ->pluck('available', 'product_id');

        foreach ($products as $product) {
            $productPrices = $prices->get($product->id, collect());
            $product->resolved_price = $productPrices->firstWhere('franchise_id', $franchiseId)
                ?? $productPrices->firstWhere('franchise_id', null);
            $product->resolved_stock = (int) ($stock[$product->id] ?? 0);
        }
    }
}

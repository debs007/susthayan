<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductView;
use App\Services\Catalog\ProductPricingService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product, ProductPricingService $pricing): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'brand']);
        $pricing->attach(collect([$product]), null);

        // Scoped to logged-in users only for now - anonymous/guest browsing
        // history would need session-based tracking as a separate piece of
        // work, not something to fold in silently here.
        if (auth('web')->check()) {
            ProductView::updateOrCreate(
                ['user_id' => auth('web')->id(), 'product_id' => $product->id],
                ['viewed_at' => now()]
            );
        }

        $related = Product::query()
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(12)
            ->get();
        $pricing->attach($related, null);

        return view('storefront.products.show', compact('product', 'related'));
    }
}

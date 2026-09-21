<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use App\Models\Product;
use App\Models\ProductView;
use App\Services\Catalog\ProductPricingService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly ProductPricingService $pricing) {}

    public function __invoke(): View
    {
        $banners = HomeBanner::where('is_active', true)->where('platform', 'web')->orderBy('sort_order')->get();

        $products = Product::query()
            ->where('is_active', true)
            ->with('category')
            ->latest('id')
            ->limit(8)
            ->get();

        // No franchise selected yet for an anonymous/just-browsing web
        // visitor - null sums real stock across every franchise, same
        // "browsing before a store is chosen" behavior the customer app
        // gets on its own Home screen.
        $this->pricing->attach($products, null);

        [$recentlyViewed, $similarProducts] = $this->personalizedSections();

        return view('storefront.home', compact('banners', 'products', 'recentlyViewed', 'similarProducts'));
    }

    /**
     * Both empty for guests/never-viewed-anything users - there's no
     * history to build either section from, and the view hides them
     * entirely rather than showing an empty shelf.
     */
    private function personalizedSections(): array
    {
        if (! auth('web')->check()) {
            return [new Collection(), new Collection()];
        }

        $recentlyViewed = ProductView::where('user_id', auth('web')->id())
            ->where('viewed_at', '>=', now()->subDays(60))
            ->with('product.category')
            ->latest('viewed_at')
            ->limit(10)
            ->get()
            ->pluck('product')
            ->filter(fn ($p) => $p && $p->is_active)
            ->values();

        $this->pricing->attach($recentlyViewed, null);

        // "Similar to what you've looked at" - the categories behind
        // recently viewed products, excluding anything already viewed,
        // rather than genuine collaborative-filtering recommendations.
        $categoryIds = $recentlyViewed->pluck('category_id')->filter()->unique();

        $similarProducts = new Collection();
        if ($categoryIds->isNotEmpty()) {
            $similarProducts = Product::where('is_active', true)
                ->whereIn('category_id', $categoryIds)
                ->whereNotIn('id', $recentlyViewed->pluck('id'))
                ->latest('id')
                ->limit(10)
                ->get();
            $this->pricing->attach($similarProducts, null);
        }

        return [$recentlyViewed, $similarProducts];
    }
}

<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\Catalog\ProductPricingService;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        // isCurrentlyValid() isn't a query scope, so the active-window
        // check happens in memory after fetching is_global/is_active
        // rows - same trade-off the API's own coupon listing accepts,
        // and the coupon count here is small enough that it's fine.
        $coupons = Coupon::where('is_global', true)
            ->where('is_active', true)
            ->get()
            ->filter(fn ($c) => $c->isCurrentlyValid());

        return view('storefront.offers.index', compact('coupons'));
    }

    public function products(Coupon $coupon, ProductPricingService $pricing): View
    {
        abort_unless($coupon->isCurrentlyValid(), 404);

        $products = $coupon->products()->where('is_active', true)->with(['category', 'brand'])->get();
        $pricing->attach($products, null);

        return view('storefront.offers.products', compact('coupon', 'products'));
    }
}

<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Coupon;
use App\Services\Catalog\ProductPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(private readonly ProductPricingService $pricing) {}

    /** What the app's Offers/Coupons page lists - only coupons the admin explicitly marked global, filtered to ones actually valid right now. */
    public function index(): JsonResponse
    {
        $coupons = Coupon::where('is_global', true)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', now()))
            ->where(fn ($q) => $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', now()))
            ->withCount('products')
            ->orderByDesc('id')
            ->get();

        $data = $coupons->map(fn ($coupon) => [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'description' => $coupon->description,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (string) $coupon->discount_value,
            'product_count' => $coupon->products_count,
        ]);

        return response()->json(['data' => $data]);
    }

    /**
     * The endpoint a coupon-linked banner tap actually calls. Same
     * resolved_price/resolved_stock batch-attach ProductController uses,
     * so ProductResource's output is identical in shape either way - this
     * just adds one extra field (coupon_price) on top.
     */
    public function products(Request $request, Coupon $coupon): JsonResponse
    {
        abort_unless($coupon->isCurrentlyValid(), 404);

        $franchiseId = $request->integer('franchise_id') ?: null;
        $products = $coupon->products()->with('category')->where('is_active', true)->get();

        $this->pricing->attach($products, $franchiseId);

        $data = $products->map(function ($product) use ($coupon) {
            $resource = (new ProductResource($product))->resolve();
            $sellingPrice = $product->resolved_price?->selling_price;
            $resource['coupon_price'] = $sellingPrice !== null
                ? number_format(max(0, (float) $sellingPrice - $coupon->calculateDiscount((float) $sellingPrice)), 2, '.', '')
                : null;

            return $resource;
        });

        return response()->json([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'description' => $coupon->description,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (string) $coupon->discount_value,
            ],
            'data' => $data,
        ]);
    }
}

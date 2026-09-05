<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateCouponRequest;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::withCount('products')->orderByDesc('id')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();

        return view('admin.coupons.create', compact('products'));
    }

    public function store(CreateCouponRequest $request): RedirectResponse
    {
        $coupon = Coupon::create([
            ...$request->safe()->except(['product_ids', 'is_global']),
            'code' => strtoupper($request->validated('code')),
            'is_global' => $request->boolean('is_global'),
            'is_active' => true,
        ]);

        $coupon->products()->sync($request->validated('product_ids'));

        return redirect()->route('admin.coupons.edit', $coupon)->with('success', "Coupon \"{$coupon->code}\" was added.");
    }

    public function edit(Coupon $coupon): View
    {
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $coupon->load('products');

        return view('admin.coupons.edit', compact('coupon', 'products'));
    }

    public function update(CreateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update([
            ...$request->safe()->except(['product_ids', 'is_global']),
            'code' => strtoupper($request->validated('code')),
            'is_global' => $request->boolean('is_global'),
        ]);

        $coupon->products()->sync($request->validated('product_ids'));

        return redirect()->route('admin.coupons.edit', $coupon)->with('success', 'Saved.');
    }

    public function toggleActive(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back()->with('success', $coupon->is_active ? "\"{$coupon->code}\" is now active." : "\"{$coupon->code}\" is now disabled.");
    }
}

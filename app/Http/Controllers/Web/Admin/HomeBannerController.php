<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateHomeBannerRequest;
use App\Models\Coupon;
use App\Models\HomeBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeBannerController extends Controller
{
    public function index(): View
    {
        $banners = HomeBanner::with('coupon')->orderBy('sort_order')->orderBy('id')->get();
        $coupons = Coupon::where('is_active', true)->orderBy('code')->get();

        return view('admin.home-banners.index', compact('banners', 'coupons'));
    }

    public function store(CreateHomeBannerRequest $request): RedirectResponse
    {
        $banner = HomeBanner::create([
            ...$request->safe()->except('image'),
            'image_path' => $request->file('image')->store('home-banners', 'r2'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.home-banners.index')->with('success', 'Banner added.');
    }

    public function toggleActive(HomeBanner $homeBanner): RedirectResponse
    {
        $homeBanner->update(['is_active' => ! $homeBanner->is_active]);

        return back()->with('success', $homeBanner->is_active ? 'Banner is now live in the app.' : 'Banner is now hidden from the app.');
    }

    public function destroy(HomeBanner $homeBanner): RedirectResponse
    {
        Storage::disk('r2')->delete($homeBanner->image_path);
        $homeBanner->delete();

        return back()->with('success', 'Banner removed.');
    }
}

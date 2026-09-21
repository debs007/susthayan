<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\Catalog\ProductPricingService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(ProductPricingService $pricing): View
    {
        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        // A genuine spread across categories, not just "latest N overall" -
        // that would risk showing only whatever category was most recently
        // restocked, rather than actually representing "all categories".
        $sampleProducts = new Collection();
        foreach ($categories as $category) {
            $sampleProducts = $sampleProducts->concat(
                Product::where('category_id', $category->id)->where('is_active', true)->latest('id')->limit(3)->get()
            );
        }
        $pricing->attach($sampleProducts, null);

        return view('storefront.categories', compact('categories', 'brands', 'sampleProducts'));
    }
}

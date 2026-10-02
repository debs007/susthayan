<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\SetProductPriceRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\UploadProductImageRequest;
use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Franchise;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\ProductImageService;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    use GeneratesUniqueSlugs;

    public function __construct(private readonly ProductImageService $productImages) {}

    public function index(Request $request): View
    {
        $products = $this->filteredQuery($request)
            ->with(['category', 'prices'])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /** Shared with destroyAll() below, so "delete all" always means exactly "all of what the list currently shows" - never a separately-maintained, potentially inconsistent copy of this same filter. */
    private function filteredQuery(Request $request): Builder
    {
        return Product::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim($request->string('q')).'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('barcode', $term));
            });
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands = Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(CreateProductRequest $request): RedirectResponse
    {
        $product = Product::create([
            ...$request->safe()->except('image'),
            'slug' => $this->uniqueSlug(Product::class, $request->validated('name')),
            'is_active' => true,
        ]);

        if ($request->hasFile('image')) {
            $this->productImages->upload($product, $request->file('image'));
        }

        return redirect()->route('admin.products.edit', $product)->with('success', "\"{$product->name}\" was created - add a price below to make it sellable.");
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $brands = Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $franchises = Franchise::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $product->load(['prices.franchise']);

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'franchises'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product details updated.');
    }

    /** Same upsert semantics as the API version - calling this again for the same franchise replaces that price rather than duplicating it. */
    public function storePrice(SetProductPriceRequest $request, Product $product): RedirectResponse
    {
        ProductPrice::updateOrCreate(
            ['product_id' => $product->id, 'franchise_id' => $request->validated('franchise_id')],
            [
                'mrp' => $request->validated('mrp'),
                'selling_price' => $request->validated('selling_price'),
                'tax_percentage' => $request->validated('tax_percentage'),
                'effective_from' => $request->validated('effective_from'),
            ]
        );

        return redirect()->route('admin.products.edit', $product)->with('success', 'Price saved.');
    }

    public function uploadImage(UploadProductImageRequest $request, Product $product): RedirectResponse
    {
        $this->productImages->upload($product, $request->file('image'));

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product image updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($this->hasIrreplaceableHistory($product)) {
            // A real row deletion would fail outright here anyway -
            // order_items/purchase_order_items/goods_receipt_items are
            // all restrictOnDelete(). Deactivating is the honest fallback
            // when the product genuinely can't be removed without
            // destroying real order or purchasing history.
            $product->update(['is_active' => false]);

            return back()->with('success', "\"{$product->name}\" has order or purchasing history, so it can't be fully deleted - it's been removed from the storefront instead.");
        }

        // Cart entries and stock records are current/transient state tied
        // to this product's continued existence, not historical records
        // worth preserving - safe to clear before the delete itself.
        // product_prices, coupon_product, and product_views all cascade
        // automatically.
        $product->cartItems()->delete();
        $product->inventory()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "\"{$product->name}\" was permanently deleted.");
    }

    /** True only for genuine, irreplaceable business history - a real customer order, franchise purchase order, or goods receipt. Cart entries and stock levels don't count here; see destroy() above. */
    private function hasIrreplaceableHistory(Product $product): bool
    {
        return $product->orderItems()->exists()
            || $product->purchaseOrderItems()->exists()
            || $product->goodsReceiptItems()->exists();
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $baseQuery = $this->filteredQuery($request);

        $withHistoryIds = (clone $baseQuery)
            ->where(fn ($q) => $q->whereHas('orderItems')->orWhereHas('purchaseOrderItems')->orWhereHas('goodsReceiptItems'))
            ->pluck('id');

        $deletableIds = (clone $baseQuery)->whereNotIn('id', $withHistoryIds)->pluck('id');

        // Same as the single-product path - transient state cleared
        // before the delete itself, not treated as a reason to preserve
        // the product. product_prices/coupon_product/product_views all
        // cascade automatically.
        CartItem::whereIn('product_id', $deletableIds)->delete();
        Inventory::whereIn('product_id', $deletableIds)->delete();
        $deletedCount = Product::whereIn('id', $deletableIds)->delete();

        $deactivatedCount = Product::whereIn('id', $withHistoryIds)->where('is_active', true)->update(['is_active' => false]);

        $message = $deletedCount.' product'.($deletedCount === 1 ? '' : 's').' permanently deleted';
        if ($deactivatedCount > 0) {
            $message .= ", {$deactivatedCount} with order or purchasing history removed from the storefront instead";
        }

        return redirect()->route('admin.products.index')->with('success', $message.'.');
    }

    public function restore(Product $product): RedirectResponse
    {
        $product->update(['is_active' => true]);

        return back()->with('success', "\"{$product->name}\" restored to the storefront.");
    }

    public function removeImage(Product $product): RedirectResponse
    {
        $this->productImages->remove($product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product image removed.');
    }
}

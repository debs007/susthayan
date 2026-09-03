<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\SetProductPriceRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Requests\Admin\UploadProductImageRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Franchise;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\ProductImageService;
use App\Traits\GeneratesUniqueSlugs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    use GeneratesUniqueSlugs;

    public function __construct(private readonly ProductImageService $productImages) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'prices'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim($request->string('q')).'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('barcode', $term));
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
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

    public function removeImage(Product $product): RedirectResponse
    {
        $this->productImages->remove($product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product image removed.');
    }
}

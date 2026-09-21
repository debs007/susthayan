<?php

namespace App\Livewire\Storefront;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\Catalog\ProductPricingService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Deliberately does NOT filter/sort by price or stock. Both are resolved
 * per-franchise from a separate table (ProductPrice) and inventory rows,
 * not real columns on `products` - filtering/sorting after pagination
 * would only operate within each page's 24 items, silently breaking
 * page counts and cross-page ordering. Getting that right needs a
 * proper join into the query itself, which is follow-up work, not
 * something to fake here.
 */
class ProductBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?int $category_id = null;

    #[Url(history: true)]
    public ?int $brand_id = null;

    #[Url(history: true)]
    public bool $prescription_only = false;

    #[Url(history: true)]
    public string $sort = 'latest';

    /** Livewire re-uses the component instance across re-renders - without resetting to page 1, changing a filter while on page 3 of the old results would show an empty or mismatched page 3 of the new ones. */
    public function updated($property): void
    {
        if ($property !== 'sort') {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['category_id', 'brand_id', 'prescription_only']);
        $this->resetPage();
    }

    public function render(ProductPricingService $pricing)
    {
        $query = Product::query()->where('is_active', true)->with(['category', 'brand']);

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('salt_composition', 'like', $term));
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->brand_id) {
            $query->where('brand_id', $this->brand_id);
        }

        if ($this->prescription_only) {
            $query->where('prescription_required', true);
        }

        $query->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sort === 'latest', fn ($q) => $q->latest('id'));

        $products = $query->paginate(24);

        // Display only - price/stock aren't filtered/sorted on (see class docblock).
        $pricing->attach($products->getCollection(), null);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('livewire.storefront.product-browser', compact('products', 'categories', 'brands'))
            ->layout('components.layouts.storefront', ['title' => 'Shop - Susthayan']);
    }
}

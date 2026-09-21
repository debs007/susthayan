<x-layouts.storefront title="Shop by Category - Susthayan">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <h1 class="font-display text-2xl font-medium text-ink">Shop by category</h1>

        @if ($categories->isEmpty())
            <p class="mt-6 text-sm text-ink-soft">No categories yet.</p>
        @else
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
                @foreach ($categories as $category)
                    <a href="{{ route('storefront.products.index', ['category_id' => $category->id]) }}" wire:navigate class="flex flex-col items-center justify-center gap-2 rounded-2xl border border-line bg-surface p-5 text-center hover:border-brand">
                        <span class="text-sm font-medium text-ink">{{ $category->name }}</span>
                        <span class="text-xs text-ink-faint">{{ $category->products_count }} items</span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($sampleProducts->isNotEmpty())
            <section class="mt-14">
                <h2 class="font-display text-xl font-medium text-ink">Popular across categories</h2>
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($sampleProducts as $product)
                        <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($brands->isNotEmpty())
            <section class="mt-14">
                <h2 class="font-display text-xl font-medium text-ink">Top brands</h2>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($brands as $brand)
                        <a href="{{ route('storefront.products.index', ['brand_id' => $brand->id]) }}" wire:navigate class="flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-2 hover:border-brand">
                            @if ($brand->logo_url)
                                <img src="{{ $brand->logo_url }}" alt="" class="h-5 w-5 object-contain">
                            @endif
                            <span class="text-sm font-medium text-ink">{{ $brand->name }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.storefront>

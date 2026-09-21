<div class="mx-auto max-w-7xl px-6 py-10">
    <div class="mb-6 flex items-center gap-3">
        <div class="flex flex-1 items-center gap-2 rounded-full border border-line bg-surface px-4 py-2.5">
            <svg class="h-4 w-4 shrink-0 text-ink-faint" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.6" /><path d="M14 14L17.5 17.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search medicines, salts, brands..." class="w-full bg-transparent text-sm outline-none">
        </div>
        <select wire:model.live="sort" class="rounded-full border border-line bg-surface px-4 py-2.5 text-sm outline-none">
            <option value="latest">Newest first</option>
            <option value="name">Name (A-Z)</option>
        </select>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        {{-- Sidebar filters --}}
        <aside class="space-y-6 lg:col-span-1">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink">Filters</p>
                    <button wire:click="clearFilters" class="text-xs text-brand hover:underline">Clear all</button>
                </div>
            </div>

            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-faint">Category</p>
                <div class="space-y-1.5">
                    <label class="flex items-center gap-2 text-sm text-ink-soft">
                        <input type="radio" wire:model.live="category_id" value="" class="accent-brand"> All categories
                    </label>
                    @foreach ($categories as $category)
                        <label class="flex items-center gap-2 text-sm text-ink-soft">
                            <input type="radio" wire:model.live="category_id" value="{{ $category->id }}" class="accent-brand"> {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-faint">Brand</p>
                <div class="max-h-48 space-y-1.5 overflow-y-auto">
                    <label class="flex items-center gap-2 text-sm text-ink-soft">
                        <input type="radio" wire:model.live="brand_id" value="" class="accent-brand"> All brands
                    </label>
                    @foreach ($brands as $brand)
                        <label class="flex items-center gap-2 text-sm text-ink-soft">
                            <input type="radio" wire:model.live="brand_id" value="{{ $brand->id }}" class="accent-brand"> {{ $brand->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-soft">
                <input type="checkbox" wire:model.live="prescription_only" class="rounded accent-brand"> Prescription required
            </label>
        </aside>

        {{-- Results --}}
        <div class="lg:col-span-3">
            <p class="mb-4 text-sm text-ink-soft">{{ $products->total() }} products found</p>

            <div wire:loading.class="opacity-50" wire:target="search,category_id,brand_id,prescription_only,sort" class="grid grid-cols-2 gap-4 sm:grid-cols-3 transition-opacity">
                @forelse ($products as $product)
                    <x-storefront.product-card :product="$product" />
                @empty
                    <p class="col-span-full py-10 text-center text-sm text-ink-soft">No products match these filters.</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

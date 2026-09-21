<x-layouts.storefront :title="$product->name.' - Susthayan'">
    <div class="mx-auto max-w-5xl px-6 py-10">
        <nav class="mb-6 text-xs text-ink-faint">
            <a href="{{ route('storefront.categories') }}" wire:navigate class="hover:text-brand">Shop</a>
            @if ($product->category)
                <span class="mx-1.5">/</span>
                <a href="{{ route('storefront.products.index', ['category_id' => $product->category_id]) }}" wire:navigate class="hover:text-brand">{{ $product->category->name }}</a>
            @endif
            <span class="mx-1.5">/</span>
            <span class="text-ink-soft">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
            <div class="aspect-square rounded-2xl bg-mist/40 p-8">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                @else
                    <div class="flex h-full items-center justify-center text-ink-faint">No image</div>
                @endif
            </div>

            <div>
                @if ($product->brand)
                    <p class="text-sm font-medium text-brand">{{ $product->brand->name }}</p>
                @endif
                <h1 class="mt-1 font-display text-2xl font-medium text-ink">{{ $product->name }}</h1>
                @if ($product->unit)
                    <p class="mt-1 text-sm text-ink-soft">{{ $product->unit }}</p>
                @endif
                @if ($product->salt_composition)
                    <p class="mt-1 text-xs text-ink-faint">{{ $product->salt_composition }}</p>
                @endif

                @if ($product->prescription_required)
                    <span class="mt-3 inline-block w-fit rounded-full bg-warning/10 px-3 py-1 text-xs font-medium text-warning">
                        Prescription required at checkout
                    </span>
                @endif

                <div class="mt-6 flex items-baseline gap-3">
                    @if ($product->resolved_price)
                        <span class="font-display text-3xl font-semibold text-ink">₹{{ $product->resolved_price->selling_price }}</span>
                        @if ((float) $product->resolved_price->mrp > (float) $product->resolved_price->selling_price)
                            <span class="text-ink-faint line-through">₹{{ $product->resolved_price->mrp }}</span>
                        @endif
                    @else
                        <span class="text-ink-faint">Price unavailable</span>
                    @endif
                </div>

                @if (($product->resolved_stock ?? 0) <= 0)
                    <p class="mt-2 text-sm font-medium text-danger">Currently out of stock</p>
                @endif

                <div class="mt-6 max-w-xs">
                    @livewire('storefront.add-to-cart', ['productId' => $product->id, 'inStock' => ($product->resolved_stock ?? 0) > 0], key('add-to-cart-detail-'.$product->id))
                </div>

                @if ($product->manufacturer)
                    <p class="mt-6 text-xs text-ink-faint">Manufactured by {{ $product->manufacturer }}</p>
                @endif

                @if ($product->description)
                    <div class="mt-8 border-t border-line pt-6">
                        <p class="text-sm font-semibold text-ink">Product description</p>
                        <p class="mt-2 whitespace-pre-line text-sm text-ink-soft">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-16">
                <h2 class="font-display text-xl font-medium text-ink">You may also need</h2>
                <div class="mt-5 grid grid-cols-4 gap-3 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10">
                    @foreach ($related as $item)
                        <x-storefront.product-thumbnail :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.storefront>

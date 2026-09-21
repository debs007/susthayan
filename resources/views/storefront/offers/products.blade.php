<x-layouts.storefront :title="$coupon->code.' Offer - Susthayan'">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <a href="{{ route('storefront.offers') }}" wire:navigate class="text-sm text-ink-soft hover:text-brand">&larr; All offers</a>

        <div class="mt-4 flex items-center gap-3">
            <span class="font-code rounded-full bg-mist px-3 py-1 text-sm font-semibold text-forest">{{ $coupon->code }}</span>
            <h1 class="font-display text-2xl font-medium text-ink">
                {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'% off' : '₹'.$coupon->discount_value.' off' }}
            </h1>
        </div>
        @if ($coupon->description)
            <p class="mt-2 text-sm text-ink-soft">{{ $coupon->description }}</p>
        @endif

        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @forelse ($products as $product)
                @php
                    $price = $product->resolved_price;
                    $discounted = $price ? max(0, (float) $price->selling_price - $coupon->calculateDiscount((float) $price->selling_price)) : null;
                @endphp
                <a href="{{ route('storefront.products.show', $product) }}" wire:navigate class="flex flex-col overflow-hidden rounded-2xl border border-line bg-surface hover:shadow-md">
                    <div class="aspect-square bg-mist/40 p-4">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="line-clamp-2 text-sm font-medium text-ink">{{ $product->name }}</p>
                        @if ($price)
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="font-semibold text-brand">₹{{ number_format($discounted, 2) }}</span>
                                <span class="text-xs text-ink-faint line-through">₹{{ $price->selling_price }}</span>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <p class="col-span-full text-sm text-ink-soft">No products are currently linked to this offer.</p>
            @endforelse
        </div>
    </div>
</x-layouts.storefront>

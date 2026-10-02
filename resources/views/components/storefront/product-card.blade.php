@props(['product'])

@php
    $price = $product->resolved_price ?? null;
    $hasDiscount = $price && (float) $price->mrp > (float) $price->selling_price;
    // Every catalogued product is always purchasable - a franchise's stock
    // (or lack of it) is a fulfillment-side concern handled after the
    // order is placed, not a reason to block the customer from ordering
    // it.
    $inStock = true;
@endphp

<div class="group flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-surface transition-shadow hover:shadow-md">
    {{-- Matches product-thumbnail.blade.php's proven structure - the
         aspect-square box is a div nested inside the link, not the link
         itself. Every product image renders into the same square box
         regardless of its own natural dimensions - a tall, narrow
         bottle photo and a wide, short box photo both end up the same
         size here, scaled to fit without cropping. --}}
    <a href="{{ route('storefront.products.show', $product) }}" wire:navigate class="block">
        <div class="aspect-square w-full shrink-0 bg-surface p-4">
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain" loading="lazy">
            @else
                <div class="flex h-full items-center justify-center text-ink-faint text-sm">No image</div>
            @endif
        </div>
    </a>
    <div class="flex flex-1 flex-col gap-1 p-4">
        <div class="h-5">
            @if ($product->prescription_required)
                <span class="w-fit rounded-full bg-warning/10 px-2 py-0.5 text-[11px] font-medium text-warning">Rx required</span>
            @endif
        </div>
        {{-- Reserves space for 2 lines whether the name actually wraps
             that far or not, so a short name and a long one don't leave
             the price sitting at different heights. --}}
        <a href="{{ route('storefront.products.show', $product) }}" wire:navigate class="line-clamp-2 min-h-[2.5rem] text-sm font-medium text-ink hover:text-brand">
            {{ $product->name }}
        </a>
        <p class="h-4 text-xs text-ink-faint">{{ $product->unit }}</p>
        <div class="mt-auto flex items-baseline gap-2 pt-2">
            @if ($price)
                <span class="font-semibold text-ink">₹{{ $price->selling_price }}</span>
                @if ($hasDiscount)
                    <span class="text-xs text-ink-faint line-through">₹{{ $price->mrp }}</span>
                @endif
            @else
                <span class="text-xs text-ink-faint">Price unavailable</span>
            @endif
        </div>
        <div class="pt-2">
            @livewire('storefront.add-to-cart', ['productId' => $product->id, 'inStock' => $inStock], key('add-to-cart-'.$product->id))
        </div>
    </div>
</div>

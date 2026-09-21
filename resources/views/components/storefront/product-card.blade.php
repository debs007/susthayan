@props(['product'])

@php
    $price = $product->resolved_price ?? null;
    $hasDiscount = $price && (float) $price->mrp > (float) $price->selling_price;
    $inStock = ($product->resolved_stock ?? 0) > 0;
@endphp

<div class="group flex flex-col overflow-hidden rounded-2xl border border-line bg-surface transition-shadow hover:shadow-md">
    <a href="{{ route('storefront.products.show', $product) }}" wire:navigate class="block aspect-square bg-mist/40 p-4">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center text-ink-faint text-sm">No image</div>
        @endif
    </a>
    <div class="flex flex-1 flex-col gap-1 p-4">
        @if ($product->prescription_required)
            <span class="w-fit rounded-full bg-warning/10 px-2 py-0.5 text-[11px] font-medium text-warning">Rx required</span>
        @endif
        <a href="{{ route('storefront.products.show', $product) }}" wire:navigate class="line-clamp-2 text-sm font-medium text-ink hover:text-brand">
            {{ $product->name }}
        </a>
        @if ($product->unit)
            <p class="text-xs text-ink-faint">{{ $product->unit }}</p>
        @endif
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

@props(['product'])

@php
    $price = $product->resolved_price ?? null;
@endphp

<a href="{{ route('storefront.products.show', $product) }}" wire:navigate class="group block w-full max-w-[130px] overflow-hidden rounded-xl border border-line bg-surface hover:border-brand">
    <div class="aspect-square w-full bg-mist/40 p-2">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center text-[10px] text-ink-faint">No image</div>
        @endif
    </div>
    <div class="p-2">
        <p class="line-clamp-2 text-[11px] font-medium leading-snug text-ink group-hover:text-brand">{{ $product->name }}</p>
        @if ($price)
            <p class="mt-0.5 text-xs font-semibold text-ink">₹{{ $price->selling_price }}</p>
        @endif
    </div>
</a>

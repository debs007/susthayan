<x-layouts.storefront title="Offers - Susthayan">
    <div class="mx-auto max-w-5xl px-6 py-10">
        <h1 class="font-display text-2xl font-medium text-ink">Offers</h1>

        @if ($coupons->isEmpty())
            <p class="mt-6 text-sm text-ink-soft">No active offers right now - check back soon.</p>
        @else
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($coupons as $coupon)
                    <a href="{{ route('storefront.offers.products', $coupon) }}" wire:navigate class="rounded-2xl border border-line bg-surface p-5 hover:border-brand">
                        <span class="font-code inline-block rounded-full bg-mist px-3 py-1 text-sm font-semibold text-forest">{{ $coupon->code }}</span>
                        <p class="mt-3 text-lg font-medium text-ink">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'% off' : '₹'.$coupon->discount_value.' off' }}
                        </p>
                        @if ($coupon->description)
                            <p class="mt-1 text-sm text-ink-soft">{{ $coupon->description }}</p>
                        @endif
                        @if ($coupon->valid_until)
                            <p class="mt-2 text-xs text-ink-faint">Valid until {{ $coupon->valid_until->format('d M Y') }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.storefront>

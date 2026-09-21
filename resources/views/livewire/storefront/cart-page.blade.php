<div class="mx-auto max-w-4xl px-6 py-10">
    <h1 class="font-display text-2xl font-medium text-ink">Your Cart</h1>

    @if (! $cart || $cart->items->isEmpty())
        <div class="mx-auto flex max-w-md flex-col items-center px-6 py-24 text-center">
            <p class="font-display text-xl font-medium text-ink">Your cart is empty</p>
            <p class="mt-2 text-sm text-ink-soft">Add some medicines or health products to get started.</p>
            <a href="{{ route('storefront.categories') }}" wire:navigate class="mt-6 rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover">Start shopping</a>
        </div>
    @else
        @php
            $lines = $cart->items->map(function ($item) use ($cart) {
                $price = $item->product->priceFor($cart->franchise_id);
                return (object) [
                    'item' => $item,
                    'unitPrice' => $price?->selling_price,
                    'lineTotal' => $price ? round($price->selling_price * $item->quantity, 2) : null,
                ];
            });
            $subtotal = $lines->sum(fn ($l) => (float) ($l->lineTotal ?? 0));
            $discount = $cart->couponDiscountAmount();
        @endphp

        @if ($cart->requiresPrescription())
            <p class="mt-4 rounded-lg bg-warning/10 px-4 py-3 text-sm text-warning">
                One or more items need a prescription - you'll be asked to upload one at checkout.
            </p>
        @endif

        <div class="mt-6 divide-y divide-line rounded-2xl border border-line bg-surface">
            @foreach ($lines as $line)
                <div class="flex items-center gap-4 p-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-ink">{{ $line->item->product->name }}</p>
                        @if ($line->item->product->prescription_required)
                            <p class="text-xs text-warning">Prescription required</p>
                        @endif
                        <p class="mt-1 text-sm text-ink-soft">
                            {{ $line->unitPrice ? '₹'.$line->unitPrice.' each' : 'Price unavailable' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="updateQuantity({{ $line->item->id }}, {{ $line->item->quantity - 1 }})" class="h-7 w-7 rounded-full border border-line text-ink-soft hover:border-brand" aria-label="Decrease quantity">&minus;</button>
                        <span class="w-6 text-center text-sm">{{ $line->item->quantity }}</span>
                        <button wire:click="updateQuantity({{ $line->item->id }}, {{ $line->item->quantity + 1 }})" class="h-7 w-7 rounded-full border border-line text-ink-soft hover:border-brand" aria-label="Increase quantity">+</button>
                    </div>

                    <p class="w-20 text-right text-sm font-semibold text-ink">
                        {{ $line->lineTotal ? '₹'.$line->lineTotal : '—' }}
                    </p>

                    <button wire:click="removeItem({{ $line->item->id }})" class="text-ink-faint hover:text-danger" aria-label="Remove item">&times;</button>
                </div>
            @endforeach
        </div>

        <div class="mt-6 rounded-2xl border border-line bg-surface p-5">
            @if ($cart->coupon)
                <div class="flex items-center justify-between">
                    <p class="text-sm">
                        Coupon <span class="font-code font-semibold text-brand">{{ $cart->coupon->code }}</span> applied - saved ₹{{ $discount }}
                    </p>
                    <button wire:click="removeCoupon" class="text-sm text-ink-soft hover:text-danger">Remove</button>
                </div>
            @else
                <form wire:submit="applyCoupon" class="flex gap-3">
                    <input wire:model="couponCode" type="text" placeholder="Have a coupon code?" class="flex-1 rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                    <button type="submit" class="rounded-full border border-line px-5 py-2 text-sm font-medium text-ink hover:border-brand hover:text-brand">Apply</button>
                </form>
                @if ($couponError)
                    <p class="mt-2 text-sm text-danger">{{ $couponError }}</p>
                @endif
            @endif
        </div>

        <div class="mt-6 space-y-2 rounded-2xl border border-line bg-surface p-5">
            <div class="flex justify-between text-sm"><span class="text-ink-soft">Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
            @if ($cart->coupon)
                <div class="flex justify-between text-sm text-success"><span>Coupon discount</span><span>-₹{{ $discount }}</span></div>
            @endif
            <div class="flex justify-between border-t border-line pt-2 text-base font-semibold">
                <span>Total</span><span>₹{{ number_format(max(0, $subtotal - $discount), 2) }}</span>
            </div>
        </div>

        <a href="{{ route('storefront.checkout') }}" wire:navigate class="mt-6 block w-full rounded-full bg-brand px-7 py-3.5 text-center text-base font-medium text-white hover:bg-brand-hover">
            Proceed to Checkout
        </a>
    @endif
</div>

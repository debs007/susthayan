<div class="mx-auto max-w-3xl px-6 py-10" x-data
     x-on:payment-ready.window="
        var rzp = new Razorpay({
            key: $wire.razorpayKey,
            amount: $wire.amountInPaise,
            currency: 'INR',
            order_id: $wire.razorpayOrderId,
            name: 'Susthayan',
            description: 'Order #' + $wire.orderId,
            handler: function (response) {
                $wire.verifyPayment(response.razorpay_order_id, response.razorpay_payment_id, response.razorpay_signature);
            },
            theme: { color: '#208060' },
        });
        rzp.open();
     ">
    <h1 class="font-display text-2xl font-medium text-ink">Checkout</h1>

    @if (! $cart || $cart->items->isEmpty())
        <p class="mt-6 text-sm text-ink-soft">Your cart is empty. <a href="{{ route('storefront.categories') }}" wire:navigate class="text-brand hover:underline">Go shopping</a>.</p>
    @else
        @if ($errorMessage)
            <p class="mt-6 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">{{ $errorMessage }}</p>
        @endif

        @if ($needsApprovedPrescription)
            <div class="mt-4 rounded-lg border border-warning/30 bg-warning/5 px-4 py-4 text-sm">
                <p class="font-medium text-ink">An approved prescription is needed for this order.</p>
                <p class="mt-1 text-ink-soft">Upload one from your account and wait for pharmacist approval, then come back to place this order.</p>
                <a href="{{ route('storefront.health-records') }}" wire:navigate class="mt-3 inline-block text-sm font-medium text-brand hover:underline">Go to My Prescriptions</a>
            </div>
        @elseif ($cart->requiresPrescription() && ! $hasApprovedPrescription)
            <p class="mt-4 rounded-lg bg-warning/10 px-4 py-3 text-sm text-warning">
                One or more items need a prescription. You'll need an approved prescription on file before this order can be placed.
            </p>
        @endif

        {{-- Address --}}
        <section class="mt-8">
            <h2 class="font-display text-lg font-medium text-ink">Delivery address</h2>

            @if ($addresses->isEmpty() && ! $showAddAddress)
                <p class="mt-2 text-sm text-ink-soft">You don't have a saved address yet.</p>
            @endif

            <div class="mt-3 space-y-2">
                @foreach ($addresses as $address)
                    <label class="flex items-start gap-3 rounded-xl border p-4 {{ $selectedAddressId === $address->id ? 'border-brand bg-mist/30' : 'border-line' }}">
                        <input type="radio" wire:model.live="selectedAddressId" value="{{ $address->id }}" class="mt-1 accent-brand">
                        <span>
                            <span class="block text-sm font-medium text-ink">{{ $address->label ?? 'Address' }}</span>
                            <span class="block text-xs text-ink-soft">{{ $address->line1 }}, {{ $address->line2 }}, {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            @if ($showAddAddress)
                <form wire:submit="saveNewAddress" class="mt-4 space-y-3 rounded-xl border border-line p-4">
                    <input wire:model="line1" type="text" placeholder="Address line 1" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                    @error('line1') <p class="text-xs text-danger">{{ $message }}</p> @enderror

                    <input wire:model="line2" type="text" placeholder="Address line 2 (optional)" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input wire:model="city" type="text" placeholder="City" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                            @error('city') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <input wire:model="state" type="text" placeholder="State" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                            @error('state') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <input wire:model="pincode" type="text" placeholder="Pincode" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                    @error('pincode') <p class="text-xs text-danger">{{ $message }}</p> @enderror

                    <div class="flex gap-3">
                        <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">Save address</button>
                        <button type="button" wire:click="$set('showAddAddress', false)" class="text-sm text-ink-soft hover:text-ink">Cancel</button>
                    </div>
                </form>
            @else
                <button wire:click="$set('showAddAddress', true)" class="mt-3 text-sm font-medium text-brand hover:underline">+ Add a new address</button>
            @endif
        </section>

        {{-- Order summary --}}
        @php
            $lines = $cart->items->map(function ($item) use ($cart) {
                $price = $item->product->priceFor($cart->franchise_id);
                return (object) ['item' => $item, 'lineTotal' => $price ? round($price->selling_price * $item->quantity, 2) : 0];
            });
            $subtotal = $lines->sum('lineTotal');
            $discount = $cart->couponDiscountAmount();
        @endphp

        <section class="mt-8 rounded-2xl border border-line bg-surface p-5">
            <h2 class="font-display text-lg font-medium text-ink">Order summary</h2>
            <div class="mt-3 space-y-1.5">
                @foreach ($lines as $line)
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-soft">{{ $line->item->product->name }} &times; {{ $line->item->quantity }}</span>
                        <span>₹{{ number_format($line->lineTotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 space-y-1.5 border-t border-line pt-3">
                <div class="flex justify-between text-sm"><span class="text-ink-soft">Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
                @if ($cart->coupon)
                    <div class="flex justify-between text-sm text-success"><span>Coupon ({{ $cart->coupon->code }})</span><span>-₹{{ $discount }}</span></div>
                @endif
                <div class="flex justify-between text-base font-semibold"><span>Total</span><span>₹{{ number_format(max(0, $subtotal - $discount), 2) }}</span></div>
            </div>
        </section>

        <button
            wire:click="placeOrder"
            @disabled($cart->requiresPrescription() && ! $hasApprovedPrescription)
            wire:loading.attr="disabled"
            wire:target="placeOrder"
            class="mt-6 w-full rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover disabled:bg-ink-faint"
        >
            <span wire:loading.remove wire:target="placeOrder">Place Order &amp; Pay</span>
            <span wire:loading wire:target="placeOrder">Placing order&hellip;</span>
        </button>
    @endif
</div>

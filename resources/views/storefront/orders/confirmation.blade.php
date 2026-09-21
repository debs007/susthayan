<x-layouts.storefront title="Order Confirmed - Susthayan">
    <div class="mx-auto max-w-2xl px-6 py-16 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-mist">
            <svg class="h-8 w-8 text-brand" viewBox="0 0 24 24" fill="none"><path d="m5 13 5 5L20 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>
        <h1 class="mt-6 font-display text-2xl font-medium text-ink">Order confirmed</h1>
        <p class="mt-2 text-sm text-ink-soft">Order #{{ $order->id }} - we'll notify you as it's prepared and shipped.</p>

        <div class="mt-8 rounded-2xl border border-line bg-surface p-6 text-left">
            <div class="divide-y divide-line">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-2 text-sm">
                        <span class="text-ink-soft">{{ $item->product->name ?? 'Product' }} &times; {{ $item->quantity }}</span>
                        <span>₹{{ number_format($item->total_price, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 flex justify-between border-t border-line pt-3 text-base font-semibold">
                <span>Total paid</span>
                <span>₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('storefront.orders') }}" wire:navigate class="rounded-full border border-line px-6 py-3 text-sm font-medium text-ink hover:border-brand hover:text-brand">View your orders</a>
            <a href="{{ route('storefront.home') }}" wire:navigate class="rounded-full bg-brand px-6 py-3 text-sm font-medium text-white hover:bg-brand-hover">Continue shopping</a>
        </div>
    </div>
</x-layouts.storefront>

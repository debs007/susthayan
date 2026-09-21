<x-layouts.storefront title="Your Orders - Susthayan">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <h1 class="font-display text-2xl font-medium text-ink">Your Orders</h1>

        @if ($orders->isEmpty())
            <div class="mt-10 text-center">
                <p class="text-sm text-ink-soft">You haven't placed any orders yet.</p>
                <a href="{{ route('storefront.categories') }}" wire:navigate class="mt-4 inline-block rounded-full bg-brand px-6 py-3 text-sm font-medium text-white hover:bg-brand-hover">Start shopping</a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($orders as $order)
                    <div class="rounded-2xl border border-line bg-surface p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-ink">Order #{{ $order->id }}</p>
                                <p class="text-xs text-ink-faint">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }} &bull; {{ $order->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-ink">₹{{ number_format($order->total_amount, 2) }}</p>
                                <p class="text-xs text-ink-soft">{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</p>
                            </div>
                        </div>

                        @if ($order->order_type === 'product' && $order->items->isNotEmpty())
                            <div class="mt-3 border-t border-line pt-3 text-sm text-ink-soft">
                                {{ $order->items->pluck('product.name')->filter()->join(', ') }}
                            </div>
                        @elseif ($order->order_type === 'lab_test' && $order->labTestBooking)
                            <div class="mt-3 border-t border-line pt-3 text-sm text-ink-soft">
                                {{ $order->labTestBooking->labTest->name ?? 'Lab test' }} &bull; {{ $order->labTestBooking->scheduled_date->format('d M Y') }}
                            </div>
                        @elseif ($order->order_type === 'appointment' && $order->appointmentBooking)
                            <div class="mt-3 border-t border-line pt-3 text-sm text-ink-soft">
                                Dr. {{ $order->appointmentBooking->doctor->name ?? '' }} &bull; {{ $order->appointmentBooking->scheduled_date->format('d M Y') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
</x-layouts.storefront>

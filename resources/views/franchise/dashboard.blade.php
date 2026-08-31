<x-layouts.app title="Dashboard">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Today's orders</p>
            <p class="mt-1.5 font-display text-3xl font-semibold">{{ $todaysOrderCount }}</p>
            @if ($orderChangeVsYesterday !== 0)
                <p class="mt-1 text-xs {{ $orderChangeVsYesterday > 0 ? 'text-success-600' : 'text-danger-500' }}">
                    {{ $orderChangeVsYesterday > 0 ? '↑' : '↓' }} {{ abs($orderChangeVsYesterday) }} vs. yesterday
                </p>
            @endif
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Awaiting verification</p>
            <p class="mt-1.5 font-display text-3xl font-semibold {{ $pendingPrescriptions > 0 ? 'text-honey-600' : '' }}">{{ $pendingPrescriptions }}</p>
            <p class="mt-1 text-xs text-ink-muted">Prescriptions, network-wide</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Low stock</p>
            <p class="mt-1.5 font-display text-3xl font-semibold {{ $lowStockCount > 0 ? 'text-honey-600' : '' }}">{{ $lowStockCount }}</p>
            <p class="mt-1 text-xs text-ink-muted">Products running low</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Near-expiry batches</p>
            <p class="mt-1.5 font-display text-3xl font-semibold text-honey-600">{{ $watchBatches->count() }}</p>
            <p class="mt-1 text-xs text-ink-muted">Within 90 days</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-5">
        <div class="lg:col-span-3 rounded-xl border border-border bg-canvas-raised">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Recent orders</h2>
                <a href="{{ route('franchise.orders.index') }}" class="text-xs font-medium text-primary-500 hover:underline">View all →</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Order</th>
                        <th class="px-5 py-3 font-medium">Customer</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="px-5 py-3 font-code text-xs">#HP-{{ $order->id }}</td>
                            <td class="px-5 py-3">{{ $order->user?->name ?? $order->walk_in_customer_name ?? 'Walk-in' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full bg-canvas px-2.5 py-1 text-xs font-medium capitalize text-ink-muted">
                                    {{ str_replace('_', ' ', $order->status->value) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-ink-muted">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:col-span-2 rounded-xl border border-border bg-canvas-raised">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Batches to watch</h2>
                <a href="{{ route('franchise.inventory.index') }}" class="text-xs font-medium text-primary-500 hover:underline">View all →</a>
            </div>
            <ul class="divide-y divide-border">
                @forelse ($watchBatches as $batch)
                    <li class="freshness-{{ $batch->freshness }} flex items-center justify-between border-l-4 px-5 py-3">
                        <div>
                            <p class="text-sm font-medium">{{ $batch->product->name }}</p>
                            <p class="font-code text-xs text-ink-muted">{{ $batch->batch_no }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="freshness-dot h-2 w-2 rounded-full"></span>
                            <span class="text-xs text-ink-muted">{{ $batch->days_to_expiry }}d left</span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-muted">Nothing expiring soon.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-layouts.app>

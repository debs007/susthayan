<x-layouts.app title="Orders">
    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Orders</h1>
        <p class="text-sm text-ink-muted">Every order across every store - product orders, lab tests, appointments, and wallet top-ups.</p>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1 block text-xs text-ink-muted">Search (order #, customer name, mobile)</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-64 rounded-lg border border-border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-xs text-ink-muted">Status</label>
            <select name="status" class="rounded-lg border border-border px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach (['pending_payment', 'confirmed', 'ready_for_dispatch', 'out_for_delivery', 'delivered', 'picked_up', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs text-ink-muted">Type</label>
            <select name="order_type" class="rounded-lg border border-border px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach (['product', 'lab_test', 'appointment', 'wallet_topup'] as $type)
                    <option value="{{ $type }}" @selected(request('order_type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 flex items-center gap-1.5 text-xs text-ink-muted">
                <input type="checkbox" name="unassigned" value="1" @checked(request()->boolean('unassigned'))>
                Awaiting assignment only
            </label>
        </div>
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Filter</button>
        @if (request()->hasAny(['search', 'status', 'order_type']))
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-ink-muted hover:text-ink">Clear</a>
        @endif
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Order</th>
                    <th class="px-5 py-3 font-medium">Customer</th>
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 font-medium">Amount</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Placed</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-5 py-3 font-code font-medium">#{{ $order->id }}</td>
                        <td class="px-5 py-3">
                            <p>{{ $order->user?->name ?? 'Unknown customer' }}</p>
                            @if ($order->user?->mobile)
                                <p class="text-xs text-ink-muted">+91 {{ $order->user->mobile }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted">
                            @if ($order->franchise)
                                {{ $order->franchise->name }}
                            @elseif ($order->order_type === 'product')
                                <span class="rounded bg-warning-50 px-1.5 py-0.5 text-xs font-medium text-warning-600">Awaiting assignment</span>
                            @else
                                &mdash;
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</td>
                        <td class="px-5 py-3 font-code">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-5 py-3 text-xs">{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</td>
                        <td class="px-5 py-3 text-xs text-ink-muted">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">Details →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-ink-muted">No orders match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</x-layouts.app>

<x-layouts.app title="Orders">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @error('status')
        <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600">{{ $message }}</div>
    @enderror
    @error('delivery')
        <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600">{{ $message }}</div>
    @enderror

    <form method="GET" class="mb-6 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('franchise.orders.index') }}" class="rounded-lg px-3 py-1.5 font-medium {{ ! request('status') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">All</a>
        @foreach (['confirmed', 'preparing', 'ready_for_dispatch', 'out_for_delivery', 'delivered', 'picked_up'] as $status)
            <a href="{{ route('franchise.orders.index', ['status' => $status]) }}" class="rounded-lg px-3 py-1.5 font-medium capitalize {{ request('status') === $status ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">
                {{ str_replace('_', ' ', $status) }}
            </a>
        @endforeach
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Order</th>
                    <th class="px-5 py-3 font-medium">Channel</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Total</th>
                    <th class="px-5 py-3 font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-code text-xs font-medium">#HP-{{ $order->id }}</p>
                            <p class="text-xs text-ink-muted">{{ $order->items->count() }} item{{ $order->items->count() === 1 ? '' : 's' }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted">{{ $order->fulfillment_type->value }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium capitalize text-primary-600">{{ str_replace('_', ' ', $order->status->value) }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-5 py-3">
                            @if (($nextStatuses[$order->id] ?? []) !== [])
                                <form method="POST" action="{{ route('franchise.orders.status', $order) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-lg border border-border px-2 py-1.5 text-xs focus:border-primary-500 focus:outline-none">
                                        @foreach ($nextStatuses[$order->id] as $next)
                                            <option value="{{ $next }}">Mark {{ str_replace('_', ' ', $next) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-600">Go</button>
                                </form>
                            @endif

                            @if ($order->status->value === 'ready_for_dispatch' && $order->fulfillment_type->value === 'delivery' && $order->deliveryAssignments->isEmpty())
                                <form method="POST" action="{{ route('franchise.orders.assign-delivery', $order) }}" class="mt-2 flex items-center gap-2">
                                    @csrf
                                    <select name="delivery_agent_id" class="rounded-lg border border-border px-2 py-1.5 text-xs focus:border-primary-500 focus:outline-none" required>
                                        <option value="">Assign to...</option>
                                        @foreach ($deliveryAgents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-lg bg-honey-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-honey-600">Assign</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No orders match this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
</x-layouts.app>

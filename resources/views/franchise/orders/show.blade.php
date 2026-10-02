@php
    $shortItems = $order->items->filter(fn ($item) => $item->available_quantity < $item->quantity);
    $prefillParams = ['prefill' => $shortItems->values()->map(fn ($item) => [
        'product_id' => $item->product_id,
        'quantity' => $item->quantity - $item->available_quantity,
    ])->all()];
@endphp
<x-layouts.app title="Order #HP-{{ $order->id }}">
    <a href="{{ route('franchise.orders.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to orders
    </a>

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

    <div class="mb-6 flex items-start justify-between gap-4 rounded-xl border border-border bg-canvas-raised p-5">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-display text-lg font-semibold">#HP-{{ $order->id }}</h1>
                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium capitalize text-primary-600">{{ str_replace('_', ' ', $order->status->value) }}</span>
                <span class="rounded-full bg-canvas px-2.5 py-1 text-xs font-medium capitalize text-ink-muted">{{ $order->fulfillment_type->value }}</span>
            </div>
            <p class="mt-2 text-sm text-ink-muted">
                {{ $order->user?->name ?? 'Unknown customer' }}
                @if ($order->user?->mobile)
                    &bull; +91 {{ $order->user->mobile }}
                @endif
                &bull; {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>
        <p class="whitespace-nowrap font-code text-base font-semibold">₹{{ number_format($order->total_amount, 2) }}</p>
    </div>

    @if ($order->order_type === 'lab_test' && $order->labTestBookings->isNotEmpty())
        <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Lab tests</h2>
            <div class="space-y-2">
                @foreach ($order->labTestBookings as $booking)
                    <div class="flex items-center justify-between text-sm">
                        <span>{{ $booking->labTest?->name ?? 'Lab test' }}</span>
                        <span class="text-ink-muted">{{ $booking->labCenter?->name ?? '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif ($order->order_type === 'appointment' && $order->appointmentBooking)
        <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Appointment</h2>
            <p class="text-sm">Dr. {{ $order->appointmentBooking->doctor?->name ?? '—' }}</p>
            <p class="text-sm text-ink-muted">{{ $order->appointmentBooking->hospital?->name ?? '—' }}</p>
        </div>
    @else
        <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Items</h2>
            <div class="space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0 last:pb-0">
                        <div>
                            <p>{{ $item->product?->name ?? "Product #{$item->product_id}" }}</p>
                            <p class="text-xs text-ink-muted">Qty {{ $item->quantity }} &bull; Have {{ $item->available_quantity }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium {{ $item->available_quantity >= $item->quantity ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600' }}">
                                {{ $item->available_quantity >= $item->quantity ? 'Available' : 'Short' }}
                            </span>
                            <span class="whitespace-nowrap font-code">₹{{ number_format($item->total_price, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($shortItems->isNotEmpty())
                <a href="{{ route('franchise.purchase-orders.create', $prefillParams) }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg border border-danger-500 px-3 py-1.5 text-xs font-medium text-danger-500 hover:bg-danger-50">
                    Create supply order for {{ $shortItems->count() }} missing item{{ $shortItems->count() === 1 ? '' : 's' }}
                </a>
            @endif
        </div>
    @endif

    <div class="rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Fulfillment</h2>

        @if ($nextStatuses !== [])
            <form method="POST" action="{{ route('franchise.orders.status', $order) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="rounded-lg border border-border px-2 py-1.5 text-xs focus:border-primary-500 focus:outline-none">
                    @foreach ($nextStatuses as $next)
                        <option value="{{ $next }}">Mark {{ str_replace('_', ' ', $next) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-600">Go</button>
            </form>
        @endif

        @if ($order->deliveryAssignments->isNotEmpty())
            <p class="mt-3 text-sm text-ink-muted">
                Delivery: {{ $order->deliveryAssignments->first()->deliveryAgent?->name ?? 'Unassigned' }}
            </p>
        @elseif ($order->status->value === 'ready_for_dispatch' && $order->fulfillment_type->value === 'delivery')
            <form method="POST" action="{{ route('franchise.orders.assign-delivery', $order) }}" class="mt-3 flex items-center gap-2">
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
    </div>
</x-layouts.app>

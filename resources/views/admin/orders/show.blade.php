<x-layouts.app title="Order #{{ $order->id }}">
    <a href="{{ route('admin.orders.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Orders
    </a>

    <div class="mb-6 flex items-center justify-between rounded-xl border border-border bg-canvas-raised p-5">
        <div>
            <h1 class="font-display text-lg font-semibold">Order #{{ $order->id }}</h1>
            <p class="text-sm text-ink-muted">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }} &bull; Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="text-right">
            <p class="font-display text-lg font-semibold">₹{{ number_format($order->total_amount, 2) }}</p>
            <p class="text-xs text-ink-muted">{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</p>
            @if ($order->invoice)
                <a href="{{ route('admin.orders.invoice', $order) }}" class="mt-2 inline-block text-xs font-medium text-primary-500 hover:underline">Download invoice</a>
            @endif
        </div>
    </div>

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-600">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-600">{{ session('success') }}</div>
    @endif

    <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Order status</h2>
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex items-center gap-2">
            @csrf
            <select name="new_status" class="flex-1 rounded-lg border border-border bg-canvas px-3 py-2 text-sm">
                @foreach (['pending_payment', 'confirmed', 'preparing', 'ready_for_dispatch', 'out_for_delivery', 'delivered', 'picked_up', 'cancelled', 'refunded'] as $status)
                    <option value="{{ $status }}" @selected($order->status->value === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Update status</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            @if ($order->order_type === 'product' && $order->items->isNotEmpty())
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Items</h2>
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                            <p>{{ $item->product?->name ?? 'Product #'.$item->product_id }}</p>
                            <p class="text-ink-muted">Qty: {{ $item->quantity }} &bull; ₹{{ number_format($item->unit_price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @elseif ($order->order_type === 'lab_test' && $order->labTestBookings->isNotEmpty())
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Lab Test Details</h2>
                    @foreach ($order->labTestBookings as $booking)
                        <p class="font-medium">{{ $booking->labTest?->name ?? '—' }}</p>
                    @endforeach
                    {{-- Every test in one booking shares the same center, date, and status - shown once. --}}
                    <p class="text-sm text-ink-muted mt-2">{{ $order->labTestBookings->first()->labCenter?->name ?? '—' }}</p>
                    <p class="text-sm text-ink-muted">Scheduled: {{ $order->labTestBookings->first()->scheduled_date->format('d M Y') }} &bull; {{ ucfirst($order->labTestBookings->first()->status) }}</p>
                </div>
            @elseif ($order->order_type === 'lab_test' && $order->labTestBooking)
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Lab Test Details</h2>
                    <p class="font-medium">{{ $order->labTestBooking->labTest?->name ?? '—' }}</p>
                    <p class="text-sm text-ink-muted">{{ $order->labTestBooking->labCenter?->name ?? '—' }}</p>
                    <p class="text-sm text-ink-muted">Scheduled: {{ $order->labTestBooking->scheduled_date->format('d M Y') }} &bull; {{ ucfirst($order->labTestBooking->status) }}</p>
                </div>
            @elseif ($order->order_type === 'appointment' && $order->appointmentBooking)
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Appointment Details</h2>
                    <p class="font-medium">{{ $order->appointmentBooking->doctor?->name ?? '—' }}</p>
                    <p class="text-sm text-ink-muted">{{ $order->appointmentBooking->hospital?->name ?? '—' }}</p>
                    <p class="text-sm text-ink-muted">Scheduled: {{ $order->appointmentBooking->scheduled_date->format('d M Y') }} &bull; {{ ucfirst($order->appointmentBooking->status) }}</p>
                </div>
            @elseif ($order->order_type === 'wallet_topup')
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Wallet Top-up</h2>
                    <p class="text-sm text-ink-muted">₹{{ number_format($order->total_amount, 2) }} added to {{ $order->user->name }}'s wallet on successful payment.</p>
                </div>
            @endif

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Payment History ({{ $order->payments->count() }})</h2>
                @forelse ($order->payments as $payment)
                    <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                        <div>
                            <p class="font-code text-xs text-ink-muted">{{ $payment->gateway_order_id }}</p>
                            <p class="text-xs text-ink-muted">{{ $payment->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-code">₹{{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs
                                {{ $payment->status->value === 'success' ? 'text-success-600' : ($payment->status->value === 'failed' ? 'text-danger-600' : 'text-ink-muted') }}">
                                {{ ucfirst($payment->status->value) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-muted">No payment attempts recorded.</p>
                @endforelse
            </div>

            @if ($order->deliveryAssignments->isNotEmpty())
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Delivery</h2>
                    @foreach ($order->deliveryAssignments as $assignment)
                        <div class="border-b border-border py-2 text-sm last:border-0">
                            <p>{{ $assignment->deliveryAgent?->name ?? '—' }}</p>
                            <p class="text-xs text-ink-muted">{{ ucfirst($assignment->status) }} &bull; Assigned {{ $assignment->assigned_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Customer</h2>
                <p class="font-medium">{{ $order->user->name }}</p>
                <p class="text-sm text-ink-muted">+91 {{ $order->user->mobile }}</p>
                <a href="{{ route('admin.customers.show', $order->user) }}" class="mt-2 inline-block text-xs font-medium text-primary-500 hover:text-primary-600">View full profile →</a>
            </div>

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Franchise</h2>
                @if ($order->franchise)
                    <p class="text-sm">{{ $order->franchise->name }}</p>
                @elseif ($order->order_type === 'product')
                    <p class="mb-3 text-sm font-medium text-warning-600">Awaiting assignment</p>
                    @if (session('error'))
                        <p class="mb-3 text-xs text-danger-600">{{ session('error') }}</p>
                    @endif
                    <form method="POST" action="{{ route('admin.orders.assign', $order) }}" class="flex items-center gap-2">
                        @csrf
                        <select name="franchise_id" required class="flex-1 rounded-lg border border-border bg-canvas px-3 py-2 text-sm">
                            <option value="">Choose a franchise&hellip;</option>
                            @foreach ($franchises as $franchise)
                                <option value="{{ $franchise->id }}">{{ $franchise->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-lg bg-primary-500 px-3 py-2 text-sm font-medium text-white hover:bg-primary-600">Assign</button>
                    </form>
                @else
                    {{-- lab_test / appointment / wallet_topup - each has its own fulfillment entity chosen at booking time (a lab center, a hospital), not a franchise --}}
                    <p class="text-sm text-ink-muted">Not applicable for this order type.</p>
                @endif
            </div>

            @if ($order->address)
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Delivery Address</h2>
                    <p class="text-sm">{{ $order->address->label ?? 'Address' }}</p>
                    <p class="text-xs text-ink-muted">{{ $order->address->line1 }}, {{ $order->address->city }}, {{ $order->address->pincode }}</p>
                </div>
            @endif

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Amount Breakdown</h2>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-ink-muted">Subtotal</span><span>₹{{ number_format($order->subtotal_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Discount</span><span>-₹{{ number_format($order->discount_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Tax</span><span>₹{{ number_format($order->tax_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Delivery</span><span>₹{{ number_format($order->delivery_charge, 2) }}</span></div>
                    <div class="flex justify-between border-t border-border pt-1 font-semibold"><span>Total</span><span>₹{{ number_format($order->total_amount, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

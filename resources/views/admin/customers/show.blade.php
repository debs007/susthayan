<x-layouts.app title="{{ $customer->name }}">
    <a href="{{ route('admin.customers.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Customers
    </a>

    <div class="mb-6 flex items-center gap-4 rounded-xl border border-border bg-canvas-raised p-5">
        <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-border bg-canvas">
            @if ($customer->profile_image_url)
                <img src="{{ $customer->profile_image_url }}" alt="" class="h-full w-full object-cover">
            @else
                <span class="text-lg text-ink-muted">{{ Str::substr($customer->name, 0, 1) }}</span>
            @endif
        </div>
        <div class="flex-1">
            <h1 class="font-display text-lg font-semibold">{{ $customer->name }}</h1>
            <p class="text-sm text-ink-muted">+91 {{ $customer->mobile }} @if ($customer->email) &bull; {{ $customer->email }} @endif</p>
            <p class="text-xs text-ink-muted">Joined {{ $customer->created_at->format('d M Y') }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-wide text-ink-muted">Wallet Balance</p>
            <p class="font-display text-lg font-semibold text-primary-600">₹{{ number_format($customer->wallet?->balance ?? 0, 2) }}</p>
            <a href="{{ route('admin.notifications.create', ['user_id' => $customer->id]) }}" class="mt-2 inline-block rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-600">
                Send Notification
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Orders --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Order History ({{ $customer->orders->count() }})</h2>
            @forelse ($customer->orders as $order)
                <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                    <div>
                        <p class="font-medium">#{{ $order->id }} <span class="text-xs text-ink-muted">({{ ucfirst(str_replace('_', ' ', $order->order_type)) }})</span></p>
                        <p class="text-xs text-ink-muted">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-code">₹{{ number_format($order->total_amount, 2) }}</p>
                        <p class="text-xs text-ink-muted">{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No orders yet.</p>
            @endforelse
        </div>

        {{-- Prescriptions --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Prescriptions ({{ $customer->prescriptions->count() }})</h2>
            @forelse ($customer->prescriptions as $prescription)
                <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                    <p class="text-xs text-ink-muted">{{ $prescription->created_at->format('d M Y') }}</p>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $prescription->verification_status === 'approved' ? 'bg-success-50 text-success-600' : ($prescription->verification_status === 'rejected' ? 'bg-danger-50 text-danger-600' : 'bg-canvas text-ink-muted') }}">
                        {{ ucfirst($prescription->verification_status) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No prescriptions uploaded.</p>
            @endforelse
        </div>

        {{-- Vitals --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Vitals Logged ({{ $customer->vitals->count() }})</h2>
            @forelse ($customer->vitals as $vital)
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="text-xs text-ink-muted">{{ $vital->recorded_at->format('d M Y, h:i A') }}</p>
                    <p class="text-xs">
                        @if ($vital->heart_rate_bpm) HR: {{ $vital->heart_rate_bpm }} bpm &bull; @endif
                        @if ($vital->blood_pressure_label) BP: {{ $vital->blood_pressure_label }} &bull; @endif
                        @if ($vital->spo2_percentage) SpO2: {{ $vital->spo2_percentage }}% @endif
                    </p>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No vitals logged.</p>
            @endforelse
        </div>

        {{-- Health Records --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Health Records ({{ $customer->healthRecords->count() }})</h2>
            @forelse ($customer->healthRecords as $record)
                <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                    <div>
                        <p class="font-medium">{{ $record->title }}</p>
                        <p class="text-xs text-ink-muted">{{ ucfirst(str_replace('_', ' ', $record->type)) }} &bull; {{ $record->record_date->format('d M Y') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No health records uploaded.</p>
            @endforelse
        </div>

        {{-- Doctor Appointments --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Doctor Appointments ({{ $customer->appointmentBookings->count() }})</h2>
            @forelse ($customer->appointmentBookings as $booking)
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="font-medium">{{ $booking->doctor->name }} <span class="text-xs text-ink-muted">— {{ $booking->hospital->name }}</span></p>
                    <p class="text-xs text-ink-muted">{{ $booking->scheduled_date->format('d M Y') }} &bull; {{ ucfirst($booking->status) }}</p>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No appointments booked.</p>
            @endforelse
        </div>

        {{-- Lab Test Bookings --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Lab Test Bookings ({{ $customer->labTestBookings->count() }})</h2>
            @forelse ($customer->labTestBookings as $booking)
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="font-medium">{{ $booking->labTest->name }} <span class="text-xs text-ink-muted">— {{ $booking->labCenter->name }}</span></p>
                    <p class="text-xs text-ink-muted">{{ $booking->scheduled_date->format('d M Y') }} &bull; {{ ucfirst($booking->status) }}</p>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No lab tests booked.</p>
            @endforelse
        </div>

        {{-- Current Cart --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Current Cart</h2>
            @if ($customer->cart && $customer->cart->items->isNotEmpty())
                @foreach ($customer->cart->items as $item)
                    <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                        <p>{{ $item->product->name }}</p>
                        <p class="text-xs text-ink-muted">Qty: {{ $item->quantity }}</p>
                    </div>
                @endforeach
                @if ($customer->cart->coupon)
                    <p class="mt-2 inline-block rounded bg-primary-50 px-2 py-1 font-code text-xs font-medium text-primary-600">
                        Coupon applied: {{ $customer->cart->coupon->code }}
                    </p>
                @endif
            @else
                <p class="text-sm text-ink-muted">Cart is empty.</p>
            @endif
        </div>

        {{-- Addresses --}}
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Saved Addresses ({{ $customer->addresses->count() }})</h2>
            @forelse ($customer->addresses as $address)
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="font-medium">{{ $address->label ?? 'Address' }}</p>
                    <p class="text-xs text-ink-muted">{{ $address->line1 }}, {{ $address->city }}, {{ $address->pincode }}</p>
                </div>
            @empty
                <p class="text-sm text-ink-muted">No saved addresses.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>

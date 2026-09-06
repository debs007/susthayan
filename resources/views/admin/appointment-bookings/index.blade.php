<x-layouts.app title="Dr Bookings">
    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Dr Bookings</h1>
        <p class="text-sm text-ink-muted">Every doctor appointment booked through the app, across all hospitals.</p>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1 block text-xs text-ink-muted">Search (customer or doctor name)</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-64 rounded-lg border border-border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-xs text-ink-muted">Status</label>
            <select name="status" class="rounded-lg border border-border px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Filter</button>
        @if (request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.appointment-bookings.index') }}" class="text-sm text-ink-muted hover:text-ink">Clear</a>
        @endif
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Customer</th>
                    <th class="px-5 py-3 font-medium">Doctor</th>
                    <th class="px-5 py-3 font-medium">Hospital</th>
                    <th class="px-5 py-3 font-medium">Scheduled</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Payment</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $booking->user->name }}</p>
                            <p class="text-xs text-ink-muted">+91 {{ $booking->user->mobile }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <p>{{ $booking->doctor?->name ?? 'Doctor removed' }}</p>
                            <p class="text-xs text-ink-muted">{{ $booking->doctor?->degree }}</p>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $booking->hospital?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs">{{ $booking->scheduled_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-xs">{{ ucfirst($booking->status) }}</td>
                        <td class="px-5 py-3 text-xs">{{ $booking->order ? ucfirst(str_replace('_', ' ', $booking->order->status->value)) : '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            @if ($booking->order)
                                <a href="{{ route('admin.orders.show', $booking->order) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">Order details →</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-ink-muted">No appointment bookings match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</x-layouts.app>

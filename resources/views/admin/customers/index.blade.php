<x-layouts.app title="Customers">
    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Customers</h1>
        <p class="text-sm text-ink-muted">Every registered app customer - tap a row for their full profile, order history, and health data.</p>
    </div>

    <form method="GET" class="mb-5 flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or mobile number" class="w-full max-w-sm rounded-lg border border-border px-3 py-2 text-sm">
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Search</button>
        @if (request('search'))
            <a href="{{ route('admin.customers.index') }}" class="rounded-lg border border-border px-4 py-2 text-sm text-ink-muted hover:bg-canvas">Clear</a>
        @endif
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Customer</th>
                    <th class="px-5 py-3 font-medium">Mobile</th>
                    <th class="px-5 py-3 font-medium">Orders</th>
                    <th class="px-5 py-3 font-medium">Joined</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $customer->name }}</td>
                        <td class="px-5 py-3 font-code text-ink-muted">+91 {{ $customer->mobile }}</td>
                        <td class="px-5 py-3 font-code text-xs">{{ $customer->orders_count }}</td>
                        <td class="px-5 py-3 text-xs text-ink-muted">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">View Details →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No customers {{ request('search') ? 'match that search' : 'yet' }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
</x-layouts.app>

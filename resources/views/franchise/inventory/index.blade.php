<x-layouts.app title="Inventory">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex-1 max-w-sm">
            <input
                type="text" name="q" value="{{ request('q') }}"
                placeholder="Search by product name..."
                class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
            >
        </form>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('franchise.inventory.index') }}" class="rounded-lg px-3 py-1.5 font-medium {{ ! request('filter') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">All batches</a>
            <a href="{{ route('franchise.inventory.index', ['filter' => 'expiring']) }}" class="rounded-lg px-3 py-1.5 font-medium {{ request('filter') === 'expiring' ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">Expiring soon</a>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 font-medium">Batch</th>
                    <th class="px-5 py-3 font-medium">Expiry</th>
                    <th class="px-5 py-3 text-right font-medium">In stock</th>
                    <th class="px-5 py-3 text-right font-medium">Reserved</th>
                    <th class="px-5 py-3 text-right font-medium">Available</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($batches as $batch)
                    <tr class="freshness-{{ $batch->freshness }} border-l-4">
                        <td class="px-5 py-3 font-medium">{{ $batch->product->name }}</td>
                        <td class="px-5 py-3 font-code text-xs text-ink-muted">{{ $batch->batch_no }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 font-code text-xs">
                                <span class="freshness-dot h-1.5 w-1.5 rounded-full"></span>
                                {{ $batch->expiry_date->format('d M Y') }}
                                <span class="text-ink-muted">({{ $batch->days_to_expiry }}d)</span>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs">{{ $batch->quantity }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs text-ink-muted">{{ $batch->reserved_quantity }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs font-semibold">{{ $batch->available_quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No stock on hand.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($batches->hasPages())
        <div class="mt-4">{{ $batches->links() }}</div>
    @endif
</x-layouts.app>

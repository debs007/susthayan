<x-layouts.app title="Purchase Orders">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex justify-end">
        <a href="{{ route('franchise.purchase-orders.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + New purchase order
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">PO</th>
                    <th class="px-5 py-3 font-medium">Supplier</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($purchaseOrders as $po)
                    <tr class="cursor-pointer hover:bg-canvas" onclick="window.location='{{ route('franchise.purchase-orders.show', $po) }}'">
                        <td class="px-5 py-3 font-code text-xs font-medium">#{{ $po->id }}</td>
                        <td class="px-5 py-3">{{ $po->supplier->name }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted">{{ str_replace('_', ' ', $po->status->value) }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($po->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-ink-muted">
                            No purchase orders yet. <a href="{{ route('franchise.purchase-orders.create') }}" class="font-medium text-primary-500 hover:underline">Create the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($purchaseOrders->hasPages())
        <div class="mt-4">{{ $purchaseOrders->links() }}</div>
    @endif
</x-layouts.app>

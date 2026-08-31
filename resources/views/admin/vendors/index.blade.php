<x-layouts.app title="Vendors">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.vendors.index') }}" class="rounded-lg bg-primary-50 px-3 py-1.5 font-medium text-primary-600">Suppliers</a>
            <a href="{{ route('admin.vendors.outstanding') }}" class="rounded-lg px-3 py-1.5 font-medium text-ink-muted hover:bg-canvas">Outstanding & purchase orders</a>
        </div>
        <a href="{{ route('admin.vendors.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add supplier
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Supplier</th>
                    <th class="px-5 py-3 font-medium">Contact</th>
                    <th class="px-5 py-3 font-medium">Credit terms</th>
                    <th class="px-5 py-3 font-medium">Purchase orders</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $supplier->name }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $supplier->contact_person ?? $supplier->phone ?? '—' }}</td>
                        <td class="px-5 py-3 font-code text-xs">{{ $supplier->credit_days ? "{$supplier->credit_days} days" : '—' }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.vendors.ledger', $supplier) }}" class="font-code text-xs text-primary-500 hover:underline">
                                {{ $supplier->purchase_orders_count }} → ledger
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $supplier->is_active ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.vendors.edit', $supplier) }}" class="text-sm font-medium text-primary-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">
                            No suppliers yet. <a href="{{ route('admin.vendors.create') }}" class="font-medium text-primary-500 hover:underline">Add the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($suppliers->hasPages())
        <div class="mt-4">{{ $suppliers->links() }}</div>
    @endif
</x-layouts.app>

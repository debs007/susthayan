<x-layouts.app title="Outstanding & Purchase Orders">
    <div class="mb-6 flex gap-2 text-sm">
        <a href="{{ route('admin.vendors.index') }}" class="rounded-lg px-3 py-1.5 font-medium text-ink-muted hover:bg-canvas">Suppliers</a>
        <a href="{{ route('admin.vendors.outstanding') }}" class="rounded-lg bg-primary-50 px-3 py-1.5 font-medium text-primary-600">Outstanding & purchase orders</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Vendor outstanding (network-wide)</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Supplier</th>
                        <th class="px-5 py-3 text-right font-medium">Invoiced</th>
                        <th class="px-5 py-3 text-right font-medium">Paid</th>
                        <th class="px-5 py-3 text-right font-medium">Outstanding</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($outstanding as $row)
                        <tr>
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.vendors.ledger', $row['supplier_id']) }}" class="hover:underline">{{ $row['supplier_name'] }}</a>
                            </td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($row['total_invoiced'], 2) }}</td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($row['total_paid'], 2) }}</td>
                            <td class="px-5 py-3 text-right font-code text-xs font-semibold text-honey-600">₹{{ number_format($row['outstanding'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-ink-muted">Nothing outstanding right now.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Recent purchase orders</h2>
                <p class="text-xs text-ink-muted">Created by franchises - admin has oversight, not creation, here.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Supplier</th>
                        <th class="px-5 py-3 font-medium">Franchise</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($recentPurchaseOrders as $po)
                        <tr class="cursor-pointer hover:bg-canvas" onclick="window.location='{{ route('admin.purchase-orders.show', $po) }}'">
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.purchase-orders.show', $po) }}" class="hover:underline">{{ $po->supplier->name }}</a>
                            </td>
                            <td class="px-5 py-3 text-ink-muted">{{ $po->franchise->name }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted">
                                    {{ str_replace('_', ' ', $po->status->value) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-ink-muted">No purchase orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>

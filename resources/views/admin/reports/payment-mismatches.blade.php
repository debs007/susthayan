<x-layouts.app title="Payment Mismatches">
    @include('admin.reports._tabs')

    <div class="mb-6 rounded-lg border border-honey-500/30 bg-honey-50 px-4 py-2.5 text-sm text-honey-600">
        Current-state data-integrity check, not a historical report - this reflects right now, not a date range.
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Confirmed without a successful payment</h2>
                <p class="text-xs text-ink-muted">Shouldn't be possible given how the payment flow gates this - if anything shows here, it's worth investigating directly.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Order</th>
                        <th class="px-5 py-3 font-medium">Franchise</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($mismatches['confirmed_without_successful_payment'] as $row)
                        <tr>
                            <td class="px-5 py-3 font-code text-xs">#{{ $row['order_id'] }}</td>
                            <td class="px-5 py-3">{{ $row['franchise'] }}</td>
                            <td class="px-5 py-3 text-xs capitalize text-ink-muted">{{ str_replace('_', ' ', $row['status']) }}</td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($row['total_amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-success-600">None - clean.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Paid but order still pending</h2>
                <p class="text-xs text-ink-muted">Almost always means a webhook silently failed and the client-side verify call never landed either.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Order</th>
                        <th class="px-5 py-3 font-medium">Franchise</th>
                        <th class="px-5 py-3 text-right font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($mismatches['paid_but_order_still_pending'] as $row)
                        <tr>
                            <td class="px-5 py-3 font-code text-xs">#{{ $row['order_id'] }}</td>
                            <td class="px-5 py-3">{{ $row['franchise'] }}</td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($row['total_amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-success-600">None - clean.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>

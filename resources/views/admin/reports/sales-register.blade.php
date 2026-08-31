<x-layouts.app title="Sales Register">
    @include('admin.reports._tabs')

    <x-reports.date-filter
        :action="route('admin.reports.sales-register')"
        :from="$from"
        :to="$to"
        :csv-action="route('admin.reports.sales-register').'?format=csv'"
    />

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Order</th>
                    <th class="px-5 py-3 font-medium">Invoice</th>
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">Channel</th>
                    <th class="px-5 py-3 text-right font-medium">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-5 py-3 font-code text-xs">{{ $row['date'] }}</td>
                        <td class="px-5 py-3 font-code text-xs">#{{ $row['order_id'] }}</td>
                        <td class="px-5 py-3 font-code text-xs text-ink-muted">{{ $row['invoice_number'] ?: '—' }}</td>
                        <td class="px-5 py-3">{{ $row['franchise'] }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted">{{ $row['channel'] }}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No sales in this period.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($rows->isNotEmpty())
                <tfoot>
                    <tr class="border-t border-border">
                        <td colspan="5" class="px-5 py-3 text-right text-sm font-semibold">Total</td>
                        <td class="px-5 py-3 text-right font-code text-sm font-semibold">₹{{ number_format($total, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</x-layouts.app>

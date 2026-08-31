<x-layouts.app title="Settlements">
    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Period</th>
                    <th class="px-5 py-3 text-right font-medium">Gross sales</th>
                    <th class="px-5 py-3 text-right font-medium">Commission</th>
                    <th class="px-5 py-3 text-right font-medium">Net payable</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($settlements as $settlement)
                    <tr>
                        <td class="px-5 py-3 font-code text-xs">{{ $settlement->period_start->format('d M') }} - {{ $settlement->period_end->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($settlement->gross_sales, 2) }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs text-ink-muted">₹{{ number_format($settlement->commission_amount, 2) }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs font-semibold">₹{{ number_format($settlement->net_payable, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $settlement->status->value === 'paid' ? 'bg-success-50 text-success-600' : 'bg-honey-50 text-honey-600' }}">
                                {{ ucfirst($settlement->status->value) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No settlements generated for your store yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($settlements->hasPages())
        <div class="mt-4">{{ $settlements->links() }}</div>
    @endif
</x-layouts.app>

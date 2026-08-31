<x-layouts.app title="{{ $supplier->name }} - Ledger">
    <a href="{{ route('admin.vendors.outstanding') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to outstanding
    </a>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="font-display text-lg font-semibold">{{ $supplier->name }}</h2>
            <p class="text-sm text-ink-muted">Network-wide statement - every franchise's transactions with this supplier.</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised px-5 py-3 text-right">
            <p class="text-xs text-ink-muted">Closing balance</p>
            <p class="font-display text-xl font-semibold {{ $ledger['closing_balance'] > 0 ? 'text-honey-600' : 'text-success-600' }}">
                ₹{{ number_format($ledger['closing_balance'], 2) }}
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Reference</th>
                    <th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 text-right font-medium">Debit</th>
                    <th class="px-5 py-3 text-right font-medium">Credit</th>
                    <th class="px-5 py-3 text-right font-medium">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($ledger['entries'] as $entry)
                    <tr>
                        <td class="px-5 py-3 font-code text-xs">{{ \Illuminate\Support\Carbon::parse($entry['date'])->format('d M Y') }}</td>
                        <td class="px-5 py-3">{{ $entry['reference'] ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $entry['type'] === 'invoice' ? 'bg-honey-50 text-honey-600' : 'bg-success-50 text-success-600' }}">
                                {{ ucfirst($entry['type']) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs">{{ $entry['debit'] > 0 ? '₹'.number_format($entry['debit'], 2) : '—' }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs">{{ $entry['credit'] > 0 ? '₹'.number_format($entry['credit'], 2) : '—' }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs font-semibold">₹{{ number_format($entry['running_balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No transactions with this supplier yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

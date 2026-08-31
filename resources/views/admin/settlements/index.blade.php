<x-layouts.app title="Settlements">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @error('release')
        <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600">{{ $message }}</div>
    @enderror

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Franchise</th>
                            <th class="px-5 py-3 font-medium">Period</th>
                            <th class="px-5 py-3 text-right font-medium">Net payable</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($settlements as $settlement)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $settlement->franchise->name }}</td>
                                <td class="px-5 py-3 font-code text-xs text-ink-muted">
                                    {{ $settlement->period_start->format('d M') }} - {{ $settlement->period_end->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-right font-code text-xs font-semibold">₹{{ number_format($settlement->net_payable, 2) }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $settlement->status->value === 'paid' ? 'bg-success-50 text-success-600' : 'bg-honey-50 text-honey-600' }}">
                                        {{ ucfirst($settlement->status->value) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @if ($settlement->status->value === 'generated')
                                        <form method="POST" action="{{ route('admin.settlements.release', $settlement) }}" onsubmit="return confirm('Release payout of ₹{{ number_format($settlement->net_payable, 2) }} to {{ $settlement->franchise->name }}?');">
                                            @csrf
                                            <button type="submit" class="text-sm font-medium text-primary-500 hover:underline">Release</button>
                                        </form>
                                    @elseif ($settlement->payout_reference)
                                        <span class="font-code text-xs text-ink-muted">{{ $settlement->payout_reference }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No settlements generated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($settlements->hasPages())
                <div class="mt-4">{{ $settlements->links() }}</div>
            @endif
        </div>

        <div>
            <form method="POST" action="{{ route('admin.settlements.generate') }}" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Generate settlement</h2>

                <x-form.select
                    name="franchise_id"
                    label="Franchise"
                    placeholder="All active franchises"
                    :options="$franchises->pluck('name', 'id')"
                    hint="Leave blank to generate for every active franchise at once - one with an overlapping period or nothing to settle is skipped, not blocking, without affecting the rest."
                />
                <x-form.field name="period_start" label="Period start" type="date" required />
                <x-form.field name="period_end" label="Period end" type="date" required />

                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Generate
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

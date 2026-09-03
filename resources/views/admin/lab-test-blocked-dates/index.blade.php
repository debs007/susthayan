<x-layouts.app title="Lab Test Blocked Dates">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Lab Test Blocked Dates</h1>
        <p class="text-sm text-ink-muted">No lab test bookings (home visit or center visit, any test) can be made for a date blocked here - applies network-wide.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Date</th>
                            <th class="px-5 py-3 font-medium">Reason</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($blockedDates as $blocked)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $blocked->date->format('D, d M Y') }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $blocked->reason ?? '—' }}</td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.lab-test-blocked-dates.destroy', $blocked) }}" onsubmit="return confirm('Unblock this date?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-danger-600 hover:text-danger-700">Unblock</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-ink-muted">No dates blocked - every future date is currently bookable.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <form method="POST" action="{{ route('admin.lab-test-blocked-dates.store') }}" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Block a date</h2>
                <x-form.field name="date" label="Date" type="date" required :min="now()->toDateString()" />
                <x-form.field name="reason" label="Reason (optional)" placeholder="e.g. Public holiday" />
                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Block date
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app title="Franchises">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-ink-muted">
            {{ $franchises->total() }} {{ Str::plural('franchise', $franchises->total()) }} in the network
        </p>
        <a href="{{ route('admin.franchises.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add franchise
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">City</th>
                    <th class="px-5 py-3 font-medium">Commission</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Bank details</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($franchises as $franchise)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $franchise->name }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $franchise->city ?? '—' }}</td>
                        <td class="px-5 py-3 font-code text-xs">{{ number_format($franchise->commission_percentage, 2) }}%</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $franchise->status === 'active' ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600' }}">
                                {{ ucfirst($franchise->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($franchise->bank_account_number)
                                <span class="text-xs font-medium text-success-600">✓ On file</span>
                            @else
                                <span class="text-xs font-medium text-honey-600">Not added</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.franchises.edit', $franchise) }}" class="text-sm font-medium text-primary-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">
                            No franchises yet.
                            <a href="{{ route('admin.franchises.create') }}" class="font-medium text-primary-500 hover:underline">Add the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($franchises->hasPages())
        <div class="mt-4">{{ $franchises->links() }}</div>
    @endif
</x-layouts.app>

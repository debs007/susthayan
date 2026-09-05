<x-layouts.app title="Lab Centers">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Lab Centers</h1>
            <p class="text-sm text-ink-muted">Where tests are performed - customers pick one of these per booking.</p>
        </div>
        <a href="{{ route('admin.lab-centers.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add center
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Center</th>
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">Home collection</th>
                    <th class="px-5 py-3 font-medium">Tests offered</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($centers as $center)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $center->name }}</p>
                            <p class="text-xs text-ink-muted">{{ $center->fullAddress() }}</p>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $center->franchise->name }}</td>
                        <td class="px-5 py-3">
                            @if ($center->offers_home_collection)
                                <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600">Yes</span>
                            @else
                                <span class="text-xs text-ink-muted">No</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-code text-xs">{{ $center->tests_count }}</td>
                        <td class="px-5 py-3">
                            @if ($center->is_active)
                                <span class="text-xs font-medium text-success-600">Active</span>
                            @else
                                <span class="text-xs font-medium text-ink-muted">Hidden</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.lab-centers.edit', $center) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">Edit</a>
                                <form method="POST" action="{{ route('admin.lab-centers.toggle-active', $center) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                        {{ $center->is_active ? 'Hide' : 'Unhide' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No centers yet - add the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

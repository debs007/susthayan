<x-layouts.app title="Hospitals">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Hospitals</h1>
            <p class="text-sm text-ink-muted">Doctors are affiliated with hospitals from the Doctor form, not here.</p>
        </div>
        <a href="{{ route('admin.hospitals.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add hospital
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Hospital</th>
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">Doctors</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($hospitals as $hospital)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $hospital->name }}</p>
                            <p class="text-xs text-ink-muted">{{ $hospital->fullAddress() }}</p>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $hospital->franchise->name }}</td>
                        <td class="px-5 py-3 font-code text-xs">{{ $hospital->doctors_count }}</td>
                        <td class="px-5 py-3">
                            @if ($hospital->is_active)
                                <span class="text-xs font-medium text-success-600">Active</span>
                            @else
                                <span class="text-xs font-medium text-ink-muted">Hidden</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('admin.hospitals.toggle-active', $hospital) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                    {{ $hospital->is_active ? 'Hide' : 'Unhide' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No hospitals yet - add the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

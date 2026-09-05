<x-layouts.app title="Doctors">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Doctors</h1>
            <p class="text-sm text-ink-muted">Each doctor can be affiliated with multiple hospitals, each with its own charge and visit days.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.departments.index') }}" class="text-sm font-medium text-ink-muted hover:text-ink">Departments</a>
            <a href="{{ route('admin.hospitals.index') }}" class="text-sm font-medium text-ink-muted hover:text-ink">Hospitals</a>
            <a href="{{ route('admin.doctors.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                + Add doctor
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Doctor</th>
                    <th class="px-5 py-3 font-medium">Department</th>
                    <th class="px-5 py-3 font-medium">Hospitals</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($doctors as $doctor)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-border bg-canvas">
                                    @if ($doctor->photo_url)
                                        <img src="{{ $doctor->photo_url }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-xs text-ink-muted">{{ Str::substr($doctor->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium">{{ $doctor->name }}</p>
                                    <p class="text-xs text-ink-muted">{{ $doctor->degree }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $doctor->department->name }}</td>
                        <td class="px-5 py-3 font-code text-xs">{{ $doctor->hospitals->count() }}</td>
                        <td class="px-5 py-3">
                            @if ($doctor->is_active)
                                <span class="text-xs font-medium text-success-600">Active</span>
                            @else
                                <span class="text-xs font-medium text-ink-muted">Hidden</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.doctors.edit', $doctor) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">Edit</a>
                                <form method="POST" action="{{ route('admin.doctors.toggle-active', $doctor) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                        {{ $doctor->is_active ? 'Hide' : 'Unhide' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No doctors yet - add the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

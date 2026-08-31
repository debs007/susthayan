<x-layouts.app title="Users & Roles">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-1.5 font-medium {{ ! request('role') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">All</a>
            @foreach ($roles as $role)
                <a href="{{ route('admin.users.index', ['role' => $role]) }}" class="rounded-lg px-3 py-1.5 font-medium {{ request('role') === $role ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">{{ $role }}</a>
            @endforeach
        </div>
        <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add user
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Mobile</th>
                    <th class="px-5 py-3 font-medium">Role</th>
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-5 py-3 font-code text-xs text-ink-muted">{{ $user->mobile }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-600">{{ $user->roles->first()?->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $user->franchise?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $user->is_active ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600' }}">
                                {{ $user->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-medium text-primary-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">
                            No staff accounts yet. <a href="{{ route('admin.users.create') }}" class="font-medium text-primary-500 hover:underline">Add the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
</x-layouts.app>

<x-layouts.app title="Audit Log">
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-border bg-canvas-raised p-4">
        <div>
            <label class="mb-1.5 block text-xs font-medium text-ink-muted">Model</label>
            <select name="subject_type" class="rounded-lg border border-border bg-canvas-raised px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">All models</option>
                @foreach ($subjectTypes as $type)
                    <option value="{{ $type }}" @selected(request('subject_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-medium text-ink-muted">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-medium text-ink-muted">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Filter</button>
        @if (request()->hasAny(['subject_type', 'from', 'to']))
            <a href="{{ route('admin.audit-log.index') }}" class="text-sm font-medium text-ink-muted hover:underline">Clear</a>
        @endif
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">When</th>
                    <th class="px-5 py-3 font-medium">Event</th>
                    <th class="px-5 py-3 font-medium">Model</th>
                    <th class="px-5 py-3 font-medium">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($activity as $entry)
                    <tr>
                        <td class="px-5 py-3 font-code text-xs text-ink-muted">{{ $entry->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-5 py-3">{{ $entry->description }}</td>
                        <td class="px-5 py-3 font-code text-xs">
                            {{ class_basename($entry->subject_type) }} #{{ $entry->subject_id }}
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $entry->causer?->name ?? 'System' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-ink-muted">No matching activity.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($activity->hasPages())
        <div class="mt-4">{{ $activity->links() }}</div>
    @endif
</x-layouts.app>

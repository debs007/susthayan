<x-layouts.app title="Import history">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Import history</h1>
            <p class="text-sm text-ink-muted">Every bulk catalogue upload, and how it went.</p>
        </div>
        <a href="{{ route('admin.products.import.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + New upload
        </a>
    </div>

    <x-form.errors />
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-border text-xs text-ink-muted">
                <tr>
                    <th class="px-5 py-3">File</th>
                    <th class="px-5 py-3">Uploaded by</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Imported</th>
                    <th class="px-5 py-3">Duplicates</th>
                    <th class="px-5 py-3">Skipped</th>
                    <th class="px-5 py-3">Started</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($imports as $import)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $import->original_filename }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $import->uploader?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @php
                                $statusColor = match ($import->status) {
                                    'completed' => 'bg-emerald-50 text-emerald-700',
                                    'failed' => 'bg-red-50 text-red-700',
                                    'processing' => 'bg-amber-50 text-amber-700',
                                    default => 'bg-canvas text-ink-muted',
                                };
                            @endphp
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor }}">{{ ucfirst($import->status) }}</span>
                            @if ($import->status === 'processing' && $import->total_rows)
                                <span class="ml-1 text-xs text-ink-muted">({{ $import->progressPercentage() }}%)</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">{{ $import->imported_count }}</td>
                        <td class="px-5 py-3">{{ $import->duplicate_count }}</td>
                        <td class="px-5 py-3">{{ $import->skipped_count }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $import->started_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            @if (in_array($import->status, ['completed', 'failed']))
                                <form method="POST" action="{{ route('admin.products.import.destroy', $import) }}" onsubmit="return confirm('Remove this import record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-ink-muted hover:text-red-600">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-ink-muted">
                            No imports yet. <a href="{{ route('admin.products.import.create') }}" class="font-medium text-primary-500 hover:underline">Upload a catalogue file</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $imports->links() }}</div>
</x-layouts.app>

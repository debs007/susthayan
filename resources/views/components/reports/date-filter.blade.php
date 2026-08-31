@props(['action', 'from', 'to', 'csvAction' => null])

<form method="GET" action="{{ $action }}" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-border bg-canvas-raised p-4">
    <div>
        <label class="mb-1.5 block text-xs font-medium text-ink-muted">From</label>
        <input type="date" name="from" value="{{ $from }}" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-ink-muted">To</label>
        <input type="date" name="to" value="{{ $to }}" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
    </div>
    <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
        Apply
    </button>
    @if ($csvAction)
        <a
            href="{{ $csvAction }}&from={{ $from }}&to={{ $to }}"
            class="ml-auto rounded-lg border border-border px-4 py-2 text-sm font-medium text-ink hover:bg-canvas"
        >
            ↓ Export CSV
        </a>
    @endif
</form>

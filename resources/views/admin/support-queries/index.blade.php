<x-layouts.app title="Help & Support">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Help &amp; Support</h1>
            <p class="text-sm text-ink-muted">Every customer query, in their own words.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-border px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="open" @selected(request('status') === 'open')>Open</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
            </select>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($queries as $query)
            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-medium">{{ $query->subject }}</p>
                        <p class="mt-0.5 text-xs text-ink-muted">
                            {{ $query->user?->name ?? 'Unknown customer' }}
                            @if ($query->user?->mobile)
                                &bull; +91 {{ $query->user->mobile }}
                            @endif
                            &bull; {{ $query->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium {{ $query->status === 'resolved' ? 'bg-success-50 text-success-600' : 'bg-amber-50 text-amber-700' }}">
                        {{ ucfirst($query->status) }}
                    </span>
                </div>
                <p class="mt-3 whitespace-pre-line text-sm text-ink">{{ $query->message }}</p>
                @if ($query->status === 'open')
                    <form method="POST" action="{{ route('admin.support-queries.resolve', $query) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg border border-success-500 px-3 py-1.5 text-xs font-medium text-success-600 hover:bg-success-50">
                            Mark resolved
                        </button>
                    </form>
                @else
                    <p class="mt-3 text-xs text-ink-muted">Resolved {{ $query->resolved_at?->format('d M Y, h:i A') }}</p>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-border bg-canvas-raised px-5 py-10 text-center text-sm text-ink-muted">
                No support queries yet.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $queries->links() }}</div>
</x-layouts.app>

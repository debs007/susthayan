<x-layouts.app title="Prescriptions">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <p class="mb-6 text-sm text-ink-muted">{{ $pending->count() }} awaiting review</p>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($pending as $prescription)
            <div x-data="{ rejecting: false }" class="rounded-xl border border-border bg-canvas-raised p-4">
                <a href="{{ route('franchise.prescriptions.file', $prescription) }}" target="_blank" class="mb-3 block overflow-hidden rounded-lg border border-border">
                    <img src="{{ route('franchise.prescriptions.file', $prescription) }}" alt="Prescription" class="h-40 w-full object-cover">
                </a>
                <p class="text-sm font-medium">{{ $prescription->user->name }}</p>
                <p class="mb-3 font-code text-xs text-ink-muted">{{ $prescription->user->mobile }} · {{ $prescription->created_at->diffForHumans() }}</p>

                <div x-show="!rejecting" class="flex gap-2">
                    <form method="POST" action="{{ route('franchise.prescriptions.verify', $prescription) }}" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="w-full rounded-lg bg-success-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-success-600">Approve</button>
                    </form>
                    <button @click="rejecting = true" type="button" class="flex-1 rounded-lg border border-danger-500/30 px-3 py-1.5 text-xs font-medium text-danger-600 hover:bg-danger-50">
                        Reject
                    </button>
                </div>

                <form x-show="rejecting" x-cloak method="POST" action="{{ route('franchise.prescriptions.verify', $prescription) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <textarea name="rejection_reason" rows="2" placeholder="Reason for rejection..." required class="w-full rounded-lg border border-border px-2 py-1.5 text-xs focus:border-primary-500 focus:outline-none"></textarea>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 rounded-lg bg-danger-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-danger-600">Confirm reject</button>
                        <button @click="rejecting = false" type="button" class="rounded-lg border border-border px-3 py-1.5 text-xs font-medium text-ink-muted hover:bg-canvas">Cancel</button>
                    </div>
                </form>
            </div>
        @empty
            <p class="col-span-full py-12 text-center text-ink-muted">Nothing waiting on review.</p>
        @endforelse
    </div>
</x-layouts.app>

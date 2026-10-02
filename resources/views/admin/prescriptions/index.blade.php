<x-layouts.app title="Prescriptions">
    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Prescriptions</h1>
        <p class="text-sm text-ink-muted">Every prescription ever uploaded, regardless of status - for reviewing what's already been decided, not just what's pending.</p>
    </div>

    <form method="GET" class="mb-5 flex items-end gap-3">
        <div>
            <label class="mb-1 block text-xs text-ink-muted">Status</label>
            <select name="status" class="rounded-lg border border-border px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Filter</button>
        @if (request('status'))
            <a href="{{ route('admin.prescriptions.index') }}" class="text-sm text-ink-muted hover:text-ink">Clear</a>
        @endif
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Customer</th>
                    <th class="px-5 py-3 font-medium">Uploaded</th>
                    <th class="px-5 py-3 font-medium">Order</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Verified by</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($prescriptions as $prescription)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $prescription->user->name }}</p>
                            <p class="text-xs text-ink-muted">+91 {{ $prescription->user->mobile }}</p>
                        </td>
                        <td class="px-5 py-3 text-xs text-ink-muted">{{ $prescription->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3 font-code text-xs">{{ $prescription->order_id ? '#'.$prescription->order_id : '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $prescription->verification_status === 'approved' ? 'bg-success-50 text-success-600' : ($prescription->verification_status === 'rejected' ? 'bg-danger-50 text-danger-600' : 'bg-canvas text-ink-muted') }}">
                                {{ ucfirst($prescription->verification_status) }}
                            </span>
                            @if ($prescription->verification_status === 'rejected' && $prescription->rejection_reason)
                                <p class="mt-1 text-xs text-ink-muted">{{ $prescription->rejection_reason }}</p>
                            @endif
                            @if ($prescription->verification_status === 'approved' && $prescription->medicines->isNotEmpty())
                                <p class="mt-1 text-xs text-ink-muted">{{ $prescription->medicines->pluck('medicine_name')->join(', ') }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-ink-muted">{{ $prescription->verifiedBy?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.prescriptions.show', $prescription) }}" target="_blank" class="text-xs font-medium text-primary-500 hover:text-primary-600">View file →</a>
                            @if ($prescription->verification_status === 'approved' && ! $prescription->order_id)
                                <a href="{{ route('admin.prescriptions.create-order', $prescription) }}" class="ml-3 text-xs font-medium text-success-600 hover:text-success-700">Create order →</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No prescriptions {{ request('status') ? 'with this status' : 'yet' }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $prescriptions->links() }}</div>
</x-layouts.app>

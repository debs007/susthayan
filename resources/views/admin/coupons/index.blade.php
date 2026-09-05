<x-layouts.app title="Coupons">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Coupons</h1>
            <p class="text-sm text-ink-muted">Link one to a Home Banner to show a discounted product list when it's tapped.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add coupon
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Code</th>
                    <th class="px-5 py-3 font-medium">Discount</th>
                    <th class="px-5 py-3 font-medium">Products</th>
                    <th class="px-5 py-3 font-medium">Valid</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($coupons as $coupon)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-code font-medium">{{ $coupon->code }}</p>
                            @if ($coupon->description)
                                <p class="text-xs text-ink-muted">{{ $coupon->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : '₹'.number_format($coupon->discount_value, 2) }}
                            @if ($coupon->max_discount_amount)
                                <span class="text-xs text-ink-muted">(up to ₹{{ number_format($coupon->max_discount_amount, 2) }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-code text-xs">{{ $coupon->products_count }}</td>
                        <td class="px-5 py-3 text-xs text-ink-muted">
                            @if ($coupon->valid_from || $coupon->valid_until)
                                {{ $coupon->valid_from?->format('d M') ?? 'Any' }} - {{ $coupon->valid_until?->format('d M Y') ?? 'No end date' }}
                            @else
                                No date limit
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if ($coupon->is_active)
                                <span class="text-xs font-medium text-success-600">Active</span>
                            @else
                                <span class="text-xs font-medium text-ink-muted">Disabled</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">Edit</a>
                                <form method="POST" action="{{ route('admin.coupons.toggle-active', $coupon) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                        {{ $coupon->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No coupons yet - add the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

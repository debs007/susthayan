<x-layouts.app title="Edit - {{ $coupon->code }}">
    <a href="{{ route('admin.coupons.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Coupons
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @php $currentProductIds = $coupon->products->pluck('id', 'id'); @endphp

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="max-w-3xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf
        @method('PATCH')

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Coupon details</h2>
            <div class="grid grid-cols-2 gap-6">
                <x-form.field name="code" label="Code" required :value="old('code', $coupon->code)" />
                <x-form.select name="discount_type" label="Discount type" required :value="old('discount_type', $coupon->discount_type)" :options="['percentage' => 'Percentage off', 'fixed' => 'Fixed amount off']" />
                <x-form.field name="discount_value" label="Discount value" type="number" step="0.01" required :value="old('discount_value', $coupon->discount_value)" />
                <x-form.field name="max_discount_amount" label="Max discount amount (₹)" type="number" step="0.01" :value="old('max_discount_amount', $coupon->max_discount_amount)" />
                <x-form.field name="valid_from" label="Valid from" type="date" :value="old('valid_from', $coupon->valid_from?->toDateString())" />
                <x-form.field name="valid_until" label="Valid until" type="date" :value="old('valid_until', $coupon->valid_until?->toDateString())" />
                <div class="col-span-2">
                    <x-form.field name="description" label="Description" :value="old('description', $coupon->description)" />
                </div>
            </div>

            <label class="mt-4 flex items-start gap-3 rounded-lg border border-border p-4">
                <input type="checkbox" name="is_global" value="1" @checked(old('is_global', $coupon->is_global)) class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                <span>
                    <span class="block text-sm font-medium text-ink">Show on the app's Offers page</span>
                    <span class="block text-xs text-ink-muted">A global coupon is browsable by any customer on the general Offers/Coupons page, not just reachable via a specific banner link.</span>
                </span>
            </label>
        </div>

        <div>
            <h2 class="mb-2 border-b border-border pb-3 font-display font-semibold">Products this coupon applies to</h2>

            @php $groupedProducts = $products->groupBy(fn ($p) => $p->category?->name ?? 'Uncategorized'); @endphp
            <div class="max-h-96 space-y-5 overflow-y-auto rounded-lg border border-border p-4">
                @foreach ($groupedProducts as $categoryName => $categoryProducts)
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $categoryName }}</p>
                        <div class="space-y-1">
                            @foreach ($categoryProducts as $product)
                                <label class="flex items-center gap-2 rounded px-2 py-1.5 hover:bg-canvas">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(isset($currentProductIds[$product->id])) class="h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                                    <span class="text-sm">{{ $product->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Save changes
        </button>
    </form>
</x-layouts.app>

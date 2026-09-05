<x-layouts.app title="Add Coupon">
    <a href="{{ route('admin.coupons.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Coupons
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.coupons.store') }}" class="max-w-3xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Coupon details</h2>
            <div class="grid grid-cols-2 gap-6">
                <x-form.field name="code" label="Code" required placeholder="e.g. HEALTH20" />
                <x-form.select name="discount_type" label="Discount type" required :options="['percentage' => 'Percentage off', 'fixed' => 'Fixed amount off']" />
                <x-form.field name="discount_value" label="Discount value" type="number" step="0.01" required placeholder="e.g. 20" />
                <x-form.field name="max_discount_amount" label="Max discount amount (₹)" type="number" step="0.01" placeholder="Optional cap - only applies to percentage discounts" />
                <x-form.field name="valid_from" label="Valid from" type="date" />
                <x-form.field name="valid_until" label="Valid until" type="date" />
                <div class="col-span-2">
                    <x-form.field name="description" label="Description" placeholder="e.g. Flat 20% off on select medicines" />
                </div>
            </div>

            <label class="mt-4 flex items-start gap-3 rounded-lg border border-border p-4">
                <input type="checkbox" name="is_global" value="1" class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                <span>
                    <span class="block text-sm font-medium text-ink">Show on the app's Offers page</span>
                    <span class="block text-xs text-ink-muted">A global coupon is browsable by any customer on the general Offers/Coupons page, not just reachable via a specific banner link. Leave unchecked for a coupon that should only appear when linked from a Home Banner.</span>
                </span>
            </label>
        </div>

        <div>
            <h2 class="mb-2 border-b border-border pb-3 font-display font-semibold">Products this coupon applies to</h2>
            <p class="mb-4 text-xs text-ink-muted">Check every product this discount should apply to. The banner linking to this coupon will show exactly this product list, with the discounted price.</p>

            @php $groupedProducts = $products->groupBy(fn ($p) => $p->category?->name ?? 'Uncategorized'); @endphp
            <div class="max-h-96 space-y-5 overflow-y-auto rounded-lg border border-border p-4">
                @foreach ($groupedProducts as $categoryName => $categoryProducts)
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $categoryName }}</p>
                        <div class="space-y-1">
                            @foreach ($categoryProducts as $product)
                                <label class="flex items-center gap-2 rounded px-2 py-1.5 hover:bg-canvas">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                                    <span class="text-sm">{{ $product->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Add coupon
        </button>
    </form>
</x-layouts.app>

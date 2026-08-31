<x-layouts.app title="Edit {{ $product->name }}">
    <a href="{{ route('admin.products.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to products
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
                @csrf
                @method('PATCH')

                <div>
                    <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <x-form.field name="name" label="Product name" :value="$product->name" required />
                        </div>
                        <x-form.select name="category_id" label="Category" placeholder="No category" :value="$product->category_id" :options="$categories->pluck('name', 'id')" />
                        <x-form.field name="manufacturer" label="Manufacturer" :value="$product->manufacturer" />
                        <div class="col-span-2">
                            <x-form.field name="salt_composition" label="Salt composition" :value="$product->salt_composition" />
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Regulatory</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <x-form.select
                            name="drug_schedule"
                            label="Drug schedule"
                            required
                            :value="$product->drug_schedule"
                            :options="['otc' => 'OTC (no prescription)', 'h' => 'Schedule H', 'h1' => 'Schedule H1', 'x' => 'Schedule X']"
                        />
                        <x-form.field name="hsn_code" label="HSN code" :value="$product->hsn_code" />
                    </div>
                    <div class="mt-5 space-y-3">
                        <x-form.checkbox name="prescription_required" label="Requires a prescription to sell" :checked="$product->prescription_required" />
                        <x-form.checkbox name="is_active" label="Active (visible for sale)" :checked="$product->is_active" />
                    </div>
                </div>

                <div>
                    <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Catalog details</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <x-form.field name="unit" label="Unit" :value="$product->unit" />
                        <x-form.field name="barcode" label="Barcode" :value="$product->barcode" />
                        <div class="col-span-2">
                            <x-form.textarea name="description" label="Description" :value="$product->description" :rows="3" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 border-t border-border pt-6">
                    <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <div class="border-b border-border px-5 py-4">
                    <h2 class="font-display font-semibold">Prices on file</h2>
                </div>
                <div class="divide-y divide-border">
                    @forelse ($product->prices as $price)
                        <div class="px-5 py-3 text-sm">
                            <p class="font-medium">{{ $price->franchise?->name ?? 'Global default' }}</p>
                            <p class="font-code text-xs text-ink-muted">
                                MRP ₹{{ number_format($price->mrp, 2) }} · Sells ₹{{ number_format($price->selling_price, 2) }} · GST {{ $price->tax_percentage }}%
                            </p>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-sm text-ink-muted">No price set yet - this product can't be sold until one exists.</p>
                    @endforelse
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.prices.store', $product) }}" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Add / update a price</h2>
                <x-form.select
                    name="franchise_id"
                    label="Franchise"
                    placeholder="Global default (all franchises)"
                    :options="$franchises->pluck('name', 'id')"
                />
                <x-form.field name="mrp" label="MRP" type="number" step="0.01" required />
                <x-form.field name="selling_price" label="Selling price" type="number" step="0.01" required />
                <x-form.field name="tax_percentage" label="GST %" type="number" step="0.01" required />
                <x-form.field name="effective_from" label="Effective from" type="date" />
                <button type="submit" class="w-full rounded-lg bg-honey-500 px-4 py-2 text-sm font-medium text-white hover:bg-honey-600">
                    Save price
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app title="Add product">
    <a href="{{ route('admin.products.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to products
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="max-w-2xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Product name" required />
                </div>
                <x-form.select name="category_id" label="Category" placeholder="No category" :options="$categories->pluck('name', 'id')" />
                <x-form.select name="brand_id" label="Brand" placeholder="No brand" :options="$brands->pluck('name', 'id')" />
                <x-form.field name="manufacturer" label="Manufacturer" />
                <div class="col-span-2">
                    <x-form.field name="salt_composition" label="Salt composition" />
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
                    :options="['otc' => 'OTC (no prescription)', 'h' => 'Schedule H', 'h1' => 'Schedule H1', 'x' => 'Schedule X']"
                />
                <x-form.field name="hsn_code" label="HSN code" />
            </div>
            <div class="mt-5">
                <x-form.checkbox name="prescription_required" label="Requires a prescription to sell" hint="Gates checkout online and requires a pharmacist for POS sales of this item." />
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Catalog details</h2>
            <div class="grid grid-cols-2 gap-6">
                <x-form.field name="unit" label="Unit" hint="e.g. strip of 10, 100ml bottle" />
                <x-form.field name="barcode" label="Barcode" />
                <div class="col-span-2">
                    <x-form.textarea name="description" label="Description" :rows="3" />
                </div>
                <div class="col-span-2">
                    <label for="image" class="block text-sm font-medium text-ink">Product photo</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
                    <p class="mt-1.5 text-xs text-ink-muted">Optional - JPG, PNG, or WebP, min 200x200px, up to 4MB. Can also be added later from the edit page.</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 border-t border-border pt-6">
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Create product
            </button>
            <a href="{{ route('admin.products.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-canvas">
                Cancel
            </a>
        </div>
    </form>
</x-layouts.app>

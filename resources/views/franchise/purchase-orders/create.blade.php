<x-layouts.app title="New Purchase Order">
    <a href="{{ route('franchise.purchase-orders.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to purchase orders
    </a>

    <x-form.errors />

    <form
        method="POST" action="{{ route('franchise.purchase-orders.store') }}"
        x-data="{ items: [{ product_id: '', ordered_qty: 1, expected_rate: '' }] }"
        class="max-w-3xl space-y-6 rounded-xl border border-border bg-canvas-raised p-6"
    >
        @csrf

        <div class="grid grid-cols-2 gap-6">
            <x-form.select name="supplier_id" label="Supplier" required placeholder="Select a supplier" :options="$suppliers->pluck('name', 'id')" />
            <x-form.field name="expected_date" label="Expected delivery date" type="date" />
        </div>

        <div>
            <h2 class="mb-3 font-display font-semibold">Line items</h2>
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-start">
                        <div class="col-span-6">
                            <select :name="`items[${index}][product_id]`" x-model="item.product_id" required class="w-full rounded-lg border border-border bg-canvas-raised px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                                <option value="">Select product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" min="1" :name="`items[${index}][ordered_qty]`" x-model.number="item.ordered_qty" placeholder="Qty" required class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                        </div>
                        <div class="col-span-3">
                            <input type="number" step="0.01" min="0" :name="`items[${index}][expected_rate]`" x-model.number="item.expected_rate" placeholder="Rate ₹" required class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                        </div>
                        <div class="col-span-1 pt-2">
                            <button type="button" @click="items.length > 1 && items.splice(index, 1)" class="text-danger-500 hover:text-danger-600" aria-label="Remove line">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            <button type="button" @click="items.push({ product_id: '', ordered_qty: 1, expected_rate: '' })" class="mt-3 text-sm font-medium text-primary-500 hover:underline">
                + Add another line
            </button>
        </div>

        <div class="flex gap-3 border-t border-border pt-6">
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Create purchase order
            </button>
            <a href="{{ route('franchise.purchase-orders.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-canvas">Cancel</a>
        </div>
    </form>
</x-layouts.app>

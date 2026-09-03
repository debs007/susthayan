<x-layouts.app title="Add Lab Test">
    <a href="{{ route('admin.lab-tests.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Lab Tests
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.lab-tests.store') }}" class="max-w-2xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Test details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Test name" required placeholder="e.g. Complete Blood Count (CBC)" />
                </div>
                <x-form.select name="lab_test_category_id" label="Category" required :options="$categories->pluck('name', 'id')" />
                <x-form.field name="price" label="Price (₹)" type="number" step="0.01" required />
                <div class="col-span-2">
                    <x-form.field name="sample_type" label="Sample type" placeholder="e.g. Blood (fasting), Urine, N/A for imaging" />
                </div>
                <div class="col-span-2">
                    <x-form.textarea name="preparation_instructions" label="Preparation instructions" :rows="2" placeholder="e.g. 8-12 hours fasting required" />
                </div>
                <div class="col-span-2">
                    <x-form.textarea name="description" label="Description" :rows="3" />
                </div>
                <x-form.field name="duration_minutes" label="Typical duration (minutes)" type="number" />
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Visit type</h2>
            <label class="flex items-start gap-3 rounded-lg border border-border p-4">
                <input type="checkbox" name="requires_center_visit" value="1" class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                <span>
                    <span class="block text-sm font-medium text-ink">Requires a center visit</span>
                    <span class="block text-xs text-ink-muted">Check this for imaging tests (MRI, X-Ray, CT, Ultrasound) where the customer must come to a center with the equipment. Leave unchecked for sample-collection tests (blood, urine) where a technician visits the customer's home instead.</span>
                </span>
            </label>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Add test
        </button>
    </form>
</x-layouts.app>

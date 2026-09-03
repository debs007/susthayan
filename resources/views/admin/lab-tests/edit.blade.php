<x-layouts.app title="Edit - {{ $test->name }}">
    <a href="{{ route('admin.lab-tests.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Lab Tests
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.lab-tests.update', $test) }}" class="max-w-2xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf
        @method('PATCH')

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Test details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Test name" required :value="old('name', $test->name)" />
                </div>
                <x-form.select name="lab_test_category_id" label="Category" required :options="$categories->pluck('name', 'id')" :selected="old('lab_test_category_id', $test->lab_test_category_id)" />
                <x-form.field name="price" label="Price (₹)" type="number" step="0.01" required :value="old('price', $test->price)" />
                <div class="col-span-2">
                    <x-form.field name="sample_type" label="Sample type" :value="old('sample_type', $test->sample_type)" />
                </div>
                <div class="col-span-2">
                    <x-form.textarea name="preparation_instructions" label="Preparation instructions" :rows="2" :value="old('preparation_instructions', $test->preparation_instructions)" />
                </div>
                <div class="col-span-2">
                    <x-form.textarea name="description" label="Description" :rows="3" :value="old('description', $test->description)" />
                </div>
                <x-form.field name="duration_minutes" label="Typical duration (minutes)" type="number" :value="old('duration_minutes', $test->duration_minutes)" />
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Visit type</h2>
            <label class="flex items-start gap-3 rounded-lg border border-border p-4">
                <input type="checkbox" name="requires_center_visit" value="1" @checked(old('requires_center_visit', $test->requires_center_visit)) class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                <span>
                    <span class="block text-sm font-medium text-ink">Requires a center visit</span>
                    <span class="block text-xs text-ink-muted">Check this for imaging tests (MRI, X-Ray, CT, Ultrasound) where the customer must come to a center with the equipment. Leave unchecked for sample-collection tests (blood, urine) where a technician visits the customer's home instead.</span>
                </span>
            </label>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Save changes
        </button>
    </form>
</x-layouts.app>

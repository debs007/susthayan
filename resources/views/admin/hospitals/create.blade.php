<x-layouts.app title="Add Hospital">
    <a href="{{ route('admin.hospitals.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Hospitals
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.hospitals.store') }}" class="max-w-2xl space-y-6 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2">
                <x-form.field name="name" label="Hospital name" required />
            </div>
            <x-form.select name="franchise_id" label="Operated by (franchise)" required :options="$franchises->pluck('name', 'id')" />
            <x-form.field name="phone" label="Phone" />
            <div class="col-span-2">
                <x-form.field name="address" label="Address" required />
            </div>
            <x-form.field name="city" label="City" />
            <x-form.field name="state" label="State" />
            <x-form.field name="pincode" label="Pincode" />
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Add hospital
        </button>
    </form>
</x-layouts.app>

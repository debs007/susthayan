<x-layouts.app title="Add franchise">
    <a href="{{ route('admin.franchises.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to franchises
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.franchises.store') }}" class="max-w-2xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Franchise name" required />
                </div>
                <x-form.field name="phone" label="Phone" />
                <x-form.field name="email" label="Email" type="email" />
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Compliance</h2>
            <div class="grid grid-cols-2 gap-6">
                <x-form.field name="gstin" label="GSTIN" hint="15 characters, e.g. 22AAAAA0000A1Z5" />
                <x-form.field name="drug_license_number" label="Drug license number" />
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Location</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.textarea name="address" label="Address" />
                </div>
                <x-form.field name="city" label="City" />
                <x-form.field name="state" label="State" />
                <x-form.field name="pincode" label="Pincode" />
                <div class="grid grid-cols-2 gap-3">
                    <x-form.field name="latitude" label="Latitude" type="number" step="0.0000001" />
                    <x-form.field name="longitude" label="Longitude" type="number" step="0.0000001" />
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Settlement</h2>
            <x-form.field
                name="commission_percentage"
                label="Commission % (platform's share)"
                type="number"
                step="0.01"
                required
                hint="The franchise keeps 100% minus this. Bank details for payouts can be added after creating the franchise."
            />
        </div>

        <div class="flex gap-3 border-t border-border pt-6">
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Create franchise
            </button>
            <a href="{{ route('admin.franchises.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-canvas">
                Cancel
            </a>
        </div>
    </form>
</x-layouts.app>

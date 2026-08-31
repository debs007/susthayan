<x-layouts.app title="Add supplier">
    <a href="{{ route('admin.vendors.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to vendors
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.vendors.store') }}" class="max-w-2xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Supplier name" required />
                </div>
                <x-form.field name="contact_person" label="Contact person" />
                <x-form.field name="phone" label="Phone" />
                <x-form.field name="email" label="Email" type="email" />
                <x-form.field name="gstin" label="GSTIN" hint="15 characters, e.g. 22AAAAA0000A1Z5" />
                <div class="col-span-2">
                    <x-form.textarea name="address" label="Address" />
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Terms</h2>
            <x-form.field name="credit_days" label="Credit period (days)" type="number" hint="How long after an invoice date payment is due." />
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Bank details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="bank_account_name" label="Account holder name" />
                </div>
                <x-form.field name="bank_account_number" label="Account number" />
                <x-form.field name="bank_ifsc" label="IFSC code" />
            </div>
        </div>

        <div class="flex gap-3 border-t border-border pt-6">
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Add supplier
            </button>
            <a href="{{ route('admin.vendors.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-canvas">Cancel</a>
        </div>
    </form>
</x-layouts.app>

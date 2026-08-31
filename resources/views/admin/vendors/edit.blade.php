<x-layouts.app title="Edit {{ $supplier->name }}">
    <a href="{{ route('admin.vendors.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to vendors
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.vendors.update', $supplier) }}" class="max-w-2xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf
        @method('PATCH')

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Supplier name" :value="$supplier->name" required />
                </div>
                <x-form.field name="contact_person" label="Contact person" :value="$supplier->contact_person" />
                <x-form.field name="phone" label="Phone" :value="$supplier->phone" />
                <x-form.field name="email" label="Email" type="email" :value="$supplier->email" />
                <x-form.field name="gstin" label="GSTIN" hint="15 characters, e.g. 22AAAAA0000A1Z5" :value="$supplier->gstin" />
                <div class="col-span-2">
                    <x-form.textarea name="address" label="Address" :value="$supplier->address" />
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Terms</h2>
            <div class="grid grid-cols-2 gap-6">
                <x-form.field name="credit_days" label="Credit period (days)" type="number" :value="$supplier->credit_days" />
                <div class="flex items-center">
                    <x-form.checkbox name="is_active" label="Active" :checked="$supplier->is_active" />
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Bank details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="bank_account_name" label="Account holder name" :value="$supplier->bank_account_name" />
                </div>
                <x-form.field name="bank_account_number" label="Account number" :value="$supplier->bank_account_number" />
                <x-form.field name="bank_ifsc" label="IFSC code" :value="$supplier->bank_ifsc" />
            </div>
        </div>

        <div class="flex gap-3 border-t border-border pt-6">
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Save changes
            </button>
        </div>
    </form>
</x-layouts.app>

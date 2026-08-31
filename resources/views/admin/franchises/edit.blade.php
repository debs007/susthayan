<x-layouts.app title="Edit {{ $franchise->name }}">
    <a href="{{ route('admin.franchises.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to franchises
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl space-y-6">
        {{-- General details --}}
        <form method="POST" action="{{ route('admin.franchises.update', $franchise) }}" class="space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
            @csrf
            @method('PATCH')

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <x-form.field name="name" label="Franchise name" :value="$franchise->name" required />
                    </div>
                    <x-form.field name="phone" label="Phone" :value="$franchise->phone" />
                    <x-form.field name="email" label="Email" type="email" :value="$franchise->email" />
                </div>
            </div>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Compliance</h2>
                <div class="grid grid-cols-2 gap-6">
                    <x-form.field name="gstin" label="GSTIN" :value="$franchise->gstin" hint="15 characters, e.g. 22AAAAA0000A1Z5" />
                    <x-form.field name="drug_license_number" label="Drug license number" :value="$franchise->drug_license_number" />
                </div>
            </div>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Location</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <x-form.textarea name="address" label="Address" :value="$franchise->address" />
                    </div>
                    <x-form.field name="city" label="City" :value="$franchise->city" />
                    <x-form.field name="state" label="State" :value="$franchise->state" />
                    <x-form.field name="pincode" label="Pincode" :value="$franchise->pincode" />
                    <div class="grid grid-cols-2 gap-3">
                        <x-form.field name="latitude" label="Latitude" type="number" step="0.0000001" :value="$franchise->latitude" />
                        <x-form.field name="longitude" label="Longitude" type="number" step="0.0000001" :value="$franchise->longitude" />
                    </div>
                </div>
            </div>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Settlement</h2>
                <div class="grid grid-cols-2 gap-6">
                    <x-form.field
                        name="commission_percentage"
                        label="Commission % (platform's share)"
                        type="number"
                        step="0.01"
                        :value="$franchise->commission_percentage"
                        required
                    />
                    <x-form.select
                        name="status"
                        label="Status"
                        :value="$franchise->status"
                        :options="['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']"
                    />
                </div>
            </div>

            <div class="flex gap-3 border-t border-border pt-6">
                <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Save changes
                </button>
            </div>
        </form>

        {{-- Bank details - a separate form to a separate endpoint on purpose: this is
             what a settlement payout actually reads from, kept as its own explicit,
             auditable action rather than folded into general edits. --}}
        <form method="POST" action="{{ route('admin.franchises.bank-details', $franchise) }}" class="space-y-5 rounded-xl border border-border bg-canvas-raised p-6">
            @csrf
            @method('PATCH')

            <div>
                <h2 class="font-display font-semibold">Bank details</h2>
                <p class="mt-1 text-sm text-ink-muted">Required before a Franchise Settlement payout can be released to this franchise.</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="bank_account_name" label="Account holder name" :value="$franchise->bank_account_name" />
                </div>
                <x-form.field name="bank_account_number" label="Account number" :value="$franchise->bank_account_number" />
                <x-form.field name="bank_ifsc" label="IFSC code" :value="$franchise->bank_ifsc" />
            </div>

            <div class="flex gap-3 border-t border-border pt-6">
                <button type="submit" class="rounded-lg bg-honey-500 px-4 py-2 text-sm font-medium text-white hover:bg-honey-600">
                    Save bank details
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>

<x-layouts.app title="Add Lab Center">
    <a href="{{ route('admin.lab-centers.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Lab Centers
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.lab-centers.store') }}" class="max-w-3xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Center details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Center name" required placeholder="e.g. Susthayan Diagnostics - Salt Lake" />
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

            <label class="mt-4 flex items-start gap-3 rounded-lg border border-border p-4">
                <input type="checkbox" name="offers_home_collection" value="1" class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                <span>
                    <span class="block text-sm font-medium text-ink">Offers home sample collection</span>
                    <span class="block text-xs text-ink-muted">If checked, this center appears as an option for home-collection tests too - not just center-visit ones. Customers choose which center processes their sample even for a home visit.</span>
                </span>
            </label>
        </div>

        <div>
            <h2 class="mb-2 border-b border-border pb-3 font-display font-semibold">Tests offered</h2>
            <p class="mb-4 text-xs text-ink-muted">Check every test this center can actually perform, and set this center's price for each. An unchecked test never appears as an option for this center in the app - e.g. leave X-Ray unchecked if this center has no X-ray machine.</p>

            @php $groupedTests = $tests->groupBy(fn ($t) => $t->category->name); @endphp
            <div class="space-y-5">
                @foreach ($groupedTests as $categoryName => $categoryTests)
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $categoryName }}</p>
                        <div class="space-y-2">
                            @foreach ($categoryTests as $test)
                                <div class="flex items-center gap-3 rounded-lg border border-border p-3">
                                    <input
                                        type="checkbox"
                                        name="tests[{{ $test->id }}][selected]"
                                        value="1"
                                        class="test-checkbox h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500"
                                        data-price-input="price-{{ $test->id }}"
                                    >
                                    <span class="flex-1 text-sm">{{ $test->name }}</span>
                                    <span class="text-xs text-ink-muted">₹</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="tests[{{ $test->id }}][price]"
                                        id="price-{{ $test->id }}"
                                        value="{{ $test->price }}"
                                        disabled
                                        class="w-28 rounded-lg border border-border bg-canvas px-2 py-1.5 text-sm disabled:bg-canvas disabled:text-ink-muted"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Add center
        </button>
    </form>

    <script>
        // Price input only usable when its test is actually checked -
        // avoids submitting a price for a test this center doesn't offer.
        document.querySelectorAll('.test-checkbox').forEach((checkbox) => {
            const priceInput = document.getElementById(checkbox.dataset.priceInput);
            checkbox.addEventListener('change', () => {
                priceInput.disabled = !checkbox.checked;
            });
        });
    </script>
</x-layouts.app>

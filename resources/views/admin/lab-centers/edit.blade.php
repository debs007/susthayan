<x-layouts.app title="Edit - {{ $center->name }}">
    <a href="{{ route('admin.lab-centers.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Lab Centers
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @php $currentTestIds = $center->tests->pluck('id', 'id'); @endphp

    <form method="POST" action="{{ route('admin.lab-centers.update', $center) }}" class="max-w-3xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf
        @method('PATCH')

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Center details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Center name" required :value="old('name', $center->name)" />
                </div>
                <x-form.select name="franchise_id" label="Operated by (franchise)" required :value="old('franchise_id', $center->franchise_id)" :options="$franchises->pluck('name', 'id')" />
                <x-form.field name="phone" label="Phone" :value="old('phone', $center->phone)" />
                <div class="col-span-2">
                    <x-form.field name="address" label="Address" required :value="old('address', $center->address)" />
                </div>
                <x-form.field name="city" label="City" :value="old('city', $center->city)" />
                <x-form.field name="state" label="State" :value="old('state', $center->state)" />
                <x-form.field name="pincode" label="Pincode" :value="old('pincode', $center->pincode)" />
            </div>

            <label class="mt-4 flex items-start gap-3 rounded-lg border border-border p-4">
                <input type="checkbox" name="offers_home_collection" value="1" @checked(old('offers_home_collection', $center->offers_home_collection)) class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500">
                <span>
                    <span class="block text-sm font-medium text-ink">Offers home sample collection</span>
                    <span class="block text-xs text-ink-muted">If checked, this center appears as an option for home-collection tests too - not just center-visit ones.</span>
                </span>
            </label>
        </div>

        <div>
            <h2 class="mb-2 border-b border-border pb-3 font-display font-semibold">Tests offered</h2>
            <p class="mb-4 text-xs text-ink-muted">Check every test this center can actually perform, and set this center's price for each.</p>

            @php $groupedTests = $tests->groupBy(fn ($t) => $t->category->name); @endphp
            <div class="space-y-5">
                @foreach ($groupedTests as $categoryName => $categoryTests)
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $categoryName }}</p>
                        <div class="space-y-2">
                            @foreach ($categoryTests as $test)
                                @php $isSelected = isset($currentTestIds[$test->id]); @endphp
                                <div class="flex items-center gap-3 rounded-lg border border-border p-3">
                                    <input
                                        type="checkbox"
                                        name="tests[{{ $test->id }}][selected]"
                                        value="1"
                                        @checked($isSelected)
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
                                        value="{{ $isSelected ? $center->tests->find($test->id)->pivot->price : $test->price }}"
                                        @disabled(! $isSelected)
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
            Save changes
        </button>
    </form>

    <script>
        document.querySelectorAll('.test-checkbox').forEach((checkbox) => {
            const priceInput = document.getElementById(checkbox.dataset.priceInput);
            checkbox.addEventListener('change', () => {
                priceInput.disabled = !checkbox.checked;
            });
        });
    </script>
</x-layouts.app>

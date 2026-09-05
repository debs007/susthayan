<x-layouts.app title="Add Doctor">
    <a href="{{ route('admin.doctors.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Doctors
    </a>

    <x-form.errors />

    <form method="POST" action="{{ route('admin.doctors.store') }}" enctype="multipart/form-data" class="max-w-3xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        @csrf

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Doctor details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <x-form.field name="name" label="Full name" required placeholder="e.g. Dr. Anita Sharma" />
                </div>
                <x-form.select name="department_id" label="Department" required :options="$departments->pluck('name', 'id')" />
                <x-form.field name="degree" label="Degree" required placeholder="e.g. MBBS, MD (Cardiology)" />
                <x-form.field name="years_of_experience" label="Years of experience" type="number" />
                <div>
                    <label for="photo" class="block text-sm font-medium text-ink">Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
                </div>
                <div class="col-span-2">
                    <x-form.textarea name="bio" label="Short bio" :rows="2" />
                </div>
            </div>
        </div>

        <div>
            <h2 class="mb-2 border-b border-border pb-3 font-display font-semibold">Hospital affiliations</h2>
            <p class="mb-4 text-xs text-ink-muted">Check every hospital this doctor visits, and set the charge and schedule for each one - a doctor can charge and visit differently at each hospital.</p>

            <div class="space-y-4">
                @foreach ($hospitals as $hospital)
                    <div class="rounded-lg border border-border p-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" class="hospital-checkbox h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500" data-hospital-fields="hospital-fields-{{ $hospital->id }}" name="hospitals[{{ $hospital->id }}][selected]" value="1">
                            <span class="font-medium text-sm">{{ $hospital->name }}</span>
                            <span class="text-xs text-ink-muted">{{ $hospital->city }}</span>
                        </label>

                        <div id="hospital-fields-{{ $hospital->id }}" class="mt-3 space-y-3 opacity-40">
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs text-ink-muted">Consultation charge (₹)</label>
                                    <input type="number" step="0.01" name="hospitals[{{ $hospital->id }}][charge]" disabled class="hospital-input mt-1 w-full rounded-lg border border-border bg-canvas px-2.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-ink-muted">Visit start time</label>
                                    <input type="time" name="hospitals[{{ $hospital->id }}][start_time]" disabled class="hospital-input mt-1 w-full rounded-lg border border-border bg-canvas px-2.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-ink-muted">Visit end time</label>
                                    <input type="time" name="hospitals[{{ $hospital->id }}][end_time]" disabled class="hospital-input mt-1 w-full rounded-lg border border-border bg-canvas px-2.5 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-ink-muted">Visit days</label>
                                <div class="mt-1.5 flex flex-wrap gap-3">
                                    @foreach (['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'] as $value => $label)
                                        <label class="flex items-center gap-1.5 text-xs">
                                            <input type="checkbox" disabled class="hospital-input h-3.5 w-3.5 rounded border-border text-primary-500 focus:ring-primary-500" name="hospitals[{{ $hospital->id }}][days][]" value="{{ $value }}">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Add doctor
        </button>
    </form>

    <script>
        // Fields only usable once their hospital is actually checked -
        // avoids submitting a charge/schedule for a hospital this doctor
        // isn't affiliated with.
        document.querySelectorAll('.hospital-checkbox').forEach((checkbox) => {
            const fieldsContainer = document.getElementById(checkbox.dataset.hospitalFields);
            const inputs = fieldsContainer.querySelectorAll('.hospital-input');
            checkbox.addEventListener('change', () => {
                inputs.forEach((input) => { input.disabled = !checkbox.checked; });
                fieldsContainer.style.opacity = checkbox.checked ? '1' : '0.4';
            });
        });
    </script>
</x-layouts.app>

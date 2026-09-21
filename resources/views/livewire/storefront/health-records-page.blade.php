<div class="mx-auto max-w-4xl px-6 py-10">
    <h1 class="font-display text-2xl font-medium text-ink">Health Records</h1>

    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- Vitals --}}
        <div class="rounded-2xl border border-line bg-surface p-5">
            <h2 class="font-display text-lg font-medium text-ink">Vitals</h2>
            <div class="mt-3 space-y-3">
                @forelse ($vitals as $vital)
                    <div class="border-b border-line pb-2 text-sm last:border-0">
                        <p class="text-xs text-ink-faint">{{ $vital->recorded_at->format('d M Y, h:i A') }}</p>
                        <p class="mt-1 text-ink-soft">
                            @if ($vital->heart_rate_bpm) HR: {{ $vital->heart_rate_bpm }} bpm &bull; @endif
                            @if ($vital->blood_pressure_label) BP: {{ $vital->blood_pressure_label }} &bull; @endif
                            @if ($vital->spo2_percentage) SpO2: {{ $vital->spo2_percentage }}% @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft">No vitals logged yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Records --}}
        <div class="rounded-2xl border border-line bg-surface p-5">
            <h2 class="font-display text-lg font-medium text-ink">Lab Reports &amp; Documents</h2>
            <div class="mt-3 space-y-3">
                @forelse ($records as $record)
                    <div class="border-b border-line pb-2 text-sm last:border-0">
                        <p class="font-medium text-ink">{{ $record->title }}</p>
                        <p class="text-xs text-ink-faint">{{ ucfirst(str_replace('_', ' ', $record->type)) }} &bull; {{ $record->record_date->format('d M Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft">No records uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Prescriptions --}}
    <div class="mt-6 rounded-2xl border border-line bg-surface p-5">
        <h2 class="font-display text-lg font-medium text-ink">Prescriptions</h2>

        <form wire:submit="uploadPrescription" class="mt-4 flex items-center gap-3">
            <input type="file" wire:model="prescriptionFile" accept=".jpg,.jpeg,.png,.pdf" class="text-sm">
            <button type="submit" wire:loading.attr="disabled" wire:target="prescriptionFile,uploadPrescription" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">
                <span wire:loading.remove wire:target="uploadPrescription">Upload</span>
                <span wire:loading wire:target="uploadPrescription">Uploading&hellip;</span>
            </button>
        </form>
        @error('prescriptionFile') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
        @if ($uploadMessage)
            <p class="mt-2 text-sm text-success">{{ $uploadMessage }}</p>
        @endif

        <div class="mt-5 space-y-3">
            @forelse ($prescriptions as $prescription)
                <div class="flex items-center justify-between border-b border-line pb-2 text-sm last:border-0">
                    <p class="text-xs text-ink-faint">{{ $prescription->created_at->format('d M Y, h:i A') }}</p>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $prescription->verification_status === 'approved' ? 'bg-success/10 text-success' : ($prescription->verification_status === 'rejected' ? 'bg-danger/10 text-danger' : 'bg-mist text-ink-soft') }}">
                        {{ ucfirst($prescription->verification_status) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-ink-soft">No prescriptions uploaded yet.</p>
            @endforelse
        </div>
    </div>
</div>

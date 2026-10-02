<div>
    <a href="{{ route('admin.prescriptions.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to prescriptions
    </a>

    <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
        <h1 class="font-display text-lg font-semibold">Create order from prescription</h1>
        <p class="mt-1 text-sm text-ink-muted">
            {{ $prescription->user->name }} &bull; +91 {{ $prescription->user->mobile }} &bull;
            approved {{ $prescription->verified_at?->format('d M Y') }}
        </p>
    </div>

    @if ($errorMessage)
        <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600">{{ $errorMessage }}</div>
    @endif

    <div class="rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Items</h2>

        <div class="space-y-3">
            @foreach ($lines as $index => $line)
                <div class="rounded-lg border border-border p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            @if ($line['medicine_name'])
                                <p class="text-xs text-ink-muted">Transcribed: {{ $line['medicine_name'] }}</p>
                            @endif

                            @if ($line['product_id'])
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600">{{ $line['product_name'] }}</span>
                                    <button type="button" wire:click="clearProduct({{ $index }})" class="text-xs text-ink-muted hover:text-ink">Change</button>
                                </div>
                            @else
                                <div class="relative mt-1">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="lines.{{ $index }}.product_search"
                                        placeholder="Search for a product to match..."
                                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                                    >
                                    @if (! empty($line['results']))
                                        <div class="absolute z-10 mt-1 w-full rounded-lg border border-border bg-canvas-raised shadow-lg">
                                            @foreach ($line['results'] as $result)
                                                <button
                                                    type="button"
                                                    wire:click="selectProduct({{ $index }}, {{ $result['id'] }}, '{{ addslashes($result['name']) }}')"
                                                    class="block w-full px-3 py-2 text-left text-sm hover:bg-canvas"
                                                >{{ $result['name'] }}</button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="w-20">
                            <label class="mb-1 block text-xs text-ink-muted">Qty</label>
                            <input
                                type="number"
                                min="1"
                                wire:model="lines.{{ $index }}.quantity"
                                class="w-full rounded-lg border border-border px-2 py-1.5 text-sm focus:border-primary-500 focus:outline-none"
                            >
                        </div>

                        @if (count($lines) > 1)
                            <button type="button" wire:click="removeLine({{ $index }})" class="mt-5 text-ink-muted hover:text-danger-500">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" wire:click="addLine" class="mt-3 text-sm font-medium text-primary-500 hover:text-primary-600">+ Add another line</button>
    </div>

    <div class="mt-6 rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Fulfillment</h2>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" wire:model.live="fulfillmentType" value="pickup"> Pickup
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" wire:model.live="fulfillmentType" value="delivery"> Delivery
            </label>
        </div>

        @if ($fulfillmentType === 'delivery')
            <div class="mt-3">
                <label class="mb-1 block text-xs text-ink-muted">Delivery address</label>
                <select wire:model="addressId" class="w-full max-w-md rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    @forelse ($prescription->user->addresses as $address)
                        <option value="{{ $address->id }}">{{ $address->line1 }}, {{ $address->city }}</option>
                    @empty
                        <option value="">No saved address for this customer</option>
                    @endforelse
                </select>
            </div>
        @endif
    </div>

    <button wire:click="submit" class="mt-6 rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
        Create order
    </button>
</div>

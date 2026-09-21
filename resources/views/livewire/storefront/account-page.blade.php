<div class="mx-auto max-w-3xl px-6 py-10">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl font-medium text-ink">Your Account</h1>
        <button wire:click="logout" class="text-sm text-ink-soft hover:text-danger">Log out</button>
    </div>

    {{-- Profile --}}
    <section class="mt-8 rounded-2xl border border-line bg-surface p-5">
        <h2 class="font-display text-lg font-medium text-ink">Profile</h2>
        <form wire:submit="updateProfile" class="mt-4 space-y-3">
            <div>
                <label class="mb-1 block text-xs text-ink-faint">Name</label>
                <input wire:model="name" type="text" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs text-ink-faint">Email</label>
                <input wire:model="email" type="email" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs text-ink-faint">Alternate mobile number</label>
                <input wire:model="alternateMobile" type="tel" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                @error('alternateMobile') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">Save changes</button>
            @if ($profileMessage)
                <span class="ml-3 text-sm text-success">{{ $profileMessage }}</span>
            @endif
        </form>
    </section>

    {{-- Addresses --}}
    <section class="mt-6 rounded-2xl border border-line bg-surface p-5">
        <h2 class="font-display text-lg font-medium text-ink">Saved Addresses</h2>

        <div class="mt-4 space-y-3">
            @forelse ($addresses as $address)
                <div class="flex items-start justify-between rounded-xl border border-line p-4">
                    <div>
                        <p class="text-sm font-medium text-ink">
                            {{ $address->label ?? 'Address' }}
                            @if ($address->is_default)
                                <span class="ml-2 rounded-full bg-mist px-2 py-0.5 text-[11px] font-medium text-forest">Default</span>
                            @endif
                        </p>
                        <p class="mt-1 text-xs text-ink-soft">{{ $address->line1 }}, {{ $address->line2 }}, {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}</p>
                    </div>
                    <div class="flex shrink-0 gap-3">
                        @if (! $address->is_default)
                            <button wire:click="setDefaultAddress({{ $address->id }})" class="text-xs text-brand hover:underline">Set default</button>
                        @endif
                        <button wire:click="deleteAddress({{ $address->id }})" wire:confirm="Remove this address?" class="text-xs text-ink-faint hover:text-danger">Remove</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft">No saved addresses yet.</p>
            @endforelse
        </div>

        @if ($showAddAddress)
            <form wire:submit="saveNewAddress" class="mt-4 space-y-3 rounded-xl border border-line p-4">
                <input wire:model="line1" type="text" placeholder="Address line 1" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                @error('line1') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                <input wire:model="line2" type="text" placeholder="Address line 2 (optional)" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input wire:model="city" type="text" placeholder="City" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                        @error('city') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input wire:model="state" type="text" placeholder="State" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                        @error('state') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>
                <input wire:model="pincode" type="text" placeholder="Pincode" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                @error('pincode') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                <div class="flex gap-3">
                    <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">Save address</button>
                    <button type="button" wire:click="$set('showAddAddress', false)" class="text-sm text-ink-soft hover:text-ink">Cancel</button>
                </div>
            </form>
        @else
            <button wire:click="$set('showAddAddress', true)" class="mt-4 text-sm font-medium text-brand hover:underline">+ Add a new address</button>
        @endif
    </section>

    {{-- Quick links --}}
    <section class="mt-6 grid grid-cols-2 gap-3">
        <a href="{{ route('storefront.orders') }}" wire:navigate class="rounded-xl border border-line bg-surface p-4 text-center text-sm font-medium text-ink hover:border-brand">Your Orders</a>
        <a href="{{ route('storefront.health-records') }}" wire:navigate class="rounded-xl border border-line bg-surface p-4 text-center text-sm font-medium text-ink hover:border-brand">Health Records</a>
    </section>
</div>

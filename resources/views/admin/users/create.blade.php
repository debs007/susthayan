<x-layouts.app title="Add user">
    <a href="{{ route('admin.users.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to users
    </a>

    <x-form.errors />

    <form
        method="POST" action="{{ route('admin.users.store') }}"
        x-data="{ role: '{{ old('role', '') }}', franchiseScoped: ['Franchise Owner', 'Franchise Staff', 'Pharmacist', 'Delivery Agent'] }"
        class="max-w-xl space-y-6 rounded-xl border border-border bg-canvas-raised p-8"
    >
        @csrf

        <x-form.field name="name" label="Full name" required />

        <div class="grid grid-cols-2 gap-6">
            <x-form.field name="mobile" label="Mobile number" required hint="10 digits, no country code." />
            <x-form.field name="email" label="Email (optional)" type="email" />
        </div>

        <x-form.field name="password" label="Initial password" type="password" required hint="Share it with them through a secure channel outside this system - there's no invite-link flow yet." />

        <div>
            <label for="role" class="mb-2 block text-sm font-medium">Role *</label>
            <select name="role" id="role" x-model="role" required class="w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">Select a role</option>
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption }}" @selected(old('role') === $roleOption)>{{ $roleOption }}</option>
                @endforeach
            </select>
            @error('role') <p class="mt-1.5 text-xs text-danger-500">{{ $message }}</p> @enderror
        </div>

        <div x-show="franchiseScoped.includes(role)" x-cloak>
            <x-form.select name="franchise_id" label="Franchise" placeholder="Select a franchise" :options="$franchises->pluck('name', 'id')" />
        </div>

        <div x-show="role === 'Super Admin' || role === 'Accountant'" x-cloak>
            <x-form.checkbox name="two_factor_enabled" label="Require 2FA on every login" hint="Super Admin always requires this regardless of this setting." />
        </div>

        <div class="flex gap-3 border-t border-border pt-6">
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Add user
            </button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-muted hover:bg-canvas">Cancel</a>
        </div>
    </form>
</x-layouts.app>

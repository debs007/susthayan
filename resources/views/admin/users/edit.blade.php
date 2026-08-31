<x-layouts.app title="Edit {{ $user->name }}">
    <a href="{{ route('admin.users.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to users
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-xl space-y-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6 rounded-xl border border-border bg-canvas-raised p-8">
            @csrf
            @method('PATCH')
            <h2 class="border-b border-border pb-3 font-display font-semibold">Profile</h2>

            <x-form.field name="name" label="Full name" :value="$user->name" required />
            <x-form.field name="email" label="Email" type="email" :value="$user->email" />
            <p class="text-xs text-ink-muted">Mobile number can't be changed here - it's the account's sign-in identity.</p>

            <div class="space-y-3">
                <x-form.checkbox name="is_active" label="Active" hint="Turning this off blocks sign-in immediately." :checked="$user->is_active" />
                <x-form.checkbox name="two_factor_enabled" label="Require 2FA on every login" :checked="$user->two_factor_enabled" />
            </div>

            <div class="border-t border-border pt-6">
                <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Save profile</button>
            </div>
        </form>

        <form
            method="POST" action="{{ route('admin.users.role', $user) }}"
            x-data="{ role: '{{ $user->roles->first()?->name }}', franchiseScoped: ['Franchise Owner', 'Franchise Staff', 'Pharmacist', 'Delivery Agent'] }"
            class="space-y-6 rounded-xl border border-border bg-canvas-raised p-8"
        >
            @csrf
            @method('PATCH')
            <h2 class="border-b border-border pb-3 font-display font-semibold">Role</h2>
            <p class="text-sm text-ink-muted">Currently <span class="font-medium text-ink">{{ $user->roles->first()?->name }}</span>{{ $user->franchise ? " at {$user->franchise->name}" : '' }}.</p>

            <div>
                <label for="role_change" class="mb-2 block text-sm font-medium">New role</label>
                <select name="role" id="role_change" x-model="role" class="w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption }}" @selected($user->roles->first()?->name === $roleOption)>{{ $roleOption }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="franchiseScoped.includes(role)" x-cloak>
                <x-form.select name="franchise_id" label="Franchise" placeholder="Select a franchise" :value="$user->franchise_id" :options="$franchises->pluck('name', 'id')" />
            </div>

            <div class="border-t border-border pt-6">
                <button type="submit" class="rounded-lg bg-honey-500 px-4 py-2 text-sm font-medium text-white hover:bg-honey-600">Change role</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-6 rounded-xl border border-border bg-canvas-raised p-8">
            @csrf
            <h2 class="border-b border-border pb-3 font-display font-semibold">Reset password</h2>
            <x-form.field name="password" label="New password" type="password" required hint="Share it with them through a secure channel outside this system." />
            <div class="border-t border-border pt-6">
                <button type="submit" class="rounded-lg border border-danger-500/30 px-4 py-2 text-sm font-medium text-danger-600 hover:bg-danger-50">Reset password</button>
            </div>
        </form>
    </div>
</x-layouts.app>

<x-layouts.guest title="Reset password">
    <h1 class="font-display text-2xl font-semibold text-ink">Reset password</h1>
    <p class="mt-1.5 text-sm text-ink-muted">Enter the code we sent and choose a new password.</p>

    @if (session('info'))
        <div class="mt-6 rounded-lg border border-primary-500/30 bg-primary-50 px-4 py-3 text-sm text-primary-600">
            {{ session('info') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-3 text-sm text-danger-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password-reset.update') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-ink">6-digit code</label>
            <input
                type="text"
                name="code"
                id="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                autofocus
                required
                maxlength="6"
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-center font-code text-lg tracking-[0.3em] text-ink focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                placeholder="------"
            >
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink">New password</label>
            <input
                type="password"
                name="password"
                id="password"
                autocomplete="new-password"
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-ink focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            >
            <p class="mt-1.5 text-xs text-ink-muted">At least 8 characters.</p>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-ink">Confirm new password</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                autocomplete="new-password"
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-ink focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary-500 px-4 py-2.5 font-medium text-white transition hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
        >
            Reset password
        </button>
    </form>

    <p class="mt-8 text-xs text-ink-muted">
        Didn't get a code? <a href="{{ route('forgot-password.show') }}" class="font-medium text-primary-500 hover:underline">Try again</a>
    </p>
</x-layouts.guest>

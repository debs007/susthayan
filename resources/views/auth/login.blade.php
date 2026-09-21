<x-layouts.guest title="Sign in">
    <h1 class="font-display text-2xl font-semibold text-ink">Sign in</h1>
    <p class="mt-1.5 text-sm text-ink-muted">Franchise and admin staff access.</p>

    @if (session('success'))
        <div class="mt-6 rounded-lg border border-success-500/30 bg-success-50 px-4 py-3 text-sm text-success-600">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-3 text-sm text-danger-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="login" class="block text-sm font-medium text-ink">Mobile number or email</label>
            <input
                type="text"
                name="login"
                id="login"
                value="{{ old('login') }}"
                autofocus
                autocomplete="username"
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-ink placeholder:text-ink-muted/60 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                placeholder="9999999999 or you@example.com"
            >
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-ink">Password</label>
                <a href="{{ route('forgot-password.show') }}" class="text-xs font-medium text-primary-500 hover:underline">Forgot password?</a>
            </div>
            <input
                type="password"
                name="password"
                id="password"
                autocomplete="current-password"
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-ink focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary-500 px-4 py-2.5 font-medium text-white transition hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
        >
            Sign in
        </button>
    </form>

    <p class="mt-8 text-xs text-ink-muted">
        Super Admin accounts confirm sign-in with a one-time code sent by SMS.
    </p>
</x-layouts.guest>

<x-layouts.guest title="Forgot password">
    <h1 class="font-display text-2xl font-semibold text-ink">Forgot password</h1>
    <p class="mt-1.5 text-sm text-ink-muted">Enter your mobile number and we'll send a reset code by SMS.</p>

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-3 text-sm text-danger-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('forgot-password.send') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="mobile" class="block text-sm font-medium text-ink">Mobile number</label>
            <input
                type="text"
                name="mobile"
                id="mobile"
                value="{{ old('mobile') }}"
                autofocus
                autocomplete="tel"
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-ink placeholder:text-ink-muted/60 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                placeholder="9999999999"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary-500 px-4 py-2.5 font-medium text-white transition hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
        >
            Send reset code
        </button>
    </form>

    <p class="mt-8 text-xs text-ink-muted">
        <a href="{{ route('login') }}" class="font-medium text-primary-500 hover:underline">Back to sign in</a>
    </p>
</x-layouts.guest>

<x-layouts.guest title="Verify it's you">
    <h1 class="font-display text-2xl font-semibold text-ink">Enter your code</h1>
    <p class="mt-1.5 text-sm text-ink-muted">
        We texted a 6-digit code to the mobile number on file. It expires in 5 minutes.
    </p>

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-3 text-sm text-danger-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ url('/two-factor') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-ink">Verification code</label>
            <input
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                name="code"
                id="code"
                autofocus
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-center font-code text-lg tracking-[0.4em] text-ink focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                placeholder="000000"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary-500 px-4 py-2.5 font-medium text-white transition hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
        >
            Verify and continue
        </button>
    </form>

    <p class="mt-4 text-xs text-ink-muted">
        Wrong account? <a href="{{ route('login') }}" class="font-medium text-primary-500 hover:underline">Start over</a>.
    </p>
</x-layouts.guest>

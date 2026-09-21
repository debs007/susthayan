<div class="mx-auto max-w-3xl px-6 py-10" x-data
     x-on:payment-ready.window="
        var rzp = new Razorpay({
            key: $wire.razorpayKey,
            amount: $wire.amountInPaise,
            currency: 'INR',
            order_id: $wire.razorpayOrderId,
            name: 'Susthayan',
            description: 'Lab Test Booking #' + $wire.orderId,
            handler: function (response) {
                $wire.verifyPayment(response.razorpay_order_id, response.razorpay_payment_id, response.razorpay_signature);
            },
            theme: { color: '#208060' },
        });
        rzp.open();
     ">
    <h1 class="font-display text-2xl font-medium text-ink">Book a Lab Test</h1>

    @if ($errorMessage)
        <p class="mt-4 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">{{ $errorMessage }}</p>
    @endif

    {{-- Step: choose test --}}
    @if ($step === 'test')
        <div class="mt-6 space-y-8">
            @forelse ($categories as $category)
                @if ($category->labTests->isNotEmpty())
                    <div>
                        <h2 class="font-display text-lg font-medium text-ink">{{ $category->name }}</h2>
                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($category->labTests as $test)
                                <button wire:click="selectTest({{ $test->id }})" class="flex items-center justify-between rounded-xl border border-line bg-surface p-4 text-left hover:border-brand">
                                    <span>
                                        <span class="block text-sm font-medium text-ink">{{ $test->name }}</span>
                                        @if ($test->sample_type)
                                            <span class="block text-xs text-ink-faint">{{ $test->sample_type }}</span>
                                        @endif
                                    </span>
                                    <span class="text-sm font-semibold text-ink">₹{{ $test->price }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-sm text-ink-soft">No lab tests are available right now.</p>
            @endforelse
        </div>
    @endif

    {{-- Step: choose center --}}
    @if ($step === 'center' && $selectedTest)
        <div class="mt-6">
            <button wire:click="backTo('test')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change test</button>
            <p class="text-sm text-ink-soft">{{ $selectedTest->name }}</p>
            <h2 class="mt-1 font-display text-lg font-medium text-ink">Choose a {{ $selectedTest->requires_center_visit ? 'center' : 'home collection center' }}</h2>

            <div class="mt-4 space-y-3">
                @forelse ($centers as $center)
                    <button wire:click="selectCenter({{ $center->id }})" class="flex w-full items-center justify-between rounded-xl border border-line bg-surface p-4 text-left hover:border-brand">
                        <span>
                            <span class="block text-sm font-medium text-ink">{{ $center->name }}</span>
                            <span class="block text-xs text-ink-faint">{{ $center->fullAddress() }}</span>
                        </span>
                        <span class="text-sm font-semibold text-ink">₹{{ $center->tests->firstWhere('id', $selectedTest->id)?->pivot->price ?? $selectedTest->price }}</span>
                    </button>
                @empty
                    <p class="text-sm text-ink-soft">No centers currently offer this test.</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Step: date + confirm --}}
    @if ($step === 'date' && $selectedTest)
        <div class="mt-6">
            <button wire:click="backTo('center')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change center</button>
            <h2 class="font-display text-lg font-medium text-ink">Choose a date</h2>

            <input wire:model="scheduledDate" type="date" min="{{ now()->toDateString() }}" class="mt-3 rounded-lg border border-line px-4 py-2.5 text-sm outline-none focus:border-brand">

            <button
                wire:click="confirmBooking"
                wire:loading.attr="disabled"
                wire:target="confirmBooking"
                class="mt-6 block w-full rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover disabled:bg-ink-faint"
            >
                <span wire:loading.remove wire:target="confirmBooking">Book &amp; Pay</span>
                <span wire:loading wire:target="confirmBooking">Booking&hellip;</span>
            </button>
        </div>
    @endif
</div>

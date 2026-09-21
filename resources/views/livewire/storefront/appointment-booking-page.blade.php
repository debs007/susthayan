<div class="mx-auto max-w-3xl px-6 py-10" x-data
     x-on:payment-ready.window="
        var rzp = new Razorpay({
            key: $wire.razorpayKey,
            amount: $wire.amountInPaise,
            currency: 'INR',
            order_id: $wire.razorpayOrderId,
            name: 'Susthayan',
            description: 'Appointment Booking #' + $wire.orderId,
            handler: function (response) {
                $wire.verifyPayment(response.razorpay_order_id, response.razorpay_payment_id, response.razorpay_signature);
            },
            theme: { color: '#208060' },
        });
        rzp.open();
     ">
    <h1 class="font-display text-2xl font-medium text-ink">Book a Doctor</h1>

    @if ($errorMessage)
        <p class="mt-4 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">{{ $errorMessage }}</p>
    @endif

    {{-- Step: department --}}
    @if ($step === 'department')
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
            @forelse ($departments as $department)
                <button wire:click="selectDepartment({{ $department->id }})" class="rounded-xl border border-line bg-surface p-4 text-center hover:border-brand">
                    <span class="block text-sm font-medium text-ink">{{ $department->name }}</span>
                    <span class="block text-xs text-ink-faint">{{ $department->doctors_count }} doctors</span>
                </button>
            @empty
                <p class="col-span-full text-sm text-ink-soft">No departments are available right now.</p>
            @endforelse
        </div>
    @endif

    {{-- Step: doctor --}}
    @if ($step === 'doctor')
        <div class="mt-6">
            <button wire:click="backTo('department')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change department</button>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @forelse ($doctors as $doctor)
                    <button wire:click="selectDoctor({{ $doctor->id }})" class="flex flex-col items-center gap-2 rounded-xl border border-line bg-surface p-4 text-center hover:border-brand">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-mist">
                            @if ($doctor->photo_url)
                                <img src="{{ $doctor->photo_url }}" alt="" class="h-full w-full object-cover">
                            @else
                                <span class="text-lg text-ink-faint">{{ substr($doctor->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <span class="line-clamp-2 text-sm font-medium leading-snug text-ink">{{ $doctor->name }}</span>
                        <span class="text-xs text-ink-faint">{{ $doctor->degree }}</span>
                        <span class="text-xs text-ink-faint">{{ $doctor->years_of_experience }} yrs experience</span>
                    </button>
                @empty
                    <p class="col-span-full text-sm text-ink-soft">No doctors in this department right now.</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Step: hospital / affiliation --}}
    @if ($step === 'hospital' && $selectedDoctor)
        <div class="mt-6">
            <button wire:click="backTo('doctor')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change doctor</button>
            <p class="text-sm text-ink-soft">{{ $selectedDoctor->name }} &bull; {{ $selectedDoctor->degree }}</p>
            <h2 class="mt-1 font-display text-lg font-medium text-ink">Choose a hospital</h2>

            <div class="mt-4 space-y-3">
                @forelse ($affiliations as $affiliation)
                    @php
                        $availableDays = $affiliation->visitDays->pluck('day_of_week')->all();
                        $weekDays = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
                    @endphp
                    <button wire:click="selectAffiliation({{ $affiliation->id }})" class="block w-full rounded-xl border border-line bg-surface p-4 text-left hover:border-brand">
                        <div class="flex items-center justify-between">
                            <span>
                                <span class="block text-sm font-medium text-ink">{{ $affiliation->hospital->name }}</span>
                                <span class="block text-xs text-ink-faint">{{ $affiliation->hospital->fullAddress() }}</span>
                            </span>
                            <span class="text-sm font-semibold text-ink">₹{{ $affiliation->consultation_charge }}</span>
                        </div>
                        {{-- At-a-glance weekly pattern - which days this doctor actually visits this hospital --}}
                        <div class="mt-3 flex gap-1">
                            @foreach ($weekDays as $key => $label)
                                <span class="flex h-6 flex-1 items-center justify-center rounded text-[10px] font-medium {{ in_array($key, $availableDays) ? 'bg-brand text-white' : 'bg-canvas text-ink-faint' }}">
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </button>
                @empty
                    <p class="text-sm text-ink-soft">This doctor isn't affiliated with any hospital right now.</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Step: date + confirm --}}
    @if ($step === 'date')
        <div class="mt-6">
            <button wire:click="backTo('hospital')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change hospital</button>
            <h2 class="font-display text-lg font-medium text-ink">Choose a date</h2>
            <p class="mt-1 text-xs text-ink-faint">Only days this doctor visits the hospital will be accepted.</p>

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

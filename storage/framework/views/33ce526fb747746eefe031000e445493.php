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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
        <p class="mt-4 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger"><?php echo e($errorMessage); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'department'): ?>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button wire:click="selectDepartment(<?php echo e($department->id); ?>)" class="rounded-xl border border-line bg-surface p-4 text-center hover:border-brand">
                    <span class="block text-sm font-medium text-ink"><?php echo e($department->name); ?></span>
                    <span class="block text-xs text-ink-faint"><?php echo e($department->doctors_count); ?> doctors</span>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="col-span-full text-sm text-ink-soft">No departments are available right now.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'doctor'): ?>
        <div class="mt-6">
            <button wire:click="backTo('department')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change department</button>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button wire:click="selectDoctor(<?php echo e($doctor->id); ?>)" class="flex flex-col items-center gap-2 rounded-xl border border-line bg-surface p-4 text-center hover:border-brand">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-mist">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doctor->photo_url): ?>
                                <img src="<?php echo e($doctor->photo_url); ?>" alt="" class="h-full w-full object-cover">
                            <?php else: ?>
                                <span class="text-lg text-ink-faint"><?php echo e(substr($doctor->name, 0, 1)); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <span class="line-clamp-2 text-sm font-medium leading-snug text-ink"><?php echo e($doctor->name); ?></span>
                        <span class="text-xs text-ink-faint"><?php echo e($doctor->degree); ?></span>
                        <span class="text-xs text-ink-faint"><?php echo e($doctor->years_of_experience); ?> yrs experience</span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="col-span-full text-sm text-ink-soft">No doctors in this department right now.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'hospital' && $selectedDoctor): ?>
        <div class="mt-6">
            <button wire:click="backTo('doctor')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change doctor</button>
            <p class="text-sm text-ink-soft"><?php echo e($selectedDoctor->name); ?> &bull; <?php echo e($selectedDoctor->degree); ?></p>
            <h2 class="mt-1 font-display text-lg font-medium text-ink">Choose a hospital</h2>

            <div class="mt-4 space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $affiliations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $affiliation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $availableDays = $affiliation->visitDays->pluck('day_of_week')->all();
                        $weekDays = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
                    ?>
                    <button wire:click="selectAffiliation(<?php echo e($affiliation->id); ?>)" class="block w-full rounded-xl border border-line bg-surface p-4 text-left hover:border-brand">
                        <div class="flex items-center justify-between">
                            <span>
                                <span class="block text-sm font-medium text-ink"><?php echo e($affiliation->hospital->name); ?></span>
                                <span class="block text-xs text-ink-faint"><?php echo e($affiliation->hospital->fullAddress()); ?></span>
                            </span>
                            <span class="text-sm font-semibold text-ink">₹<?php echo e($affiliation->consultation_charge); ?></span>
                        </div>
                        
                        <div class="mt-3 flex gap-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <span class="flex h-6 flex-1 items-center justify-center rounded text-[10px] font-medium <?php echo e(in_array($key, $availableDays) ? 'bg-brand text-white' : 'bg-canvas text-ink-faint'); ?>">
                                    <?php echo e($label); ?>

                                </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="text-sm text-ink-soft">This doctor isn't affiliated with any hospital right now.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'date'): ?>
        <div class="mt-6">
            <button wire:click="backTo('hospital')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change hospital</button>
            <h2 class="font-display text-lg font-medium text-ink">Choose a date</h2>
            <p class="mt-1 text-xs text-ink-faint">Only days this doctor visits the hospital will be accepted.</p>

            <input wire:model="scheduledDate" type="date" min="<?php echo e(now()->toDateString()); ?>" class="mt-3 rounded-lg border border-line px-4 py-2.5 text-sm outline-none focus:border-brand">

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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/appointment-booking-page.blade.php ENDPATH**/ ?>
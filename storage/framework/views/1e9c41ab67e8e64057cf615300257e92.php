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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
        <p class="mt-4 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger"><?php echo e($errorMessage); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'test'): ?>
        <div class="mt-6 space-y-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->labTests->isNotEmpty()): ?>
                    <div>
                        <h2 class="font-display text-lg font-medium text-ink"><?php echo e($category->name); ?></h2>
                        <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $category->labTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button wire:click="selectTest(<?php echo e($test->id); ?>)" class="flex items-center justify-between rounded-xl border border-line bg-surface p-4 text-left hover:border-brand">
                                    <span>
                                        <span class="block text-sm font-medium text-ink"><?php echo e($test->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->sample_type): ?>
                                            <span class="block text-xs text-ink-faint"><?php echo e($test->sample_type); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                    <span class="text-sm font-semibold text-ink">₹<?php echo e($test->price); ?></span>
                                </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-sm text-ink-soft">No lab tests are available right now.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'center' && $selectedTest): ?>
        <div class="mt-6">
            <button wire:click="backTo('test')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change test</button>
            <p class="text-sm text-ink-soft"><?php echo e($selectedTest->name); ?></p>
            <h2 class="mt-1 font-display text-lg font-medium text-ink">Choose a <?php echo e($selectedTest->requires_center_visit ? 'center' : 'home collection center'); ?></h2>

            <div class="mt-4 space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $centers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $center): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button wire:click="selectCenter(<?php echo e($center->id); ?>)" class="flex w-full items-center justify-between rounded-xl border border-line bg-surface p-4 text-left hover:border-brand">
                        <span>
                            <span class="block text-sm font-medium text-ink"><?php echo e($center->name); ?></span>
                            <span class="block text-xs text-ink-faint"><?php echo e($center->fullAddress()); ?></span>
                        </span>
                        <span class="text-sm font-semibold text-ink">₹<?php echo e($center->tests->firstWhere('id', $selectedTest->id)?->pivot->price ?? $selectedTest->price); ?></span>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="text-sm text-ink-soft">No centers currently offer this test.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'date' && $selectedTest): ?>
        <div class="mt-6">
            <button wire:click="backTo('center')" class="mb-4 text-sm text-ink-soft hover:text-brand">&larr; Change center</button>
            <h2 class="font-display text-lg font-medium text-ink">Choose a date</h2>

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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/lab-test-booking-page.blade.php ENDPATH**/ ?>
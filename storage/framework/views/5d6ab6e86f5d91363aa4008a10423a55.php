<div class="mx-auto flex min-h-[70vh] max-w-md flex-col justify-center px-6 py-16">
    <p class="font-display text-3xl font-semibold text-forest">Susthayan</p>
    <h1 class="mt-6 font-display text-2xl font-medium text-ink">
        <?php echo e($step === 'mobile' ? 'Log in or sign up' : 'Enter the code we sent you'); ?>

    </h1>
    <p class="mt-2 text-sm text-ink-soft">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'mobile'): ?>
            We'll text you a one-time code - no password to remember.
        <?php else: ?>
            Sent to +91 <?php echo e($mobile); ?>.
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
        <p class="mt-6 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger"><?php echo e($errorMessage); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step === 'mobile'): ?>
        <form wire:submit="requestOtp" class="mt-8 space-y-4">
            <div class="flex items-center rounded-xl border border-line px-4 py-3 focus-within:border-brand">
                <span class="mr-2 text-sm text-ink-soft">+91</span>
                <input
                    wire:model="mobile"
                    type="tel"
                    inputmode="numeric"
                    maxlength="10"
                    placeholder="10-digit mobile number"
                    class="w-full bg-transparent text-sm outline-none"
                    autofocus
                >
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-sm text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <button type="submit" class="w-full rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover" wire:loading.attr="disabled" wire:target="requestOtp">
                <span wire:loading.remove wire:target="requestOtp">Send OTP</span>
                <span wire:loading wire:target="requestOtp">Sending code&hellip;</span>
            </button>
        </form>
    <?php else: ?>
        <form wire:submit="verifyOtp" class="mt-8 space-y-4">
            <input
                wire:model="code"
                type="text"
                inputmode="numeric"
                placeholder="Enter OTP"
                class="w-full rounded-xl border border-line px-4 py-3 text-center text-lg tracking-[0.3em] outline-none focus:border-brand"
                autofocus
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-sm text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <input
                wire:model="name"
                type="text"
                placeholder="Your name (only needed the first time)"
                class="w-full rounded-xl border border-line px-4 py-3 text-sm outline-none focus:border-brand"
            >

            <button type="submit" class="w-full rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover" wire:loading.attr="disabled" wire:target="verifyOtp">
                <span wire:loading.remove wire:target="verifyOtp">Verify &amp; Continue</span>
                <span wire:loading wire:target="verifyOtp">Verifying&hellip;</span>
            </button>

            <button type="button" wire:click="changeNumber" class="w-full text-center text-sm text-ink-soft hover:text-brand">
                Change mobile number
            </button>
        </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/login.blade.php ENDPATH**/ ?>
<div class="mx-auto max-w-3xl px-6 py-10">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl font-medium text-ink">Your Account</h1>
        <button wire:click="logout" class="text-sm text-ink-soft hover:text-danger">Log out</button>
    </div>

    
    <section class="mt-8 rounded-2xl border border-line bg-surface p-5">
        <h2 class="font-display text-lg font-medium text-ink">Profile</h2>
        <form wire:submit="updateProfile" class="mt-4 space-y-3">
            <div>
                <label class="mb-1 block text-xs text-ink-faint">Name</label>
                <input wire:model="name" type="text" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="mb-1 block text-xs text-ink-faint">Email</label>
                <input wire:model="email" type="email" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="mb-1 block text-xs text-ink-faint">Alternate mobile number</label>
                <input wire:model="alternateMobile" type="tel" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alternateMobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">Save changes</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileMessage): ?>
                <span class="ml-3 text-sm text-success"><?php echo e($profileMessage); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </section>

    
    <section class="mt-6 rounded-2xl border border-line bg-surface p-5">
        <h2 class="font-display text-lg font-medium text-ink">Saved Addresses</h2>

        <div class="mt-4 space-y-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex items-start justify-between rounded-xl border border-line p-4">
                    <div>
                        <p class="text-sm font-medium text-ink">
                            <?php echo e($address->label ?? 'Address'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address->is_default): ?>
                                <span class="ml-2 rounded-full bg-mist px-2 py-0.5 text-[11px] font-medium text-forest">Default</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                        <p class="mt-1 text-xs text-ink-soft"><?php echo e($address->line1); ?>, <?php echo e($address->line2); ?>, <?php echo e($address->city); ?>, <?php echo e($address->state); ?> - <?php echo e($address->pincode); ?></p>
                    </div>
                    <div class="flex shrink-0 gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $address->is_default): ?>
                            <button wire:click="setDefaultAddress(<?php echo e($address->id); ?>)" class="text-xs text-brand hover:underline">Set default</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button wire:click="deleteAddress(<?php echo e($address->id); ?>)" wire:confirm="Remove this address?" class="text-xs text-ink-faint hover:text-danger">Remove</button>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-sm text-ink-soft">No saved addresses yet.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddAddress): ?>
            <form wire:submit="saveNewAddress" class="mt-4 space-y-3 rounded-xl border border-line p-4">
                <input wire:model="line1" type="text" placeholder="Address line 1" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['line1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <input wire:model="line2" type="text" placeholder="Address line 2 (optional)" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input wire:model="city" type="text" placeholder="City" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <input wire:model="state" type="text" placeholder="State" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <input wire:model="pincode" type="text" placeholder="Pincode" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['pincode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">Save address</button>
                    <button type="button" wire:click="$set('showAddAddress', false)" class="text-sm text-ink-soft hover:text-ink">Cancel</button>
                </div>
            </form>
        <?php else: ?>
            <button wire:click="$set('showAddAddress', true)" class="mt-4 text-sm font-medium text-brand hover:underline">+ Add a new address</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    
    <section class="mt-6 grid grid-cols-2 gap-3">
        <a href="<?php echo e(route('storefront.orders')); ?>" wire:navigate class="rounded-xl border border-line bg-surface p-4 text-center text-sm font-medium text-ink hover:border-brand">Your Orders</a>
        <a href="<?php echo e(route('storefront.health-records')); ?>" wire:navigate class="rounded-xl border border-line bg-surface p-4 text-center text-sm font-medium text-ink hover:border-brand">Health Records</a>
    </section>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/account-page.blade.php ENDPATH**/ ?>
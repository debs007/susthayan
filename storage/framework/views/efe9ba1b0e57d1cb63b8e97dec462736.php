<div>
    <a href="<?php echo e(route('admin.prescriptions.index')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to prescriptions
    </a>

    <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
        <h1 class="font-display text-lg font-semibold">Create order from prescription</h1>
        <p class="mt-1 text-sm text-ink-muted">
            <?php echo e($prescription->user->name); ?> &bull; +91 <?php echo e($prescription->user->mobile); ?> &bull;
            approved <?php echo e($prescription->verified_at?->format('d M Y')); ?>

        </p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
        <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600"><?php echo e($errorMessage); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Items</h2>

        <div class="space-y-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="rounded-lg border border-border p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line['medicine_name']): ?>
                                <p class="text-xs text-ink-muted">Transcribed: <?php echo e($line['medicine_name']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line['product_id']): ?>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600"><?php echo e($line['product_name']); ?></span>
                                    <button type="button" wire:click="clearProduct(<?php echo e($index); ?>)" class="text-xs text-ink-muted hover:text-ink">Change</button>
                                </div>
                            <?php else: ?>
                                <div class="relative mt-1">
                                    <input
                                        type="text"
                                        wire:model="lines.<?php echo e($index); ?>.product_search"
                                        wire:keyup.debounce.300ms="searchProduct(<?php echo e($index); ?>)"
                                        placeholder="Search for a product to match..."
                                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                                    >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($line['results'])): ?>
                                        <div class="absolute z-10 mt-1 w-full rounded-lg border border-border bg-canvas-raised shadow-lg">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $line['results']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <button
                                                    type="button"
                                                    wire:click="selectProduct(<?php echo e($index); ?>, <?php echo e($result['id']); ?>, '<?php echo e(addslashes($result['name'])); ?>')"
                                                    class="block w-full px-3 py-2 text-left text-sm hover:bg-canvas"
                                                ><?php echo e($result['name']); ?></button>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="w-20">
                            <label class="mb-1 block text-xs text-ink-muted">Qty</label>
                            <input
                                type="number"
                                min="1"
                                wire:model="lines.<?php echo e($index); ?>.quantity"
                                class="w-full rounded-lg border border-border px-2 py-1.5 text-sm focus:border-primary-500 focus:outline-none"
                            >
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($lines) > 1): ?>
                            <button type="button" wire:click="removeLine(<?php echo e($index); ?>)" class="mt-5 text-ink-muted hover:text-danger-500">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <button type="button" wire:click="addLine" class="mt-3 text-sm font-medium text-primary-500 hover:text-primary-600">+ Add another line</button>
    </div>

    <div class="mt-6 rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Fulfillment</h2>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" wire:model.live="fulfillmentType" value="pickup"> Pickup
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" wire:model.live="fulfillmentType" value="delivery"> Delivery
            </label>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fulfillmentType === 'delivery'): ?>
            <div class="mt-3">
                <label class="mb-1 block text-xs text-ink-muted">Delivery address</label>
                <select wire:model="addressId" class="w-full max-w-md rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $prescription->user->addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($address->id); ?>"><?php echo e($address->line1); ?>, <?php echo e($address->city); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <option value="">No saved address for this customer</option>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <button wire:click="submit" class="mt-6 rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
        Create order
    </button>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/admin/create-order-from-prescription.blade.php ENDPATH**/ ?>
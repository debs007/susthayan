<div class="mx-auto max-w-7xl px-6 py-10">
    <div class="mb-6 flex items-center gap-3">
        <div class="flex flex-1 items-center gap-2 rounded-full border border-line bg-surface px-4 py-2.5">
            <svg class="h-4 w-4 shrink-0 text-ink-faint" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.6" /><path d="M14 14L17.5 17.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search medicines, salts, brands..." class="w-full bg-transparent text-sm outline-none">
        </div>
        <select wire:model.live="sort" class="rounded-full border border-line bg-surface px-4 py-2.5 text-sm outline-none">
            <option value="latest">Newest first</option>
            <option value="name">Name (A-Z)</option>
        </select>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        
        <aside class="space-y-6 lg:col-span-1">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink">Filters</p>
                    <button wire:click="clearFilters" class="text-xs text-brand hover:underline">Clear all</button>
                </div>
            </div>

            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-faint">Category</p>
                <div class="space-y-1.5">
                    <label class="flex items-center gap-2 text-sm text-ink-soft">
                        <input type="radio" wire:model.live="category_id" value="" class="accent-brand"> All categories
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="flex items-center gap-2 text-sm text-ink-soft">
                            <input type="radio" wire:model.live="category_id" value="<?php echo e($category->id); ?>" class="accent-brand"> <?php echo e($category->name); ?>

                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-ink-faint">Brand</p>
                <div class="max-h-48 space-y-1.5 overflow-y-auto">
                    <label class="flex items-center gap-2 text-sm text-ink-soft">
                        <input type="radio" wire:model.live="brand_id" value="" class="accent-brand"> All brands
                    </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="flex items-center gap-2 text-sm text-ink-soft">
                            <input type="radio" wire:model.live="brand_id" value="<?php echo e($brand->id); ?>" class="accent-brand"> <?php echo e($brand->name); ?>

                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-soft">
                <input type="checkbox" wire:model.live="prescription_only" class="rounded accent-brand"> Prescription required
            </label>
        </aside>

        
        <div class="lg:col-span-3">
            <p class="mb-4 text-sm text-ink-soft"><?php echo e($products->total()); ?> products found</p>

            <div wire:loading.class="opacity-50" wire:target="search,category_id,brand_id,prescription_only,sort" class="grid grid-cols-2 gap-4 sm:grid-cols-3 transition-opacity">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalf005a9b650879d5c895a4baa7406938a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf005a9b650879d5c895a4baa7406938a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $attributes = $__attributesOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__attributesOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $component = $__componentOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__componentOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="col-span-full py-10 text-center text-sm text-ink-soft">No products match these filters.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mt-8">
                <?php echo e($products->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/product-browser.blade.php ENDPATH**/ ?>
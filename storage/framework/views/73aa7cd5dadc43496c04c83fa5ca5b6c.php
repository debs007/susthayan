<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Brands']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Brands']); ?>
    <?php if (isset($component)) { $__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.errors','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00)): ?>
<?php $attributes = $__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00; ?>
<?php unset($__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00)): ?>
<?php $component = $__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00; ?>
<?php unset($__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00); ?>
<?php endif; ?>

    <?php if(session('success')): ?>
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Brands</h1>
            <p class="text-sm text-ink-muted">Shown as "Top Brands" in the app, with their logo.</p>
        </div>
        <a href="<?php echo e(route('admin.products.index')); ?>" class="text-sm font-medium text-ink-muted hover:text-ink">← Back to Catalogue</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Logo</th>
                            <th class="px-5 py-3 font-medium">Brand</th>
                            <th class="px-5 py-3 font-medium">Products</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-5 py-3">
                                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg border border-border bg-canvas">
                                        <?php if($brand->logo_url): ?>
                                            <img src="<?php echo e($brand->logo_url); ?>" alt="<?php echo e($brand->name); ?>" class="h-full w-full object-contain">
                                        <?php else: ?>
                                            <span class="text-[10px] text-ink-muted">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium"><?php echo e($brand->name); ?></td>
                                <td class="px-5 py-3 font-code text-xs"><?php echo e($brand->products_count); ?></td>
                                <td class="px-5 py-3">
                                    <?php if($brand->is_active): ?>
                                        <span class="text-xs font-medium text-success-600">Active</span>
                                    <?php else: ?>
                                        <span class="text-xs font-medium text-ink-muted">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="<?php echo e(route('admin.brands.toggle-active', $brand)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                            <?php echo e($brand->is_active ? 'Hide' : 'Unhide'); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No brands yet - add the first one.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <form method="POST" action="<?php echo e(route('admin.brands.store')); ?>" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                <?php echo csrf_field(); ?>
                <h2 class="font-display font-semibold">Add brand</h2>
                <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'name','label' => 'Name','required' => true,'placeholder' => 'e.g. Himalaya']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Name','required' => true,'placeholder' => 'e.g. Himalaya']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                <div>
                    <label for="logo" class="block text-sm font-medium text-ink">Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
                    <p class="mt-1.5 text-xs text-ink-muted">Optional - JPG, PNG, or WebP, up to 2MB.</p>
                </div>
                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Add brand
                </button>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/brands/index.blade.php ENDPATH**/ ?>
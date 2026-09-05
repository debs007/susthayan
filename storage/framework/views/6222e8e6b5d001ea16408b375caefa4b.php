<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Inventory']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Inventory']); ?>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex-1 max-w-sm">
            <input
                type="text" name="q" value="<?php echo e(request('q')); ?>"
                placeholder="Search by product name..."
                class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
            >
        </form>
        <div class="flex gap-2 text-sm">
            <a href="<?php echo e(route('franchise.inventory.index')); ?>" class="rounded-lg px-3 py-1.5 font-medium <?php echo e(! request('filter') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas'); ?>">All batches</a>
            <a href="<?php echo e(route('franchise.inventory.index', ['filter' => 'expiring'])); ?>" class="rounded-lg px-3 py-1.5 font-medium <?php echo e(request('filter') === 'expiring' ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas'); ?>">Expiring soon</a>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Photo</th>
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 font-medium">Batch</th>
                    <th class="px-5 py-3 font-medium">Expiry</th>
                    <th class="px-5 py-3 text-right font-medium">In stock</th>
                    <th class="px-5 py-3 text-right font-medium">Reserved</th>
                    <th class="px-5 py-3 text-right font-medium">Available</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="freshness-<?php echo e($batch->freshness); ?> border-l-4">
                        <td class="px-5 py-3">
                            <a href="<?php echo e(route('franchise.products.image.edit', $batch->product)); ?>" class="block h-10 w-10 overflow-hidden rounded-lg border border-border bg-canvas">
                                <?php if($batch->product->image_url): ?>
                                    <img src="<?php echo e($batch->product->image_url); ?>" alt="" class="h-full w-full object-contain">
                                <?php else: ?>
                                    <span class="flex h-full w-full items-center justify-center text-[10px] text-ink-muted">Add</span>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td class="px-5 py-3 font-medium"><?php echo e($batch->product->name); ?></td>
                        <td class="px-5 py-3 font-code text-xs text-ink-muted"><?php echo e($batch->batch_no); ?></td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 font-code text-xs">
                                <span class="freshness-dot h-1.5 w-1.5 rounded-full"></span>
                                <?php echo e($batch->expiry_date->format('d M Y')); ?>

                                <span class="text-ink-muted">(<?php echo e($batch->days_to_expiry); ?>d)</span>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs"><?php echo e($batch->quantity); ?></td>
                        <td class="px-5 py-3 text-right font-code text-xs text-ink-muted"><?php echo e($batch->reserved_quantity); ?></td>
                        <td class="px-5 py-3 text-right font-code text-xs font-semibold"><?php echo e($batch->available_quantity); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-ink-muted">No stock on hand.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($batches->hasPages()): ?>
        <div class="mt-4"><?php echo e($batches->links()); ?></div>
    <?php endif; ?>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/franchise/inventory/index.blade.php ENDPATH**/ ?>
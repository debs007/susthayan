<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Franchises']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Franchises']); ?>
    <?php if(session('success')): ?>
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-ink-muted">
            <?php echo e($franchises->total()); ?> <?php echo e(Str::plural('franchise', $franchises->total())); ?> in the network
        </p>
        <a href="<?php echo e(route('admin.franchises.create')); ?>" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
            + Add franchise
        </a>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Franchise</th>
                    <th class="px-5 py-3 font-medium">City</th>
                    <th class="px-5 py-3 font-medium">Commission</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Bank details</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php $__empty_1 = true; $__currentLoopData = $franchises; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $franchise): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-5 py-3 font-medium"><?php echo e($franchise->name); ?></td>
                        <td class="px-5 py-3 text-ink-muted"><?php echo e($franchise->city ?? '—'); ?></td>
                        <td class="px-5 py-3 font-code text-xs"><?php echo e(number_format($franchise->commission_percentage, 2)); ?>%</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($franchise->status === 'active' ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600'); ?>">
                                <?php echo e(ucfirst($franchise->status)); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <?php if($franchise->bank_account_number): ?>
                                <span class="text-xs font-medium text-success-600">✓ On file</span>
                            <?php else: ?>
                                <span class="text-xs font-medium text-honey-600">Not added</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="<?php echo e(route('admin.franchises.edit', $franchise)); ?>" class="text-sm font-medium text-primary-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">
                            No franchises yet.
                            <a href="<?php echo e(route('admin.franchises.create')); ?>" class="font-medium text-primary-500 hover:underline">Add the first one</a>.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($franchises->hasPages()): ?>
        <div class="mt-4"><?php echo e($franchises->links()); ?></div>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/franchises/index.blade.php ENDPATH**/ ?>
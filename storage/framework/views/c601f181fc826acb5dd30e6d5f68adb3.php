<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Doctors']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Doctors']); ?>
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
            <h1 class="font-display text-lg font-semibold">Doctors</h1>
            <p class="text-sm text-ink-muted">Each doctor can be affiliated with multiple hospitals, each with its own charge and visit days.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('admin.departments.index')); ?>" class="text-sm font-medium text-ink-muted hover:text-ink">Departments</a>
            <a href="<?php echo e(route('admin.hospitals.index')); ?>" class="text-sm font-medium text-ink-muted hover:text-ink">Hospitals</a>
            <a href="<?php echo e(route('admin.doctors.create')); ?>" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                + Add doctor
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Doctor</th>
                    <th class="px-5 py-3 font-medium">Department</th>
                    <th class="px-5 py-3 font-medium">Hospitals</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php $__empty_1 = true; $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-border bg-canvas">
                                    <?php if($doctor->photo_url): ?>
                                        <img src="<?php echo e($doctor->photo_url); ?>" alt="" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <span class="text-xs text-ink-muted"><?php echo e(Str::substr($doctor->name, 0, 1)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-medium"><?php echo e($doctor->name); ?></p>
                                    <p class="text-xs text-ink-muted"><?php echo e($doctor->degree); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-muted"><?php echo e($doctor->department->name); ?></td>
                        <td class="px-5 py-3 font-code text-xs"><?php echo e($doctor->hospitals->count()); ?></td>
                        <td class="px-5 py-3">
                            <?php if($doctor->is_active): ?>
                                <span class="text-xs font-medium text-success-600">Active</span>
                            <?php else: ?>
                                <span class="text-xs font-medium text-ink-muted">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="<?php echo e(route('admin.doctors.edit', $doctor)); ?>" class="text-xs font-medium text-primary-500 hover:text-primary-600">Edit</a>
                                <form method="POST" action="<?php echo e(route('admin.doctors.toggle-active', $doctor)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                        <?php echo e($doctor->is_active ? 'Hide' : 'Unhide'); ?>

                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No doctors yet - add the first one.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/doctors/index.blade.php ENDPATH**/ ?>
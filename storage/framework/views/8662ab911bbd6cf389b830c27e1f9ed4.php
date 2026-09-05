<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Audit Log']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Audit Log']); ?>
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-border bg-canvas-raised p-4">
        <div>
            <label class="mb-1.5 block text-xs font-medium text-ink-muted">Model</label>
            <select name="subject_type" class="rounded-lg border border-border bg-canvas-raised px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                <option value="">All models</option>
                <?php $__currentLoopData = $subjectTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>" <?php if(request('subject_type') === $type): echo 'selected'; endif; ?>><?php echo e($type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-medium text-ink-muted">From</label>
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-medium text-ink-muted">To</label>
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
        </div>
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Filter</button>
        <?php if(request()->hasAny(['subject_type', 'from', 'to'])): ?>
            <a href="<?php echo e(route('admin.audit-log.index')); ?>" class="text-sm font-medium text-ink-muted hover:underline">Clear</a>
        <?php endif; ?>
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">When</th>
                    <th class="px-5 py-3 font-medium">Event</th>
                    <th class="px-5 py-3 font-medium">Model</th>
                    <th class="px-5 py-3 font-medium">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php $__empty_1 = true; $__currentLoopData = $activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-5 py-3 font-code text-xs text-ink-muted"><?php echo e($entry->created_at->format('d M Y, H:i')); ?></td>
                        <td class="px-5 py-3"><?php echo e($entry->description); ?></td>
                        <td class="px-5 py-3 font-code text-xs">
                            <?php echo e(class_basename($entry->subject_type)); ?> #<?php echo e($entry->subject_id); ?>

                        </td>
                        <td class="px-5 py-3 text-ink-muted"><?php echo e($entry->causer?->name ?? 'System'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-ink-muted">No matching activity.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($activity->hasPages()): ?>
        <div class="mt-4"><?php echo e($activity->links()); ?></div>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/audit-log/index.blade.php ENDPATH**/ ?>
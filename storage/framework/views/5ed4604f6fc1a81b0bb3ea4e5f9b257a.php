<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Customers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Customers']); ?>
    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Customers</h1>
        <p class="text-sm text-ink-muted">Every registered app customer - tap a row for their full profile, order history, and health data.</p>
    </div>

    <form method="GET" class="mb-5 flex gap-3">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name or mobile number" class="w-full max-w-sm rounded-lg border border-border px-3 py-2 text-sm">
        <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Search</button>
        <?php if(request('search')): ?>
            <a href="<?php echo e(route('admin.customers.index')); ?>" class="rounded-lg border border-border px-4 py-2 text-sm text-ink-muted hover:bg-canvas">Clear</a>
        <?php endif; ?>
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Customer</th>
                    <th class="px-5 py-3 font-medium">Mobile</th>
                    <th class="px-5 py-3 font-medium">Orders</th>
                    <th class="px-5 py-3 font-medium">Joined</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-5 py-3 font-medium"><?php echo e($customer->name); ?></td>
                        <td class="px-5 py-3 font-code text-ink-muted">+91 <?php echo e($customer->mobile); ?></td>
                        <td class="px-5 py-3 font-code text-xs"><?php echo e($customer->orders_count); ?></td>
                        <td class="px-5 py-3 text-xs text-ink-muted"><?php echo e($customer->created_at->format('d M Y')); ?></td>
                        <td class="px-5 py-3 text-right">
                            <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="text-xs font-medium text-primary-500 hover:text-primary-600">View Details →</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No customers <?php echo e(request('search') ? 'match that search' : 'yet'); ?>.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($customers->links()); ?></div>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/customers/index.blade.php ENDPATH**/ ?>
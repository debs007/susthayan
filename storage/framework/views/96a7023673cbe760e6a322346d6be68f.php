<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Outstanding & Purchase Orders']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Outstanding & Purchase Orders']); ?>
    <div class="mb-6 flex gap-2 text-sm">
        <a href="<?php echo e(route('admin.vendors.index')); ?>" class="rounded-lg px-3 py-1.5 font-medium text-ink-muted hover:bg-canvas">Suppliers</a>
        <a href="<?php echo e(route('admin.vendors.outstanding')); ?>" class="rounded-lg bg-primary-50 px-3 py-1.5 font-medium text-primary-600">Outstanding & purchase orders</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Vendor outstanding (network-wide)</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Supplier</th>
                        <th class="px-5 py-3 text-right font-medium">Invoiced</th>
                        <th class="px-5 py-3 text-right font-medium">Paid</th>
                        <th class="px-5 py-3 text-right font-medium">Outstanding</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php $__empty_1 = true; $__currentLoopData = $outstanding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-5 py-3 font-medium">
                                <a href="<?php echo e(route('admin.vendors.ledger', $row['supplier_id'])); ?>" class="hover:underline"><?php echo e($row['supplier_name']); ?></a>
                            </td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹<?php echo e(number_format($row['total_invoiced'], 2)); ?></td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹<?php echo e(number_format($row['total_paid'], 2)); ?></td>
                            <td class="px-5 py-3 text-right font-code text-xs font-semibold text-honey-600">₹<?php echo e(number_format($row['outstanding'], 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-ink-muted">Nothing outstanding right now.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Recent purchase orders</h2>
                <p class="text-xs text-ink-muted">Created by franchises - admin has oversight, not creation, here.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Supplier</th>
                        <th class="px-5 py-3 font-medium">Franchise</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php $__empty_1 = true; $__currentLoopData = $recentPurchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-5 py-3 font-medium"><?php echo e($po->supplier->name); ?></td>
                            <td class="px-5 py-3 text-ink-muted"><?php echo e($po->franchise->name); ?></td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted">
                                    <?php echo e(str_replace('_', ' ', $po->status->value)); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-ink-muted">No purchase orders yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/vendors/outstanding.blade.php ENDPATH**/ ?>
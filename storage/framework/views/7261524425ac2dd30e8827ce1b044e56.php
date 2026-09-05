<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard']); ?>
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Today's revenue</p>
            <p class="mt-1.5 font-display text-3xl font-semibold">₹<?php echo e(number_format($todaysRevenue, 0)); ?></p>
            <?php if($revenueChangePercent !== null): ?>
                <p class="mt-1 text-xs <?php echo e($revenueChangePercent >= 0 ? 'text-success-600' : 'text-danger-500'); ?>">
                    <?php echo e($revenueChangePercent >= 0 ? '↑' : '↓'); ?> <?php echo e(abs($revenueChangePercent)); ?>% vs. yesterday
                </p>
            <?php endif; ?>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Orders today</p>
            <p class="mt-1.5 font-display text-3xl font-semibold"><?php echo e($todaysOrderCount); ?></p>
            <p class="mt-1 text-xs text-ink-muted">across <?php echo e($activeFranchiseCount); ?> active <?php echo e(Str::plural('franchise', $activeFranchiseCount)); ?></p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Pending prescriptions</p>
            <p class="mt-1.5 font-display text-3xl font-semibold <?php echo e($pendingPrescriptions > 0 ? 'text-honey-600' : ''); ?>"><?php echo e($pendingPrescriptions); ?></p>
            <?php if($pendingPrescriptions > 0): ?>
                <p class="mt-1 text-xs text-honey-600">Needs review</p>
            <?php endif; ?>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Delivery success rate</p>
            <p class="mt-1.5 font-display text-3xl font-semibold"><?php echo e($deliverySuccessRate !== null ? $deliverySuccessRate.'%' : '—'); ?></p>
            <p class="mt-1 text-xs text-ink-muted">last 7 days</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <div class="flex items-center justify-between border-b border-border px-5 py-4">
                    <h2 class="font-display font-semibold">Recent orders</h2>
                    <a href="<?php echo e(route('admin.reports.sales-register')); ?>" class="text-xs font-medium text-primary-500 hover:underline">View all →</a>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Order</th>
                            <th class="px-5 py-3 font-medium">Customer</th>
                            <th class="px-5 py-3 font-medium">Franchise</th>
                            <th class="px-5 py-3 text-right font-medium">Amount</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-5 py-3 font-code text-xs font-medium">#HP-<?php echo e($order->id); ?></td>
                                <td class="px-5 py-3"><?php echo e($order->user?->name ?? $order->walk_in_customer_name ?? 'Walk-in'); ?></td>
                                <td class="px-5 py-3 text-ink-muted"><?php echo e($order->franchise?->name ?? '—'); ?></td>
                                <td class="px-5 py-3 text-right font-code text-xs">₹<?php echo e(number_format($order->total_amount, 2)); ?></td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted"><?php echo e(str_replace('_', ' ', $order->status->value)); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-ink-muted">No orders yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-4 font-display font-semibold">Hourly orders today</h2>
                <?php if($hourlyOrders->sum() > 0): ?>
                    <?php $peak = max($hourlyOrders->max(), 1); ?>
                    <div class="flex h-32 items-end gap-1">
                        <?php $__currentLoopData = $hourlyOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hour => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="group relative flex-1">
                                <div
                                    class="rounded-t bg-primary-500/70 transition-all group-hover:bg-primary-500"
                                    style="height: <?php echo e($count > 0 ? max(($count / $peak) * 100, 6) : 2); ?>%"
                                ></div>
                                <div class="pointer-events-none absolute -top-6 left-1/2 -translate-x-1/2 rounded bg-ink px-1.5 py-0.5 text-[10px] text-white opacity-0 transition-opacity group-hover:opacity-100">
                                    <?php echo e($count); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-2 flex justify-between text-[10px] text-ink-muted">
                        <span>12am</span><span>6am</span><span>12pm</span><span>6pm</span><span>now</span>
                    </div>
                <?php else: ?>
                    <p class="py-8 text-center text-sm text-ink-muted">No orders yet today.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Recent activity</h2>
            </div>
            <div class="divide-y divide-border">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-5 py-3 text-sm">
                        <p><?php echo e($item['message']); ?></p>
                        <p class="mt-0.5 text-xs text-ink-muted"><?php echo e($item['timestamp']->diffForHumans()); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-8 text-center text-sm text-ink-muted">Nothing to show yet.</p>
                <?php endif; ?>
            </div>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>
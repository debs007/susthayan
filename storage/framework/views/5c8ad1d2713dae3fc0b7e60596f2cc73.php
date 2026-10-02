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
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Today's orders</p>
            <p class="mt-1.5 font-display text-3xl font-semibold"><?php echo e($todaysOrderCount); ?></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderChangeVsYesterday !== 0): ?>
                <p class="mt-1 text-xs <?php echo e($orderChangeVsYesterday > 0 ? 'text-success-600' : 'text-danger-500'); ?>">
                    <?php echo e($orderChangeVsYesterday > 0 ? '↑' : '↓'); ?> <?php echo e(abs($orderChangeVsYesterday)); ?> vs. yesterday
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Awaiting verification</p>
            <p class="mt-1.5 font-display text-3xl font-semibold <?php echo e($pendingPrescriptions > 0 ? 'text-honey-600' : ''); ?>"><?php echo e($pendingPrescriptions); ?></p>
            <p class="mt-1 text-xs text-ink-muted">Prescriptions, network-wide</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Low stock</p>
            <p class="mt-1.5 font-display text-3xl font-semibold <?php echo e($lowStockCount > 0 ? 'text-honey-600' : ''); ?>"><?php echo e($lowStockCount); ?></p>
            <p class="mt-1 text-xs text-ink-muted">Products running low</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Near-expiry batches</p>
            <p class="mt-1.5 font-display text-3xl font-semibold text-honey-600"><?php echo e($watchBatches->count()); ?></p>
            <p class="mt-1 text-xs text-ink-muted">Within 90 days</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-5">
        <div class="lg:col-span-3 rounded-xl border border-border bg-canvas-raised">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Recent orders</h2>
                <a href="<?php echo e(route('franchise.orders.index')); ?>" class="text-xs font-medium text-primary-500 hover:underline">View all →</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3 font-medium">Order</th>
                        <th class="px-5 py-3 font-medium">Customer</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="px-5 py-3 font-code text-xs">#HP-<?php echo e($order->id); ?></td>
                            <td class="px-5 py-3"><?php echo e($order->user?->name ?? $order->walk_in_customer_name ?? 'Walk-in'); ?></td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full bg-canvas px-2.5 py-1 text-xs font-medium capitalize text-ink-muted">
                                    <?php echo e(str_replace('_', ' ', $order->status->value)); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-code text-xs">₹<?php echo e(number_format($order->total_amount, 2)); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-ink-muted">No orders yet.</td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="lg:col-span-2 rounded-xl border border-border bg-canvas-raised">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Batches to watch</h2>
                <a href="<?php echo e(route('franchise.inventory.index')); ?>" class="text-xs font-medium text-primary-500 hover:underline">View all →</a>
            </div>
            <ul class="divide-y divide-border">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $watchBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li class="freshness-<?php echo e($batch->freshness); ?> flex items-center justify-between border-l-4 px-5 py-3">
                        <div>
                            <p class="text-sm font-medium"><?php echo e($batch->product->name); ?></p>
                            <p class="font-code text-xs text-ink-muted"><?php echo e($batch->batch_no); ?></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="freshness-dot h-2 w-2 rounded-full"></span>
                            <span class="text-xs text-ink-muted"><?php echo e($batch->days_to_expiry); ?>d left</span>
                        </div>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <li class="px-5 py-8 text-center text-sm text-ink-muted">Nothing expiring soon.</li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/franchise/dashboard.blade.php ENDPATH**/ ?>
<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Order #'.e($order->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Order #'.e($order->id).'']); ?>
    <a href="<?php echo e(route('admin.orders.index')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Orders
    </a>

    <div class="mb-6 flex items-center justify-between rounded-xl border border-border bg-canvas-raised p-5">
        <div>
            <h1 class="font-display text-lg font-semibold">Order #<?php echo e($order->id); ?></h1>
            <p class="text-sm text-ink-muted"><?php echo e(ucfirst(str_replace('_', ' ', $order->order_type))); ?> &bull; Placed <?php echo e($order->created_at->format('d M Y, h:i A')); ?></p>
        </div>
        <div class="text-right">
            <p class="font-display text-lg font-semibold">₹<?php echo e(number_format($order->total_amount, 2)); ?></p>
            <p class="text-xs text-ink-muted"><?php echo e(ucfirst(str_replace('_', ' ', $order->status->value))); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <?php if($order->order_type === 'product' && $order->items->isNotEmpty()): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Items</h2>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                            <p><?php echo e($item->product?->name ?? 'Product #'.$item->product_id); ?></p>
                            <p class="text-ink-muted">Qty: <?php echo e($item->quantity); ?> &bull; ₹<?php echo e(number_format($item->unit_price, 2)); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php elseif($order->order_type === 'lab_test' && $order->labTestBooking): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Lab Test Details</h2>
                    <p class="font-medium"><?php echo e($order->labTestBooking->labTest?->name ?? '—'); ?></p>
                    <p class="text-sm text-ink-muted"><?php echo e($order->labTestBooking->labCenter?->name ?? '—'); ?></p>
                    <p class="text-sm text-ink-muted">Scheduled: <?php echo e($order->labTestBooking->scheduled_date->format('d M Y')); ?> &bull; <?php echo e(ucfirst($order->labTestBooking->status)); ?></p>
                </div>
            <?php elseif($order->order_type === 'appointment' && $order->appointmentBooking): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Appointment Details</h2>
                    <p class="font-medium"><?php echo e($order->appointmentBooking->doctor?->name ?? '—'); ?></p>
                    <p class="text-sm text-ink-muted"><?php echo e($order->appointmentBooking->hospital?->name ?? '—'); ?></p>
                    <p class="text-sm text-ink-muted">Scheduled: <?php echo e($order->appointmentBooking->scheduled_date->format('d M Y')); ?> &bull; <?php echo e(ucfirst($order->appointmentBooking->status)); ?></p>
                </div>
            <?php elseif($order->order_type === 'wallet_topup'): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Wallet Top-up</h2>
                    <p class="text-sm text-ink-muted">₹<?php echo e(number_format($order->total_amount, 2)); ?> added to <?php echo e($order->user->name); ?>'s wallet on successful payment.</p>
                </div>
            <?php endif; ?>

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Payment History (<?php echo e($order->payments->count()); ?>)</h2>
                <?php $__empty_1 = true; $__currentLoopData = $order->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                        <div>
                            <p class="font-code text-xs text-ink-muted"><?php echo e($payment->gateway_order_id); ?></p>
                            <p class="text-xs text-ink-muted"><?php echo e($payment->created_at->format('d M Y, h:i A')); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-code">₹<?php echo e(number_format($payment->amount, 2)); ?></p>
                            <p class="text-xs
                                <?php echo e($payment->status->value === 'success' ? 'text-success-600' : ($payment->status->value === 'failed' ? 'text-danger-600' : 'text-ink-muted')); ?>">
                                <?php echo e(ucfirst($payment->status->value)); ?>

                            </p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-ink-muted">No payment attempts recorded.</p>
                <?php endif; ?>
            </div>

            <?php if($order->deliveryAssignments->isNotEmpty()): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Delivery</h2>
                    <?php $__currentLoopData = $order->deliveryAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border-b border-border py-2 text-sm last:border-0">
                            <p><?php echo e($assignment->deliveryAgent?->name ?? '—'); ?></p>
                            <p class="text-xs text-ink-muted"><?php echo e(ucfirst($assignment->status)); ?> &bull; Assigned <?php echo e($assignment->assigned_at?->format('d M Y, h:i A')); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Customer</h2>
                <p class="font-medium"><?php echo e($order->user->name); ?></p>
                <p class="text-sm text-ink-muted">+91 <?php echo e($order->user->mobile); ?></p>
                <a href="<?php echo e(route('admin.customers.show', $order->user)); ?>" class="mt-2 inline-block text-xs font-medium text-primary-500 hover:text-primary-600">View full profile →</a>
            </div>

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Franchise</h2>
                <p class="text-sm"><?php echo e($order->franchise?->name ?? 'No franchise (wallet top-up)'); ?></p>
            </div>

            <?php if($order->address): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Delivery Address</h2>
                    <p class="text-sm"><?php echo e($order->address->label ?? 'Address'); ?></p>
                    <p class="text-xs text-ink-muted"><?php echo e($order->address->line1); ?>, <?php echo e($order->address->city); ?>, <?php echo e($order->address->pincode); ?></p>
                </div>
            <?php endif; ?>

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Amount Breakdown</h2>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-ink-muted">Subtotal</span><span>₹<?php echo e(number_format($order->subtotal_amount, 2)); ?></span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Discount</span><span>-₹<?php echo e(number_format($order->discount_amount, 2)); ?></span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Tax</span><span>₹<?php echo e(number_format($order->tax_amount, 2)); ?></span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Delivery</span><span>₹<?php echo e(number_format($order->delivery_charge, 2)); ?></span></div>
                    <div class="flex justify-between border-t border-border pt-1 font-semibold"><span>Total</span><span>₹<?php echo e(number_format($order->total_amount, 2)); ?></span></div>
                </div>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/orders/show.blade.php ENDPATH**/ ?>
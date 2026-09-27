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
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->invoice): ?>
                <a href="<?php echo e(route('admin.orders.invoice', $order)); ?>" class="mt-2 inline-block text-xs font-medium text-primary-500 hover:underline">Download invoice</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="mb-6 rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-600"><?php echo e(session('error')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="mb-6 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-600"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
        <h2 class="mb-3 font-display font-semibold">Order status</h2>
        <form method="POST" action="<?php echo e(route('admin.orders.status', $order)); ?>" class="flex items-center gap-2">
            <?php echo csrf_field(); ?>
            <select name="new_status" class="flex-1 rounded-lg border border-border bg-canvas px-3 py-2 text-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['pending_payment', 'confirmed', 'preparing', 'ready_for_dispatch', 'out_for_delivery', 'delivered', 'picked_up', 'cancelled', 'refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($status); ?>" <?php if($order->status->value === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst(str_replace('_', ' ', $status))); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Update status</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->order_type === 'product' && $order->items->isNotEmpty()): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Items</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                            <p><?php echo e($item->product?->name ?? 'Product #'.$item->product_id); ?></p>
                            <p class="text-ink-muted">Qty: <?php echo e($item->quantity); ?> &bull; ₹<?php echo e(number_format($item->unit_price, 2)); ?></p>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php elseif($order->order_type === 'lab_test' && $order->labTestBookings->isNotEmpty()): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Lab Test Details</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->labTestBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <p class="font-medium"><?php echo e($booking->labTest?->name ?? '—'); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    
                    <p class="text-sm text-ink-muted mt-2"><?php echo e($order->labTestBookings->first()->labCenter?->name ?? '—'); ?></p>
                    <p class="text-sm text-ink-muted">Scheduled: <?php echo e($order->labTestBookings->first()->scheduled_date->format('d M Y')); ?> &bull; <?php echo e(ucfirst($order->labTestBookings->first()->status)); ?></p>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="rounded-xl border border-border bg-canvas-raised p-5">
                <h2 class="mb-3 font-display font-semibold">Payment History (<?php echo e($order->payments->count()); ?>)</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $order->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="text-sm text-ink-muted">No payment attempts recorded.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->deliveryAssignments->isNotEmpty()): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Delivery</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->deliveryAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="border-b border-border py-2 text-sm last:border-0">
                            <p><?php echo e($assignment->deliveryAgent?->name ?? '—'); ?></p>
                            <p class="text-xs text-ink-muted"><?php echo e(ucfirst($assignment->status)); ?> &bull; Assigned <?php echo e($assignment->assigned_at?->format('d M Y, h:i A')); ?></p>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->franchise): ?>
                    <p class="text-sm"><?php echo e($order->franchise->name); ?></p>
                <?php elseif($order->order_type === 'product'): ?>
                    <p class="mb-3 text-sm font-medium text-warning-600">Awaiting assignment</p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                        <p class="mb-3 text-xs text-danger-600"><?php echo e(session('error')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <form method="POST" action="<?php echo e(route('admin.orders.assign', $order)); ?>" class="flex items-center gap-2">
                        <?php echo csrf_field(); ?>
                        <select name="franchise_id" required class="flex-1 rounded-lg border border-border bg-canvas px-3 py-2 text-sm">
                            <option value="">Choose a franchise&hellip;</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $franchises; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $franchise): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($franchise->id); ?>"><?php echo e($franchise->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <button type="submit" class="rounded-lg bg-primary-500 px-3 py-2 text-sm font-medium text-white hover:bg-primary-600">Assign</button>
                    </form>
                <?php else: ?>
                    
                    <p class="text-sm text-ink-muted">Not applicable for this order type.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->address): ?>
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <h2 class="mb-3 font-display font-semibold">Delivery Address</h2>
                    <p class="text-sm"><?php echo e($order->address->label ?? 'Address'); ?></p>
                    <p class="text-xs text-ink-muted"><?php echo e($order->address->line1); ?>, <?php echo e($order->address->city); ?>, <?php echo e($order->address->pincode); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
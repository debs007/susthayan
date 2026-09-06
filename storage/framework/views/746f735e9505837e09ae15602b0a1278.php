<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => ''.e($customer->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($customer->name).'']); ?>
    <a href="<?php echo e(route('admin.customers.index')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Customers
    </a>

    <div class="mb-6 flex items-center gap-4 rounded-xl border border-border bg-canvas-raised p-5">
        <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-border bg-canvas">
            <?php if($customer->profile_image_url): ?>
                <img src="<?php echo e($customer->profile_image_url); ?>" alt="" class="h-full w-full object-cover">
            <?php else: ?>
                <span class="text-lg text-ink-muted"><?php echo e(Str::substr($customer->name, 0, 1)); ?></span>
            <?php endif; ?>
        </div>
        <div class="flex-1">
            <h1 class="font-display text-lg font-semibold"><?php echo e($customer->name); ?></h1>
            <p class="text-sm text-ink-muted">+91 <?php echo e($customer->mobile); ?> <?php if($customer->email): ?> &bull; <?php echo e($customer->email); ?> <?php endif; ?></p>
            <p class="text-xs text-ink-muted">Joined <?php echo e($customer->created_at->format('d M Y')); ?></p>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-wide text-ink-muted">Wallet Balance</p>
            <p class="font-display text-lg font-semibold text-primary-600">₹<?php echo e(number_format($customer->wallet?->balance ?? 0, 2)); ?></p>
            <a href="<?php echo e(route('admin.notifications.create', ['user_id' => $customer->id])); ?>" class="mt-2 inline-block rounded-lg bg-primary-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-600">
                Send Notification
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Order History (<?php echo e($customer->orders->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                    <div>
                        <p class="font-medium">#<?php echo e($order->id); ?> <span class="text-xs text-ink-muted">(<?php echo e(ucfirst(str_replace('_', ' ', $order->order_type))); ?>)</span></p>
                        <p class="text-xs text-ink-muted"><?php echo e($order->created_at->format('d M Y, h:i A')); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="font-code">₹<?php echo e(number_format($order->total_amount, 2)); ?></p>
                        <p class="text-xs text-ink-muted"><?php echo e(ucfirst(str_replace('_', ' ', $order->status->value))); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No orders yet.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Prescriptions (<?php echo e($customer->prescriptions->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->prescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                    <p class="text-xs text-ink-muted"><?php echo e($prescription->created_at->format('d M Y')); ?></p>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium
                        <?php echo e($prescription->verification_status === 'verified' ? 'bg-success-50 text-success-600' : ($prescription->verification_status === 'rejected' ? 'bg-danger-50 text-danger-600' : 'bg-canvas text-ink-muted')); ?>">
                        <?php echo e(ucfirst($prescription->verification_status)); ?>

                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No prescriptions uploaded.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Vitals Logged (<?php echo e($customer->vitals->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->vitals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vital): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="text-xs text-ink-muted"><?php echo e($vital->recorded_at->format('d M Y, h:i A')); ?></p>
                    <p class="text-xs">
                        <?php if($vital->heart_rate_bpm): ?> HR: <?php echo e($vital->heart_rate_bpm); ?> bpm &bull; <?php endif; ?>
                        <?php if($vital->blood_pressure_label): ?> BP: <?php echo e($vital->blood_pressure_label); ?> &bull; <?php endif; ?>
                        <?php if($vital->spo2_percentage): ?> SpO2: <?php echo e($vital->spo2_percentage); ?>% <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No vitals logged.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Health Records (<?php echo e($customer->healthRecords->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->healthRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                    <div>
                        <p class="font-medium"><?php echo e($record->title); ?></p>
                        <p class="text-xs text-ink-muted"><?php echo e(ucfirst(str_replace('_', ' ', $record->type))); ?> &bull; <?php echo e($record->record_date->format('d M Y')); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No health records uploaded.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Doctor Appointments (<?php echo e($customer->appointmentBookings->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->appointmentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="font-medium"><?php echo e($booking->doctor->name); ?> <span class="text-xs text-ink-muted">— <?php echo e($booking->hospital->name); ?></span></p>
                    <p class="text-xs text-ink-muted"><?php echo e($booking->scheduled_date->format('d M Y')); ?> &bull; <?php echo e(ucfirst($booking->status)); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No appointments booked.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Lab Test Bookings (<?php echo e($customer->labTestBookings->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->labTestBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="font-medium"><?php echo e($booking->labTest->name); ?> <span class="text-xs text-ink-muted">— <?php echo e($booking->labCenter->name); ?></span></p>
                    <p class="text-xs text-ink-muted"><?php echo e($booking->scheduled_date->format('d M Y')); ?> &bull; <?php echo e(ucfirst($booking->status)); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No lab tests booked.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Current Cart</h2>
            <?php if($customer->cart && $customer->cart->items->isNotEmpty()): ?>
                <?php $__currentLoopData = $customer->cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between border-b border-border py-2 text-sm last:border-0">
                        <p><?php echo e($item->product->name); ?></p>
                        <p class="text-xs text-ink-muted">Qty: <?php echo e($item->quantity); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($customer->cart->coupon): ?>
                    <p class="mt-2 inline-block rounded bg-primary-50 px-2 py-1 font-code text-xs font-medium text-primary-600">
                        Coupon applied: <?php echo e($customer->cart->coupon->code); ?>

                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-sm text-ink-muted">Cart is empty.</p>
            <?php endif; ?>
        </div>

        
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <h2 class="mb-3 font-display font-semibold">Saved Addresses (<?php echo e($customer->addresses->count()); ?>)</h2>
            <?php $__empty_1 = true; $__currentLoopData = $customer->addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-b border-border py-2 text-sm last:border-0">
                    <p class="font-medium"><?php echo e($address->label ?? 'Address'); ?></p>
                    <p class="text-xs text-ink-muted"><?php echo e($address->line1); ?>, <?php echo e($address->city); ?>, <?php echo e($address->pincode); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-muted">No saved addresses.</p>
            <?php endif; ?>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/customers/show.blade.php ENDPATH**/ ?>
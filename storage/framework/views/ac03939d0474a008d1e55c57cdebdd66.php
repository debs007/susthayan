<?php if (isset($component)) { $__componentOriginal51ea8e071ed038a4c6542abfe5470d94 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ea8e071ed038a4c6542abfe5470d94 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.storefront','data' => ['title' => 'Your Orders - Susthayan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.storefront'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Your Orders - Susthayan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mx-auto max-w-4xl px-6 py-10">
        <h1 class="font-display text-2xl font-medium text-ink">Your Orders</h1>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->isEmpty()): ?>
            <div class="mt-10 text-center">
                <p class="text-sm text-ink-soft">You haven't placed any orders yet.</p>
                <a href="<?php echo e(route('storefront.categories')); ?>" wire:navigate class="mt-4 inline-block rounded-full bg-brand px-6 py-3 text-sm font-medium text-white hover:bg-brand-hover">Start shopping</a>
            </div>
        <?php else: ?>
            <div class="mt-6 space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="rounded-2xl border border-line bg-surface p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-ink">Order #<?php echo e($order->id); ?></p>
                                <p class="text-xs text-ink-faint"><?php echo e(ucfirst(str_replace('_', ' ', $order->order_type))); ?> &bull; <?php echo e($order->created_at->format('d M Y, h:i A')); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-ink">₹<?php echo e(number_format($order->total_amount, 2)); ?></p>
                                <p class="text-xs text-ink-soft"><?php echo e(ucfirst(str_replace('_', ' ', $order->status->value))); ?></p>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->order_type === 'product' && $order->items->isNotEmpty()): ?>
                            <div class="mt-3 border-t border-line pt-3 text-sm text-ink-soft">
                                <?php echo e($order->items->pluck('product.name')->filter()->join(', ')); ?>

                            </div>
                        <?php elseif($order->order_type === 'lab_test' && $order->labTestBooking): ?>
                            <div class="mt-3 border-t border-line pt-3 text-sm text-ink-soft">
                                <?php echo e($order->labTestBooking->labTest->name ?? 'Lab test'); ?> &bull; <?php echo e($order->labTestBooking->scheduled_date->format('d M Y')); ?>

                            </div>
                        <?php elseif($order->order_type === 'appointment' && $order->appointmentBooking): ?>
                            <div class="mt-3 border-t border-line pt-3 text-sm text-ink-soft">
                                Dr. <?php echo e($order->appointmentBooking->doctor->name ?? ''); ?> &bull; <?php echo e($order->appointmentBooking->scheduled_date->format('d M Y')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="mt-6"><?php echo e($orders->links()); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ea8e071ed038a4c6542abfe5470d94)): ?>
<?php $attributes = $__attributesOriginal51ea8e071ed038a4c6542abfe5470d94; ?>
<?php unset($__attributesOriginal51ea8e071ed038a4c6542abfe5470d94); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ea8e071ed038a4c6542abfe5470d94)): ?>
<?php $component = $__componentOriginal51ea8e071ed038a4c6542abfe5470d94; ?>
<?php unset($__componentOriginal51ea8e071ed038a4c6542abfe5470d94); ?>
<?php endif; ?>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/storefront/orders/index.blade.php ENDPATH**/ ?>
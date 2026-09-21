<div class="mx-auto max-w-4xl px-6 py-10">
    <h1 class="font-display text-2xl font-medium text-ink">Your Cart</h1>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $cart || $cart->items->isEmpty()): ?>
        <div class="mx-auto flex max-w-md flex-col items-center px-6 py-24 text-center">
            <p class="font-display text-xl font-medium text-ink">Your cart is empty</p>
            <p class="mt-2 text-sm text-ink-soft">Add some medicines or health products to get started.</p>
            <a href="<?php echo e(route('storefront.categories')); ?>" wire:navigate class="mt-6 rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover">Start shopping</a>
        </div>
    <?php else: ?>
        <?php
            $lines = $cart->items->map(function ($item) use ($cart) {
                $price = $item->product->priceFor($cart->franchise_id);
                return (object) [
                    'item' => $item,
                    'unitPrice' => $price?->selling_price,
                    'lineTotal' => $price ? round($price->selling_price * $item->quantity, 2) : null,
                ];
            });
            $subtotal = $lines->sum(fn ($l) => (float) ($l->lineTotal ?? 0));
            $discount = $cart->couponDiscountAmount();
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cart->requiresPrescription()): ?>
            <p class="mt-4 rounded-lg bg-warning/10 px-4 py-3 text-sm text-warning">
                One or more items need a prescription - you'll be asked to upload one at checkout.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-6 divide-y divide-line rounded-2xl border border-line bg-surface">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex items-center gap-4 p-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-ink"><?php echo e($line->item->product->name); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line->item->product->prescription_required): ?>
                            <p class="text-xs text-warning">Prescription required</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="mt-1 text-sm text-ink-soft">
                            <?php echo e($line->unitPrice ? '₹'.$line->unitPrice.' each' : 'Price unavailable'); ?>

                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="updateQuantity(<?php echo e($line->item->id); ?>, <?php echo e($line->item->quantity - 1); ?>)" class="h-7 w-7 rounded-full border border-line text-ink-soft hover:border-brand" aria-label="Decrease quantity">&minus;</button>
                        <span class="w-6 text-center text-sm"><?php echo e($line->item->quantity); ?></span>
                        <button wire:click="updateQuantity(<?php echo e($line->item->id); ?>, <?php echo e($line->item->quantity + 1); ?>)" class="h-7 w-7 rounded-full border border-line text-ink-soft hover:border-brand" aria-label="Increase quantity">+</button>
                    </div>

                    <p class="w-20 text-right text-sm font-semibold text-ink">
                        <?php echo e($line->lineTotal ? '₹'.$line->lineTotal : '—'); ?>

                    </p>

                    <button wire:click="removeItem(<?php echo e($line->item->id); ?>)" class="text-ink-faint hover:text-danger" aria-label="Remove item">&times;</button>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="mt-6 rounded-2xl border border-line bg-surface p-5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cart->coupon): ?>
                <div class="flex items-center justify-between">
                    <p class="text-sm">
                        Coupon <span class="font-code font-semibold text-brand"><?php echo e($cart->coupon->code); ?></span> applied - saved ₹<?php echo e($discount); ?>

                    </p>
                    <button wire:click="removeCoupon" class="text-sm text-ink-soft hover:text-danger">Remove</button>
                </div>
            <?php else: ?>
                <form wire:submit="applyCoupon" class="flex gap-3">
                    <input wire:model="couponCode" type="text" placeholder="Have a coupon code?" class="flex-1 rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                    <button type="submit" class="rounded-full border border-line px-5 py-2 text-sm font-medium text-ink hover:border-brand hover:text-brand">Apply</button>
                </form>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($couponError): ?>
                    <p class="mt-2 text-sm text-danger"><?php echo e($couponError); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mt-6 space-y-2 rounded-2xl border border-line bg-surface p-5">
            <div class="flex justify-between text-sm"><span class="text-ink-soft">Subtotal</span><span>₹<?php echo e(number_format($subtotal, 2)); ?></span></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cart->coupon): ?>
                <div class="flex justify-between text-sm text-success"><span>Coupon discount</span><span>-₹<?php echo e($discount); ?></span></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="flex justify-between border-t border-line pt-2 text-base font-semibold">
                <span>Total</span><span>₹<?php echo e(number_format(max(0, $subtotal - $discount), 2)); ?></span>
            </div>
        </div>

        <a href="<?php echo e(route('storefront.checkout')); ?>" wire:navigate class="mt-6 block w-full rounded-full bg-brand px-7 py-3.5 text-center text-base font-medium text-white hover:bg-brand-hover">
            Proceed to Checkout
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/cart-page.blade.php ENDPATH**/ ?>
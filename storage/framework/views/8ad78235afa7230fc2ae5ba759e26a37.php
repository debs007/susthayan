<div class="mx-auto max-w-3xl px-6 py-10" x-data
     x-on:payment-ready.window="
        var rzp = new Razorpay({
            key: $wire.razorpayKey,
            amount: $wire.amountInPaise,
            currency: 'INR',
            order_id: $wire.razorpayOrderId,
            name: 'Susthayan',
            description: 'Order #' + $wire.orderId,
            handler: function (response) {
                $wire.verifyPayment(response.razorpay_order_id, response.razorpay_payment_id, response.razorpay_signature);
            },
            theme: { color: '#208060' },
        });
        rzp.open();
     ">
    <h1 class="font-display text-2xl font-medium text-ink">Checkout</h1>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $cart || $cart->items->isEmpty()): ?>
        <p class="mt-6 text-sm text-ink-soft">Your cart is empty. <a href="<?php echo e(route('storefront.categories')); ?>" wire:navigate class="text-brand hover:underline">Go shopping</a>.</p>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
            <p class="mt-6 rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger"><?php echo e($errorMessage); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($needsApprovedPrescription): ?>
            <div class="mt-4 rounded-lg border border-warning/30 bg-warning/5 px-4 py-4 text-sm">
                <p class="font-medium text-ink">An approved prescription is needed for this order.</p>
                <p class="mt-1 text-ink-soft">Upload one from your account and wait for pharmacist approval, then come back to place this order.</p>
                <a href="<?php echo e(route('storefront.health-records')); ?>" wire:navigate class="mt-3 inline-block text-sm font-medium text-brand hover:underline">Go to My Prescriptions</a>
            </div>
        <?php elseif($cart->requiresPrescription() && ! $hasApprovedPrescription): ?>
            <p class="mt-4 rounded-lg bg-warning/10 px-4 py-3 text-sm text-warning">
                One or more items need a prescription. You'll need an approved prescription on file before this order can be placed.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <section class="mt-8">
            <h2 class="font-display text-lg font-medium text-ink">Delivery address</h2>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($addresses->isEmpty() && ! $showAddAddress): ?>
                <p class="mt-2 text-sm text-ink-soft">You don't have a saved address yet.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-3 space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <label class="flex items-start gap-3 rounded-xl border p-4 <?php echo e($selectedAddressId === $address->id ? 'border-brand bg-mist/30' : 'border-line'); ?>">
                        <input type="radio" wire:model.live="selectedAddressId" value="<?php echo e($address->id); ?>" class="mt-1 accent-brand">
                        <span>
                            <span class="block text-sm font-medium text-ink"><?php echo e($address->label ?? 'Address'); ?></span>
                            <span class="block text-xs text-ink-soft"><?php echo e($address->line1); ?>, <?php echo e($address->line2); ?>, <?php echo e($address->city); ?>, <?php echo e($address->state); ?> - <?php echo e($address->pincode); ?></span>
                        </span>
                    </label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddAddress): ?>
                <form wire:submit="saveNewAddress" class="mt-4 space-y-3 rounded-xl border border-line p-4">
                    <input wire:model="line1" type="text" placeholder="Address line 1" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['line1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <input wire:model="line2" type="text" placeholder="Address line 2 (optional)" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input wire:model="city" type="text" placeholder="City" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div>
                            <input wire:model="state" type="text" placeholder="State" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <input wire:model="pincode" type="text" placeholder="Pincode" class="w-full rounded-lg border border-line px-3 py-2 text-sm outline-none focus:border-brand">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['pincode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="flex gap-3">
                        <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-medium text-white hover:bg-brand-hover">Save address</button>
                        <button type="button" wire:click="$set('showAddAddress', false)" class="text-sm text-ink-soft hover:text-ink">Cancel</button>
                    </div>
                </form>
            <?php else: ?>
                <button wire:click="$set('showAddAddress', true)" class="mt-3 text-sm font-medium text-brand hover:underline">+ Add a new address</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        
        <?php
            $lines = $cart->items->map(function ($item) use ($cart) {
                $price = $item->product->priceFor($cart->franchise_id);
                return (object) ['item' => $item, 'lineTotal' => $price ? round($price->selling_price * $item->quantity, 2) : 0];
            });
            $subtotal = $lines->sum('lineTotal');
            $discount = $cart->couponDiscountAmount();
        ?>

        <section class="mt-8 rounded-2xl border border-line bg-surface p-5">
            <h2 class="font-display text-lg font-medium text-ink">Order summary</h2>
            <div class="mt-3 space-y-1.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-soft"><?php echo e($line->item->product->name); ?> &times; <?php echo e($line->item->quantity); ?></span>
                        <span>₹<?php echo e(number_format($line->lineTotal, 2)); ?></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <div class="mt-3 space-y-1.5 border-t border-line pt-3">
                <div class="flex justify-between text-sm"><span class="text-ink-soft">Subtotal</span><span>₹<?php echo e(number_format($subtotal, 2)); ?></span></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cart->coupon): ?>
                    <div class="flex justify-between text-sm text-success"><span>Coupon (<?php echo e($cart->coupon->code); ?>)</span><span>-₹<?php echo e($discount); ?></span></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex justify-between text-base font-semibold"><span>Total</span><span>₹<?php echo e(number_format(max(0, $subtotal - $discount), 2)); ?></span></div>
            </div>
        </section>

        <button
            wire:click="placeOrder"
            <?php if($cart->requiresPrescription() && ! $hasApprovedPrescription): echo 'disabled'; endif; ?>
            wire:loading.attr="disabled"
            wire:target="placeOrder"
            class="mt-6 w-full rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover disabled:bg-ink-faint"
        >
            <span wire:loading.remove wire:target="placeOrder">Place Order &amp; Pay</span>
            <span wire:loading wire:target="placeOrder">Placing order&hellip;</span>
        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/livewire/storefront/checkout-page.blade.php ENDPATH**/ ?>
<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'POS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'POS']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div
        x-data="posSale(<?php echo json_encode($catalog, 15, 512) ?>, <?php echo e($isPharmacist ? 'true' : 'false'); ?>)"
        class="grid gap-6 lg:grid-cols-3"
    >
        
        <div class="lg:col-span-2">
            <input
                type="text"
                x-model="search"
                placeholder="Scan barcode or search by name..."
                autofocus
                class="mb-4 w-full rounded-lg border border-border px-4 py-3 text-sm focus:border-primary-500 focus:outline-none"
            >

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <button
                        type="button"
                        @click="addToCart(product)"
                        class="rounded-xl border border-border bg-canvas-raised p-4 text-left hover:border-primary-500"
                        :disabled="product.available <= 0"
                        :class="{ 'opacity-40 cursor-not-allowed': product.available <= 0 }"
                    >
                        <p class="text-sm font-medium" x-text="product.name"></p>
                        <p class="mt-1 font-code text-xs text-ink-muted">₹<span x-text="product.price"></span> · <span x-text="product.available"></span> in stock</p>
                        <span x-show="product.prescription_required" class="mt-1.5 inline-block rounded-full bg-honey-50 px-1.5 py-0.5 text-[10px] font-medium uppercase text-honey-600">Rx</span>
                    </button>
                </template>

                <p x-show="filteredProducts.length === 0" class="col-span-full py-12 text-center text-sm text-ink-muted">
                    No products match "<span x-text="search"></span>".
                </p>
            </div>
        </div>

        
        <div>
            <form method="POST" action="<?php echo e(route('franchise.pos.store')); ?>" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                <?php echo csrf_field(); ?>

                <h2 class="font-display font-semibold">Current sale</h2>

                <div class="max-h-72 space-y-2 overflow-y-auto">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex items-center justify-between gap-2 border-b border-border pb-2 text-sm">
                            <div class="flex-1">
                                <p class="font-medium" x-text="item.name"></p>
                                <p class="font-code text-xs text-ink-muted">₹<span x-text="item.price"></span> each</p>
                            </div>
                            <input
                                type="number" min="1" :max="item.available"
                                x-model.number="item.quantity"
                                class="w-14 rounded-lg border border-border px-2 py-1 text-center text-xs"
                            >
                            <button type="button" @click="cart.splice(index, 1)" class="text-danger-500 hover:text-danger-600" aria-label="Remove">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="cart.length === 0" class="py-6 text-center text-sm text-ink-muted">Tap a product to add it.</p>
                </div>

                
                <template x-for="(item, index) in cart" :key="'input-'+item.id">
                    <div>
                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                    </div>
                </template>

                <div class="flex items-center justify-between border-t border-border pt-3 font-display text-lg font-semibold">
                    <span>Total</span>
                    <span>₹<span x-text="cartTotal.toFixed(2)"></span></span>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium">Payment mode</label>
                    <select name="payment_mode" class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-ink-muted">Walk-in name (optional)</label>
                        <input type="text" name="walk_in_name" class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-ink-muted">Phone (optional)</label>
                        <input type="text" name="walk_in_phone" class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    </div>
                </div>

                <div x-show="hasPrescriptionItem" x-cloak class="rounded-lg border border-honey-500/30 bg-honey-50 p-3">
                    <p class="mb-2 text-xs font-medium text-honey-600">
                        This sale includes a prescription medicine
                        <template x-if="!isPharmacist"><span> - only a pharmacist can complete it.</span></template>
                    </p>
                    <input
                        type="text" name="prescription_note"
                        placeholder="Doctor / registration reference"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                    >
                </div>

                <button
                    type="submit"
                    :disabled="cart.length === 0 || (hasPrescriptionItem && !isPharmacist)"
                    :class="{ 'opacity-40 cursor-not-allowed': cart.length === 0 || (hasPrescriptionItem && !isPharmacist) }"
                    class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600"
                >
                    Complete sale
                </button>
            </form>
        </div>
    </div>

    <script>
        function posSale(catalog, isPharmacist) {
            return {
                catalog,
                isPharmacist,
                search: '',
                cart: [],
                get filteredProducts() {
                    const q = this.search.trim().toLowerCase();
                    if (!q) return this.catalog.slice(0, 12);
                    return this.catalog.filter(p =>
                        p.name.toLowerCase().includes(q) || (p.barcode && p.barcode.toLowerCase() === q)
                    ).slice(0, 12);
                },
                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                get hasPrescriptionItem() {
                    return this.cart.some(item => item.prescription_required);
                },
                addToCart(product) {
                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        if (existing.quantity < product.available) existing.quantity++;
                        return;
                    }
                    this.cart.push({ ...product, quantity: 1 });
                    // A scanned barcode should clear the search box so the
                    // next scan starts from an empty field, not a stale query.
                    this.search = '';
                },
            };
        }
    </script>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/franchise/pos/index.blade.php ENDPATH**/ ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['product']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $price = $product->resolved_price ?? null;
    $hasDiscount = $price && (float) $price->mrp > (float) $price->selling_price;
    // Every catalogued product is always purchasable - a franchise's stock
    // (or lack of it) is a fulfillment-side concern handled after the
    // order is placed, not a reason to block the customer from ordering
    // it.
    $inStock = true;
?>

<div class="group flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-surface transition-shadow hover:shadow-md">
    
    <a href="<?php echo e(route('storefront.products.show', $product)); ?>" wire:navigate class="block">
        <div class="aspect-square w-full shrink-0 bg-surface p-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->image_url): ?>
                <img src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->name); ?>" class="h-full w-full object-contain" loading="lazy">
            <?php else: ?>
                <div class="flex h-full items-center justify-center text-ink-faint text-sm">No image</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </a>
    <div class="flex flex-1 flex-col gap-1 p-4">
        <div class="h-5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->prescription_required): ?>
                <span class="w-fit rounded-full bg-warning/10 px-2 py-0.5 text-[11px] font-medium text-warning">Rx required</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        <a href="<?php echo e(route('storefront.products.show', $product)); ?>" wire:navigate class="line-clamp-2 min-h-[2.5rem] text-sm font-medium text-ink hover:text-brand">
            <?php echo e($product->name); ?>

        </a>
        <p class="h-4 text-xs text-ink-faint"><?php echo e($product->unit); ?></p>
        <div class="mt-auto flex items-baseline gap-2 pt-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($price): ?>
                <span class="font-semibold text-ink">₹<?php echo e($price->selling_price); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDiscount): ?>
                    <span class="text-xs text-ink-faint line-through">₹<?php echo e($price->mrp); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <span class="text-xs text-ink-faint">Price unavailable</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="pt-2">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('storefront.add-to-cart', ['productId' => $product->id, 'inStock' => $inStock]);

$__keyOuter = $__key ?? null;

$__key = 'add-to-cart-'.$product->id;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1120133553-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/storefront/product-card.blade.php ENDPATH**/ ?>
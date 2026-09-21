<!DOCTYPE html>
<html lang="en" class="storefront">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'Susthayan - Your Health, Our Priority'); ?></title>
    <meta name="description" content="<?php echo e($description ?? 'Genuine medicines, at-home lab tests, and verified doctor appointments, delivered.'); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/storefront.css', 'resources/js/storefront.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="storefront flex min-h-screen flex-col bg-canvas text-ink">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('storefront.header');

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1910614256-0', $__key);

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

    <main class="flex-1">
        <?php echo e($slot); ?>

    </main>

    <?php if (isset($component)) { $__componentOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab)): ?>
<?php $attributes = $__attributesOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab; ?>
<?php unset($__attributesOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab)): ?>
<?php $component = $__componentOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab; ?>
<?php unset($__componentOriginal57bf4c7ea88c5e87d82fdfc5d85bdcab); ?>
<?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/layouts/storefront.blade.php ENDPATH**/ ?>
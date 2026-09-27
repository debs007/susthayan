<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'label', 'type' => 'text', 'required' => false, 'value' => null, 'step' => null, 'hint' => null]));

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

foreach (array_filter((['name', 'label', 'type' => 'text', 'required' => false, 'value' => null, 'step' => null, 'hint' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // old()/$errors need dot notation for nested arrays ('items.0.batch_no'),
    // but the name= attribute needs bracket notation ('items[0][batch_no]')
    // to actually submit as a nested array. Converting once here means
    // every caller can just pass whichever notation HTML needs and not
    // have to know about this mismatch.
    $dotName = str_replace([']', '['], ['', '.'], $name);
?>

<div>
    <label for="<?php echo e($name); ?>" class="mb-2 block text-sm font-medium">
        <?php echo e($label); ?><?php echo e($required ? ' *' : ''); ?>

    </label>
    <input
        type="<?php echo e($type); ?>"
        name="<?php echo e($name); ?>"
        id="<?php echo e($name); ?>"
        value="<?php echo e(old($dotName, $value)); ?>"
        <?php if($step): ?> step="<?php echo e($step); ?>" <?php endif; ?>
        <?php echo e($attributes->merge([
            'class' => 'w-full rounded-lg border px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary-500 '
                . ($errors->has($dotName) ? 'border-danger-500' : 'border-border'),
        ])); ?>

    >
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hint && ! $errors->has($dotName)): ?>
        <p class="mt-1.5 text-xs text-ink-muted"><?php echo e($hint); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$dotName];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1.5 text-xs text-danger-500"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/form/field.blade.php ENDPATH**/ ?>
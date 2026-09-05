<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'label', 'checked' => false, 'hint' => null]));

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

foreach (array_filter((['name', 'label', 'checked' => false, 'hint' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div>
    <label class="flex items-start gap-2.5">
        
        <input type="hidden" name="<?php echo e($name); ?>" value="0">
        <input
            type="checkbox"
            name="<?php echo e($name); ?>"
            value="1"
            <?php if(old($name, $checked)): echo 'checked'; endif; ?>
            class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500"
        >
        <span class="text-sm">
            <span class="font-medium"><?php echo e($label); ?></span>
            <?php if($hint): ?>
                <span class="block text-xs text-ink-muted"><?php echo e($hint); ?></span>
            <?php endif; ?>
        </span>
    </label>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-xs text-danger-500"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/form/checkbox.blade.php ENDPATH**/ ?>
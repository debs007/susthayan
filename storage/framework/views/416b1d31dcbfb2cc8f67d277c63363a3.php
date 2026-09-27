<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['action', 'from', 'to', 'csvAction' => null]));

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

foreach (array_filter((['action', 'from', 'to', 'csvAction' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<form method="GET" action="<?php echo e($action); ?>" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-border bg-canvas-raised p-4">
    <div>
        <label class="mb-1.5 block text-xs font-medium text-ink-muted">From</label>
        <input type="date" name="from" value="<?php echo e($from); ?>" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-ink-muted">To</label>
        <input type="date" name="to" value="<?php echo e($to); ?>" class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
    </div>
    <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
        Apply
    </button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($csvAction): ?>
        <a
            href="<?php echo e($csvAction); ?>&from=<?php echo e($from); ?>&to=<?php echo e($to); ?>"
            class="ml-auto rounded-lg border border-border px-4 py-2 text-sm font-medium text-ink hover:bg-canvas"
        >
            ↓ Export CSV
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</form>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/reports/date-filter.blade.php ENDPATH**/ ?>
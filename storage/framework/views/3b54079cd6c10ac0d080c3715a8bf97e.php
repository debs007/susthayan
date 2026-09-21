<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'GST Summary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'GST Summary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php echo $__env->make('admin.reports._tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if (isset($component)) { $__componentOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.reports.date-filter','data' => ['action' => route('admin.reports.gst-summary'),'from' => $summary['period']['from'],'to' => $summary['period']['to']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('reports.date-filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.reports.gst-summary')),'from' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($summary['period']['from']),'to' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($summary['period']['to'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc)): ?>
<?php $attributes = $__attributesOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc; ?>
<?php unset($__attributesOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc)): ?>
<?php $component = $__componentOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc; ?>
<?php unset($__componentOriginalcd2d7b6ea0a4828ce60c07fb7e3d93bc); ?>
<?php endif; ?>

    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Output GST (collected on sales)</p>
            <p class="mt-1.5 font-display text-2xl font-semibold">₹<?php echo e(number_format($summary['output_gst'], 2)); ?></p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Input GST (paid on purchases)</p>
            <p class="mt-1.5 font-display text-2xl font-semibold">₹<?php echo e(number_format($summary['input_gst'], 2)); ?></p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Net GST payable</p>
            <p class="mt-1.5 font-display text-2xl font-semibold text-honey-600">₹<?php echo e(number_format($summary['net_gst_payable'], 2)); ?></p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-honey-500/30 bg-honey-50 px-4 py-2.5 text-sm text-honey-600">
        Summary level only - a GSTR-1 filing needs HSN-code-wise and rate-wise breakdowns, which the Sales/Purchase registers have the underlying data for but this view doesn't group by yet.
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/reports/gst-summary.blade.php ENDPATH**/ ?>
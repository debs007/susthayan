<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => ''.e($supplier->name).' - Ledger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($supplier->name).' - Ledger']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <a href="<?php echo e(route('admin.vendors.outstanding')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to outstanding
    </a>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="font-display text-lg font-semibold"><?php echo e($supplier->name); ?></h2>
            <p class="text-sm text-ink-muted">Network-wide statement - every franchise's transactions with this supplier.</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised px-5 py-3 text-right">
            <p class="text-xs text-ink-muted">Closing balance</p>
            <p class="font-display text-xl font-semibold <?php echo e($ledger['closing_balance'] > 0 ? 'text-honey-600' : 'text-success-600'); ?>">
                ₹<?php echo e(number_format($ledger['closing_balance'], 2)); ?>

            </p>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Reference</th>
                    <th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 text-right font-medium">Debit</th>
                    <th class="px-5 py-3 text-right font-medium">Credit</th>
                    <th class="px-5 py-3 text-right font-medium">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ledger['entries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td class="px-5 py-3 font-code text-xs"><?php echo e(\Illuminate\Support\Carbon::parse($entry['date'])->format('d M Y')); ?></td>
                        <td class="px-5 py-3"><?php echo e($entry['reference'] ?? '—'); ?></td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($entry['type'] === 'invoice' ? 'bg-honey-50 text-honey-600' : 'bg-success-50 text-success-600'); ?>">
                                <?php echo e(ucfirst($entry['type'])); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs"><?php echo e($entry['debit'] > 0 ? '₹'.number_format($entry['debit'], 2) : '—'); ?></td>
                        <td class="px-5 py-3 text-right font-code text-xs"><?php echo e($entry['credit'] > 0 ? '₹'.number_format($entry['credit'], 2) : '—'); ?></td>
                        <td class="px-5 py-3 text-right font-code text-xs font-semibold">₹<?php echo e(number_format($entry['running_balance'], 2)); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No transactions with this supplier yet.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/vendors/ledger.blade.php ENDPATH**/ ?>
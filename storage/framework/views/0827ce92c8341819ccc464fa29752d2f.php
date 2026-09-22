<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; }
    table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: top; padding-bottom: 20px; }
    .franchise-name { font-size: 18px; font-weight: bold; color: #0A4A38; }
    .muted { color: #666; font-size: 11px; }
    .invoice-title { font-size: 22px; font-weight: bold; text-align: right; color: #0A4A38; }
    .meta-table td { padding: 2px 0; font-size: 11px; }
    .meta-label { color: #666; padding-right: 10px; }
    .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; font-size: 12px; }
    .items-table { margin-top: 10px; border: 1px solid #ddd; }
    .items-table th { background: #f0f4f2; text-align: left; padding: 6px 8px; font-size: 11px; border-bottom: 1px solid #ddd; }
    .items-table td { padding: 6px 8px; font-size: 11px; border-bottom: 1px solid #eee; }
    .text-right { text-align: right; }
    .totals-table { width: 260px; margin-left: auto; margin-top: 10px; }
    .totals-table td { padding: 3px 8px; font-size: 11px; }
    .totals-table .grand-total td { font-weight: bold; font-size: 13px; border-top: 1px solid #333; padding-top: 6px; }
    .footer { margin-top: 30px; font-size: 10px; color: #888; text-align: center; }
</style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->franchise): ?>
                    <div class="franchise-name"><?php echo e($order->franchise->name); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->franchise->address): ?>
                        <div class="muted"><?php echo e($order->franchise->address); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="muted">
                        <?php echo e(collect([$order->franchise->city, $order->franchise->state, $order->franchise->pincode])->filter()->implode(', ')); ?>

                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->franchise->gstin): ?>
                        <div class="muted">GSTIN: <?php echo e($order->franchise->gstin); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->franchise->drug_license_number): ?>
                        <div class="muted">Drug Licence: <?php echo e($order->franchise->drug_license_number); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <div class="franchise-name">Susthayan</div>
                    <div class="muted">Fulfilling store: to be assigned</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </td>
            <td style="width: 40%;">
                <div class="invoice-title">TAX INVOICE</div>
                <table class="meta-table" style="margin-top: 8px;">
                    <tr><td class="meta-label text-right" style="width: 50%;">Invoice #</td><td class="text-right"><?php echo e($invoice->invoice_number); ?></td></tr>
                    <tr><td class="meta-label text-right">Date</td><td class="text-right"><?php echo e($invoice->generated_at->format('d M Y')); ?></td></tr>
                    <tr><td class="meta-label text-right">Order #</td><td class="text-right"><?php echo e($order->id); ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Billed to</div>
    <div><?php echo e($order->user->name); ?></div>
    <div class="muted">+91 <?php echo e($order->user->mobile); ?></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->fulfillment_type === 'delivery' && $order->address): ?>
        <div class="muted">
            <?php echo e(collect([$order->address->line1, $order->address->line2, $order->address->city, $order->address->state, $order->address->pincode])->filter()->implode(', ')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 46%;">Item</th>
                <th class="text-right" style="width: 12%;">Qty</th>
                <th class="text-right" style="width: 14%;">Unit Price</th>
                <th class="text-right" style="width: 10%;">GST %</th>
                <th class="text-right" style="width: 18%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($item->product->name); ?></td>
                    <td class="text-right"><?php echo e($item->quantity); ?></td>
                    <td class="text-right">₹<?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td class="text-right"><?php echo e($item->tax_percentage); ?>%</td>
                    <td class="text-right">₹<?php echo e(number_format($item->total_price, 2)); ?></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>

    <table class="totals-table">
        <tr><td>Subtotal</td><td class="text-right">₹<?php echo e(number_format($invoice->subtotal_amount, 2)); ?></td></tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount_amount > 0): ?>
            <tr><td>Discount</td><td class="text-right">-₹<?php echo e(number_format($order->discount_amount, 2)); ?></td></tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <tr><td>GST</td><td class="text-right">₹<?php echo e(number_format($invoice->gst_amount, 2)); ?></td></tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->delivery_charge > 0): ?>
            <tr><td>Delivery</td><td class="text-right">₹<?php echo e(number_format($order->delivery_charge, 2)); ?></td></tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <tr class="grand-total"><td>Total</td><td class="text-right">₹<?php echo e(number_format($invoice->total_amount, 2)); ?></td></tr>
    </table>

    <div class="footer">This is a computer-generated invoice and does not require a signature.</div>
</body>
</html>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/invoices/pdf.blade.php ENDPATH**/ ?>
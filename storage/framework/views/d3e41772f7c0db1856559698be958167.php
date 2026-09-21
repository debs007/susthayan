<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Home Banners']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Home Banners']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.errors','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00)): ?>
<?php $attributes = $__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00; ?>
<?php unset($__attributesOriginal2347dc4dfde5cbda367ab4d22dfe8d00); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00)): ?>
<?php $component = $__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00; ?>
<?php unset($__componentOriginal2347dc4dfde5cbda367ab4d22dfe8d00); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Home Banners</h1>
        <p class="text-sm text-ink-muted">Two separate sets - the mobile app and the website each have their own banners, since they need very different image shapes. Lower order number shows first.</p>
    </div>

    
    <section class="mb-10">
        <h2 class="mb-4 font-display font-semibold">Website</h2>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <?php if (isset($component)) { $__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.banner-list','data' => ['banners' => $webBanners]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.banner-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['banners' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($webBanners)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd)): ?>
<?php $attributes = $__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd; ?>
<?php unset($__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd)): ?>
<?php $component = $__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd; ?>
<?php unset($__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd); ?>
<?php endif; ?>
            </div>
            <div>
                <?php if (isset($component)) { $__componentOriginale73aa9789c3afe3ed82677a8e144fb6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.banner-upload-form','data' => ['platform' => 'web','coupons' => $coupons]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.banner-upload-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['platform' => 'web','coupons' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($coupons)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d)): ?>
<?php $attributes = $__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d; ?>
<?php unset($__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale73aa9789c3afe3ed82677a8e144fb6d)): ?>
<?php $component = $__componentOriginale73aa9789c3afe3ed82677a8e144fb6d; ?>
<?php unset($__componentOriginale73aa9789c3afe3ed82677a8e144fb6d); ?>
<?php endif; ?>
            </div>
        </div>
    </section>

    
    <section>
        <h2 class="mb-4 font-display font-semibold">Mobile App</h2>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <?php if (isset($component)) { $__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.banner-list','data' => ['banners' => $mobileBanners]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.banner-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['banners' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mobileBanners)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd)): ?>
<?php $attributes = $__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd; ?>
<?php unset($__attributesOriginal2ee2d426a4e6ad01b52e2306e695f8fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd)): ?>
<?php $component = $__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd; ?>
<?php unset($__componentOriginal2ee2d426a4e6ad01b52e2306e695f8fd); ?>
<?php endif; ?>
            </div>
            <div>
                <?php if (isset($component)) { $__componentOriginale73aa9789c3afe3ed82677a8e144fb6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.banner-upload-form','data' => ['platform' => 'mobile','coupons' => $coupons]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.banner-upload-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['platform' => 'mobile','coupons' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($coupons)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d)): ?>
<?php $attributes = $__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d; ?>
<?php unset($__attributesOriginale73aa9789c3afe3ed82677a8e144fb6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale73aa9789c3afe3ed82677a8e144fb6d)): ?>
<?php $component = $__componentOriginale73aa9789c3afe3ed82677a8e144fb6d; ?>
<?php unset($__componentOriginale73aa9789c3afe3ed82677a8e144fb6d); ?>
<?php endif; ?>
            </div>
        </div>
    </section>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/home-banners/index.blade.php ENDPATH**/ ?>
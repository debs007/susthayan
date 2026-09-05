<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Edit '.e($franchise->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit '.e($franchise->name).'']); ?>
    <a href="<?php echo e(route('admin.franchises.index')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to franchises
    </a>

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

    <?php if(session('success')): ?>
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="max-w-2xl space-y-6">
        
        <form method="POST" action="<?php echo e(route('admin.franchises.update', $franchise)); ?>" class="space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Basic info</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'name','label' => 'Franchise name','value' => $franchise->name,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Franchise name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->name),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'phone','label' => 'Phone','value' => $franchise->phone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone','label' => 'Phone','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->phone)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'email','label' => 'Email','type' => 'email','value' => $franchise->email]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'email','label' => 'Email','type' => 'email','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->email)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                </div>
            </div>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Compliance</h2>
                <div class="grid grid-cols-2 gap-6">
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'gstin','label' => 'GSTIN','value' => $franchise->gstin,'hint' => '15 characters, e.g. 22AAAAA0000A1Z5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'gstin','label' => 'GSTIN','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->gstin),'hint' => '15 characters, e.g. 22AAAAA0000A1Z5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'drug_license_number','label' => 'Drug license number','value' => $franchise->drug_license_number]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'drug_license_number','label' => 'Drug license number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->drug_license_number)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                </div>
            </div>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Location</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <?php if (isset($component)) { $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.textarea','data' => ['name' => 'address','label' => 'Address','value' => $franchise->address]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'address','label' => 'Address','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->address)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $attributes = $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $component = $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'city','label' => 'City','value' => $franchise->city]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'city','label' => 'City','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->city)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'state','label' => 'State','value' => $franchise->state]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'state','label' => 'State','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->state)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'pincode','label' => 'Pincode','value' => $franchise->pincode]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pincode','label' => 'Pincode','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->pincode)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    <div class="grid grid-cols-2 gap-3">
                        <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'latitude','label' => 'Latitude','type' => 'number','step' => '0.0000001','value' => $franchise->latitude]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'latitude','label' => 'Latitude','type' => 'number','step' => '0.0000001','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->latitude)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'longitude','label' => 'Longitude','type' => 'number','step' => '0.0000001','value' => $franchise->longitude]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'longitude','label' => 'Longitude','type' => 'number','step' => '0.0000001','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->longitude)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Settlement</h2>
                <div class="grid grid-cols-2 gap-6">
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'commission_percentage','label' => 'Commission % (platform\'s share)','type' => 'number','step' => '0.01','value' => $franchise->commission_percentage,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'commission_percentage','label' => 'Commission % (platform\'s share)','type' => 'number','step' => '0.01','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->commission_percentage),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'status','label' => 'Status','value' => $franchise->status,'options' => ['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','label' => 'Status','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->status),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $attributes = $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $component = $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
                </div>
            </div>

            <div class="flex gap-3 border-t border-border pt-6">
                <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Save changes
                </button>
            </div>
        </form>

        
        <form method="POST" action="<?php echo e(route('admin.franchises.bank-details', $franchise)); ?>" class="space-y-5 rounded-xl border border-border bg-canvas-raised p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <div>
                <h2 class="font-display font-semibold">Bank details</h2>
                <p class="mt-1 text-sm text-ink-muted">Required before a Franchise Settlement payout can be released to this franchise.</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'bank_account_name','label' => 'Account holder name','value' => $franchise->bank_account_name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bank_account_name','label' => 'Account holder name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->bank_account_name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                </div>
                <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'bank_account_number','label' => 'Account number','value' => $franchise->bank_account_number]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bank_account_number','label' => 'Account number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->bank_account_number)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'bank_ifsc','label' => 'IFSC code','value' => $franchise->bank_ifsc]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bank_ifsc','label' => 'IFSC code','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($franchise->bank_ifsc)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $attributes = $__attributesOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__attributesOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal45920e144996b26f3340500ed9e02bd3)): ?>
<?php $component = $__componentOriginal45920e144996b26f3340500ed9e02bd3; ?>
<?php unset($__componentOriginal45920e144996b26f3340500ed9e02bd3); ?>
<?php endif; ?>
            </div>

            <div class="flex gap-3 border-t border-border pt-6">
                <button type="submit" class="rounded-lg bg-honey-500 px-4 py-2 text-sm font-medium text-white hover:bg-honey-600">
                    Save bank details
                </button>
            </div>
        </form>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/franchises/edit.blade.php ENDPATH**/ ?>
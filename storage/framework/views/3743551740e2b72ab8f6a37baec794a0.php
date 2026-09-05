<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Edit - '.e($doctor->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit - '.e($doctor->name).'']); ?>
    <a href="<?php echo e(route('admin.doctors.index')); ?>" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Doctors
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

    <?php
        $currentAffiliations = $doctor->affiliations->keyBy('hospital_id');
    ?>

    <form method="POST" action="<?php echo e(route('admin.doctors.update', $doctor)); ?>" enctype="multipart/form-data" class="max-w-3xl space-y-10 rounded-xl border border-border bg-canvas-raised p-8">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        <div>
            <h2 class="mb-5 border-b border-border pb-3 font-display font-semibold">Doctor details</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <?php if($doctor->photo_url): ?>
                        <img src="<?php echo e($doctor->photo_url); ?>" alt="" class="mb-3 h-20 w-20 rounded-full border border-border object-cover">
                    <?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'name','label' => 'Full name','required' => true,'value' => old('name', $doctor->name)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Full name','required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('name', $doctor->name))]); ?>
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
                <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'department_id','label' => 'Department','required' => true,'value' => old('department_id', $doctor->department_id),'options' => $departments->pluck('name', 'id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'department_id','label' => 'Department','required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('department_id', $doctor->department_id)),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($departments->pluck('name', 'id'))]); ?>
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
                <?php if (isset($component)) { $__componentOriginal45920e144996b26f3340500ed9e02bd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal45920e144996b26f3340500ed9e02bd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'degree','label' => 'Degree','required' => true,'value' => old('degree', $doctor->degree)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'degree','label' => 'Degree','required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('degree', $doctor->degree))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.field','data' => ['name' => 'years_of_experience','label' => 'Years of experience','type' => 'number','value' => old('years_of_experience', $doctor->years_of_experience)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'years_of_experience','label' => 'Years of experience','type' => 'number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('years_of_experience', $doctor->years_of_experience))]); ?>
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
                <div>
                    <label for="photo" class="block text-sm font-medium text-ink">Replace photo</label>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
                </div>
                <div class="col-span-2">
                    <?php if (isset($component)) { $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.textarea','data' => ['name' => 'bio','label' => 'Short bio','rows' => 2,'value' => old('bio', $doctor->bio)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bio','label' => 'Short bio','rows' => 2,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('bio', $doctor->bio))]); ?>
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
            </div>
        </div>

        <div>
            <h2 class="mb-2 border-b border-border pb-3 font-display font-semibold">Hospital affiliations</h2>
            <p class="mb-4 text-xs text-ink-muted">Unchecking a hospital removes this doctor's affiliation and schedule there entirely.</p>

            <div class="space-y-4">
                <?php $__currentLoopData = $hospitals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hospital): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $affiliation = $currentAffiliations->get($hospital->id);
                        $currentDays = $affiliation ? $affiliation->visitDays->pluck('day_of_week')->all() : [];
                        $firstVisitDay = $affiliation?->visitDays->first();
                    ?>
                    <div class="rounded-lg border border-border p-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" class="hospital-checkbox h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500" data-hospital-fields="hospital-fields-<?php echo e($hospital->id); ?>" name="hospitals[<?php echo e($hospital->id); ?>][selected]" value="1" <?php if($affiliation): echo 'checked'; endif; ?>>
                            <span class="font-medium text-sm"><?php echo e($hospital->name); ?></span>
                            <span class="text-xs text-ink-muted"><?php echo e($hospital->city); ?></span>
                        </label>

                        <div id="hospital-fields-<?php echo e($hospital->id); ?>" class="mt-3 space-y-3" style="opacity: <?php echo e($affiliation ? '1' : '0.4'); ?>">
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs text-ink-muted">Consultation charge (₹)</label>
                                    <input type="number" step="0.01" name="hospitals[<?php echo e($hospital->id); ?>][charge]" value="<?php echo e($affiliation?->consultation_charge); ?>" <?php if(! $affiliation): echo 'disabled'; endif; ?> class="hospital-input mt-1 w-full rounded-lg border border-border bg-canvas px-2.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-ink-muted">Visit start time</label>
                                    <input type="time" name="hospitals[<?php echo e($hospital->id); ?>][start_time]" value="<?php echo e($firstVisitDay?->start_time); ?>" <?php if(! $affiliation): echo 'disabled'; endif; ?> class="hospital-input mt-1 w-full rounded-lg border border-border bg-canvas px-2.5 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-ink-muted">Visit end time</label>
                                    <input type="time" name="hospitals[<?php echo e($hospital->id); ?>][end_time]" value="<?php echo e($firstVisitDay?->end_time); ?>" <?php if(! $affiliation): echo 'disabled'; endif; ?> class="hospital-input mt-1 w-full rounded-lg border border-border bg-canvas px-2.5 py-2 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-ink-muted">Visit days</label>
                                <div class="mt-1.5 flex flex-wrap gap-3">
                                    <?php $__currentLoopData = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-1.5 text-xs">
                                            <input type="checkbox" <?php if(! $affiliation): echo 'disabled'; endif; ?> <?php if(in_array($value, $currentDays)): echo 'checked'; endif; ?> class="hospital-input h-3.5 w-3.5 rounded border-border text-primary-500 focus:ring-primary-500" name="hospitals[<?php echo e($hospital->id); ?>][days][]" value="<?php echo e($value); ?>">
                                            <?php echo e($label); ?>

                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
            Save changes
        </button>
    </form>

    <script>
        document.querySelectorAll('.hospital-checkbox').forEach((checkbox) => {
            const fieldsContainer = document.getElementById(checkbox.dataset.hospitalFields);
            const inputs = fieldsContainer.querySelectorAll('.hospital-input');
            checkbox.addEventListener('change', () => {
                inputs.forEach((input) => { input.disabled = !checkbox.checked; });
                fieldsContainer.style.opacity = checkbox.checked ? '1' : '0.4';
            });
        });
    </script>
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
<?php /**PATH /var/www/susthayan/susthayan/resources/views/admin/doctors/edit.blade.php ENDPATH**/ ?>
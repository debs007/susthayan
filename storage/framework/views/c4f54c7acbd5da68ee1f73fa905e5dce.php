<?php if (isset($component)) { $__componentOriginal1e6834b7596effc838ab3adb1475b477 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1e6834b7596effc838ab3adb1475b477 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.guest','data' => ['title' => 'Forgot password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.guest'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Forgot password']); ?>
    <h1 class="font-display text-2xl font-semibold text-ink">Forgot password</h1>
    <p class="mt-1.5 text-sm text-ink-muted">Enter your mobile number and we'll send a reset code by SMS.</p>

    <?php if($errors->any()): ?>
        <div class="mt-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-3 text-sm text-danger-600">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('forgot-password.send')); ?>" class="mt-8 space-y-5">
        <?php echo csrf_field(); ?>

        <div>
            <label for="mobile" class="block text-sm font-medium text-ink">Mobile number</label>
            <input
                type="text"
                name="mobile"
                id="mobile"
                value="<?php echo e(old('mobile')); ?>"
                autofocus
                autocomplete="tel"
                required
                class="mt-1.5 block w-full rounded-lg border border-border bg-canvas-raised px-3.5 py-2.5 text-ink placeholder:text-ink-muted/60 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                placeholder="9999999999"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-primary-500 px-4 py-2.5 font-medium text-white transition hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
        >
            Send reset code
        </button>
    </form>

    <p class="mt-8 text-xs text-ink-muted">
        <a href="<?php echo e(route('login')); ?>" class="font-medium text-primary-500 hover:underline">Back to sign in</a>
    </p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1e6834b7596effc838ab3adb1475b477)): ?>
<?php $attributes = $__attributesOriginal1e6834b7596effc838ab3adb1475b477; ?>
<?php unset($__attributesOriginal1e6834b7596effc838ab3adb1475b477); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1e6834b7596effc838ab3adb1475b477)): ?>
<?php $component = $__componentOriginal1e6834b7596effc838ab3adb1475b477; ?>
<?php unset($__componentOriginal1e6834b7596effc838ab3adb1475b477); ?>
<?php endif; ?>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/auth/forgot-password.blade.php ENDPATH**/ ?>
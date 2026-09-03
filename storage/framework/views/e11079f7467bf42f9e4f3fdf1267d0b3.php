<?php if($errors->any()): ?>
    <div class="mb-8 rounded-lg border border-danger-500/30 bg-danger-50 px-5 py-4 text-sm text-danger-600">
        <p class="mb-1.5 font-medium">
            <?php echo e($errors->count() === 1 ? 'Please fix the following:' : "Please fix the following {$errors->count()} things:"); ?>

        </p>
        <ul class="list-disc space-y-1 pl-5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/form/errors.blade.php ENDPATH**/ ?>
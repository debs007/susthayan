<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'Sign in'); ?> · Susthayan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full font-body text-[15px]">
    <div class="min-h-full grid lg:grid-cols-2">
        
        <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-20">
            <div class="mx-auto w-full max-w-sm">
                <div class="flex items-center gap-2.5 mb-10">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-primary-500 text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                            <path d="M12 3v18M3 12h18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="font-display font-semibold text-lg tracking-tight">Susthayan</span>
                </div>

                <?php echo e($slot); ?>

            </div>
        </div>

        
        <div class="hidden lg:flex relative overflow-hidden bg-primary-700 text-white flex-col justify-between p-14">
            <div class="absolute inset-0 grid grid-cols-8 gap-2 p-8 opacity-40" aria-hidden="true">
                <?php
                    $pattern = [2,0,1,0,2,1,0,2,0,1,2,0,1,2,0,1,1,0,2,1,0,0,2,1,0,1,0,2,1,0,1,2,2,1,0,1,0,2,0,1,0,2,1,0,1,0,2,1];
                    $tones = ['bg-success-500/70', 'bg-honey-500/70', 'bg-danger-500/70'];
                ?>
                <?php $__currentLoopData = $pattern; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="aspect-square rounded-md <?php echo e($tones[$tone]); ?>"></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="relative">
                <p class="font-display text-2xl font-medium leading-snug max-w-sm">
                    Every batch tracked.<br>Every expiry respected.
                </p>
            </div>

            <div class="relative flex items-center gap-2 text-sm text-white/70">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M12 2 4 5v6c0 5 3.4 8.7 8 9 4.6-.3 8-4 8-9V5l-8-3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
                Staff access is logged and audited
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/layouts/guest.blade.php ENDPATH**/ ?>
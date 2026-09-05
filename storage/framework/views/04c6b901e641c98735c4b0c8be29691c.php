<?php
    $user = auth('web')->user();
    $isAdminPortal = $user->hasAnyRole(['Super Admin', 'Accountant']);

    $franchiseNav = [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'franchise.dashboard', 'live' => true],
        ],
        'Operations' => [
            ['label' => 'Orders', 'route' => 'franchise.orders.index', 'live' => true],
            ['label' => 'POS / Walk-in', 'route' => 'franchise.pos.index', 'live' => true],
            ['label' => 'Prescriptions', 'route' => 'franchise.prescriptions.index', 'live' => true],
        ],
        'Inventory' => [
            ['label' => 'Stock', 'route' => 'franchise.inventory.index', 'live' => true],
            ['label' => 'Purchase Orders', 'route' => 'franchise.purchase-orders.index', 'active' => 'franchise.purchase-orders.*', 'live' => true],
        ],
        'Finance' => [
            ['label' => 'Settlements', 'route' => 'franchise.settlements.index', 'live' => true],
        ],
    ];

    $adminNav = [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'live' => true],
        ],
        'Admin' => [
            ['label' => 'Franchises', 'route' => 'admin.franchises.index', 'active' => 'admin.franchises.*', 'live' => true],
            ['label' => 'Catalogue', 'route' => 'admin.products.index', 'active' => 'admin.products.*|admin.categories.*', 'live' => true],
            ['label' => 'Brands', 'route' => 'admin.brands.index', 'active' => 'admin.brands.*', 'live' => true],
            ['label' => 'Lab Tests', 'route' => 'admin.lab-tests.index', 'active' => 'admin.lab-tests.*|admin.lab-test-categories.*|admin.lab-test-blocked-dates.*', 'live' => true],
            ['label' => 'Lab Centers', 'route' => 'admin.lab-centers.index', 'active' => 'admin.lab-centers.*', 'live' => true],
            ['label' => 'Home Banners', 'route' => 'admin.home-banners.index', 'active' => 'admin.home-banners.*', 'live' => true],
            ['label' => 'Coupons', 'route' => 'admin.coupons.index', 'active' => 'admin.coupons.*', 'live' => true],
            ['label' => 'Health Articles', 'route' => 'admin.health-articles.index', 'active' => 'admin.health-articles.*', 'live' => true],
            ['label' => 'Departments', 'route' => 'admin.departments.index', 'active' => 'admin.departments.*', 'live' => true],
            ['label' => 'Hospitals', 'route' => 'admin.hospitals.index', 'active' => 'admin.hospitals.*', 'live' => true],
            ['label' => 'Doctors', 'route' => 'admin.doctors.index', 'active' => 'admin.doctors.*', 'live' => true],
            ['label' => 'Suppliers & PO', 'route' => 'admin.vendors.index', 'active' => 'admin.vendors.*', 'live' => true],
        ],
        'Finance' => [
            ['label' => 'Settlements', 'route' => 'admin.settlements.index', 'live' => true],
            ['label' => 'Reports', 'route' => 'admin.reports.sales-register', 'active' => 'admin.reports.*', 'live' => true],
        ],
        'System' => [
            ['label' => 'Users & Roles', 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'live' => true],
            ['label' => 'Audit Log', 'route' => 'admin.audit-log.index', 'live' => true],
        ],
    ];

    $navSections = $isAdminPortal ? $adminNav : $franchiseNav;
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'Dashboard'); ?> · Susthayan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full font-body text-[15px] text-ink" x-data="{ mobileNavOpen: false }">
    <div class="min-h-full lg:grid lg:grid-cols-[16rem_1fr]">

        
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-primary-700 text-white transition-transform lg:static lg:translate-x-0"
            :class="{ '!translate-x-0': mobileNavOpen }"
        >
            <div class="flex h-16 items-center gap-2.5 px-6">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-500">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4.5 w-4.5" aria-hidden="true">
                        <path d="M12 3v18M3 12h18" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="font-display font-semibold tracking-tight">Susthayan</span>
            </div>

            <nav class="mt-4 space-y-4 overflow-y-auto px-3 pb-6" style="max-height: calc(100vh - 8rem);">
                <?php $__currentLoopData = $navSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $heading => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <p class="px-3 pb-1.5 text-[11px] font-semibold uppercase tracking-wider text-white/40"><?php echo e($heading); ?></p>
                        <div class="space-y-0.5">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($item['live'] && Route::has($item['route'])): ?>
                                    <a
                                        href="<?php echo e(route($item['route'])); ?>"
                                        class="flex items-center rounded-lg px-3 py-2 text-sm font-medium <?php echo e(request()->routeIs($item['active'] ?? $item['route']) ? 'bg-primary-600 text-white' : 'text-white/75 hover:bg-primary-600/60 hover:text-white'); ?>"
                                    >
                                        <?php echo e($item['label']); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-white/35">
                                        <?php echo e($item['label']); ?>

                                        <span class="rounded bg-white/10 px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide">Soon</span>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>

            <div class="absolute bottom-0 w-full border-t border-white/10 p-4">
                <p class="px-2 text-xs text-white/50">
                    <?php echo e($isAdminPortal ? 'Admin Portal' : 'Franchise Portal'); ?>

                    <?php if($user->franchise): ?>
                        · <?php echo e($user->franchise->name); ?>

                    <?php endif; ?>
                </p>
            </div>
        </aside>

        <div
            x-show="mobileNavOpen"
            x-cloak
            @click="mobileNavOpen = false"
            class="fixed inset-0 z-30 bg-ink/40 lg:hidden"
        ></div>

        
        <div class="flex min-h-full flex-col">
            <header class="flex h-16 items-center justify-between border-b border-border bg-canvas-raised px-4 sm:px-8">
                <div class="flex items-center gap-3">
                    <button @click="mobileNavOpen = true" class="rounded-md p-1.5 text-ink-muted hover:bg-canvas lg:hidden" aria-label="Open navigation">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <h1 class="font-display text-lg font-semibold"><?php echo e($title ?? 'Dashboard'); ?></h1>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2.5 rounded-lg px-2 py-1.5 hover:bg-canvas">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 font-display text-sm font-semibold text-primary-600">
                            <?php echo e(Str::of($user->name)->substr(0, 1)); ?>

                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-sm font-medium leading-none"><?php echo e($user->name); ?></span>
                            <span class="mt-0.5 block text-xs text-ink-muted"><?php echo e($user->getRoleNames()->first()); ?></span>
                        </span>
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        x-transition
                        class="absolute right-0 mt-2 w-44 rounded-lg border border-border bg-canvas-raised py-1 shadow-lg"
                    >
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-ink hover:bg-canvas">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-8 sm:px-8">
                <?php echo e($slot); ?>

            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>
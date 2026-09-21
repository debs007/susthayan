<?php if (isset($component)) { $__componentOriginal51ea8e071ed038a4c6542abfe5470d94 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51ea8e071ed038a4c6542abfe5470d94 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.storefront','data' => ['title' => 'Susthayan - Your Health, Our Priority']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.storefront'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Susthayan - Your Health, Our Priority']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    
    <section class="relative overflow-hidden border-b border-line bg-gradient-to-b from-mist/60 to-canvas">
        
        <svg class="pointer-events-none absolute inset-0 h-full w-full text-brand" viewBox="0 0 1200 500" preserveAspectRatio="xMidYMid slice" fill="none" aria-hidden="true">
            <g opacity="0.07" fill="currentColor">
                <path d="M120 60c60-30 130-10 150 45s-20 115-80 130-125-15-135-70 5-75 65-105Z" />
                <path d="M950 40c-10 60-70 95-125 85s-95-70-75-125 85-80 135-55 75 35 65 95Z" />
                <path d="M60 380c40-45 110-50 145-10s15 105-30 140-110 25-140-15 -15-75 25-115Z" />
                <path d="M1080 320c-35 40-100 40-130 0s-10-100 35-130 100-20 125 20 5 70-30 110Z" />
            </g>
            <g opacity="0.06" stroke="currentColor" stroke-width="10" stroke-linecap="round">
                <rect x="230" y="220" width="120" height="46" rx="23" transform="rotate(-25 290 243)" />
                <line x1="270" y1="205" x2="310" y2="281" transform="rotate(-25 290 243)" />
                <rect x="820" y="140" width="100" height="40" rx="20" transform="rotate(18 870 160)" />
                <line x1="855" y1="128" x2="885" y2="192" transform="rotate(18 870 160)" />
                <rect x="700" y="380" width="110" height="42" rx="21" transform="rotate(-12 755 401)" />
                <line x1="740" y1="365" x2="770" y2="437" transform="rotate(-12 755 401)" />
            </g>
        </svg>

        <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-6 py-16 lg:grid-cols-2 lg:py-24">
            <div class="<?php echo e($banners->isEmpty() ? 'lg:col-span-2 lg:mx-auto lg:max-w-2xl lg:text-center' : ''); ?>">
                
                <div
                    x-data="{
                        line: null,
                        init() {
                            if (! navigator.geolocation) { return }
                            navigator.geolocation.getCurrentPosition(
                                (pos) => {
                                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`)
                                        .then((r) => r.json())
                                        .then((data) => {
                                            const a = data.address || {};
                                            const area = a.suburb || a.neighbourhood || a.village || a.town || a.city_district || '';
                                            const city = a.city || a.town || a.state_district || '';
                                            const parts = [area, city, a.postcode].filter(Boolean);
                                            if (parts.length) { this.line = parts.join(', '); }
                                        })
                                        .catch(() => {});
                                },
                                () => {},
                                { timeout: 8000 }
                            );
                        },
                    }"
                    x-show="line"
                    x-cloak
                    class="mb-3 flex items-center gap-1.5 text-sm font-medium text-brand <?php echo e($banners->isEmpty() ? 'lg:justify-center' : ''); ?>"
                >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none"><path d="M10 2c-3 0-5.5 2.3-5.5 5.6C4.5 11.5 10 18 10 18s5.5-6.5 5.5-10.4C15.5 4.3 13 2 10 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" /><circle cx="10" cy="7.6" r="2" stroke="currentColor" stroke-width="1.5" /></svg>
                    <span>Delivering to <span x-text="line" class="font-semibold"></span></span>
                </div>

                <h1 class="font-display text-4xl font-medium leading-[1.15] text-forest lg:text-5xl">
                    Medicines, lab tests, and doctors &mdash; sorted, without leaving home.
                </h1>
                <p class="mt-5 max-w-md text-ink-soft <?php echo e($banners->isEmpty() ? 'lg:mx-auto' : ''); ?>">
                    Susthayan brings genuine medicines, at-home lab collection, and verified doctor
                    appointments together, so managing your family's health takes minutes, not a whole afternoon.
                </p>
                <div class="mt-8 flex flex-wrap gap-3 <?php echo e($banners->isEmpty() ? 'lg:justify-center' : ''); ?>">
                    <a href="<?php echo e(route('storefront.categories')); ?>" wire:navigate class="rounded-full bg-brand px-7 py-3.5 text-base font-medium text-white hover:bg-brand-hover">
                        Browse medicines
                    </a>
                    <a href="<?php echo e(route('storefront.lab-tests')); ?>" wire:navigate class="rounded-full border border-line px-7 py-3.5 text-base font-medium text-ink hover:border-brand hover:text-brand">
                        Book a lab test
                    </a>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banners->isNotEmpty()): ?>
                <div
                    class="relative aspect-[4/3] overflow-hidden rounded-3xl bg-mist"
                    x-data="{ current: 0, total: <?php echo e($banners->count()); ?> }"
                    x-init="setInterval(() => { current = (current + 1) % total }, 4000)"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div
                            class="absolute inset-0 transition-opacity duration-[3000ms] ease-in-out"
                            :class="current === <?php echo e($i); ?> ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
                            :aria-hidden="current !== <?php echo e($i); ?>"
                        >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banner->image_url): ?>
                                <img src="<?php echo e($banner->image_url); ?>" alt="<?php echo e($banner->headline); ?>" class="absolute inset-0 h-full w-full object-cover">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banner->badge_text): ?>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-white/90"><?php echo e($banner->badge_text); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <p class="mt-1 font-display text-xl font-medium text-white"><?php echo e($banner->headline); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banner->subtitle): ?>
                                    <p class="text-sm text-white/85"><?php echo e($banner->subtitle); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($banners->count() > 1): ?>
                        <div class="absolute bottom-3 left-1/2 z-10 flex -translate-x-1/2 gap-1.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button
                                    @click="current = <?php echo e($i); ?>"
                                    :class="current === <?php echo e($i); ?> ? 'w-5 bg-white' : 'w-1.5 bg-white/50'"
                                    class="h-1.5 rounded-full transition-all"
                                    aria-label="Show slide <?php echo e($i + 1); ?>"
                                ></button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <section class="mx-auto max-w-7xl px-6 py-10">
        <h2 class="font-display text-2xl font-medium text-ink">Shop by category</h2>
        <div class="mt-6 grid grid-cols-3 gap-4 sm:grid-cols-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                ['name' => 'Medicines', 'icon' => '💊'],
                ['name' => 'Mother & Baby', 'icon' => '🍼'],
                ['name' => 'Vitamins', 'icon' => '🌿'],
                ['name' => 'Skin Care', 'icon' => '🧴'],
                ['name' => 'Diabetic Care', 'icon' => '🩺'],
                ['name' => 'Ayurveda', 'icon' => '🍃'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('storefront.products.index', ['q' => $category['name']])); ?>" wire:navigate class="flex flex-col items-center gap-2 rounded-2xl border border-line bg-surface p-4 text-center hover:border-brand">
                    <span class="text-2xl"><?php echo e($category['icon']); ?></span>
                    <span class="text-xs font-medium text-ink"><?php echo e($category['name']); ?></span>
                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </section>

    
    <section class="mx-auto max-w-7xl px-6 py-10">
        <div class="flex items-baseline justify-between">
            <h2 class="font-display text-2xl font-medium text-ink">Newly added</h2>
            <a href="<?php echo e(route('storefront.products.index')); ?>" wire:navigate class="text-sm font-medium text-brand hover:underline">View all</a>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalf005a9b650879d5c895a4baa7406938a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf005a9b650879d5c895a4baa7406938a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $attributes = $__attributesOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__attributesOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $component = $__componentOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__componentOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="col-span-full text-sm text-ink-soft">No products yet.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentlyViewed->isNotEmpty()): ?>
        <section class="mx-auto max-w-7xl px-6 py-10">
            <h2 class="font-display text-2xl font-medium text-ink">Recently viewed</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentlyViewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalf005a9b650879d5c895a4baa7406938a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf005a9b650879d5c895a4baa7406938a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $attributes = $__attributesOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__attributesOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $component = $__componentOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__componentOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($similarProducts->isNotEmpty()): ?>
        <section class="mx-auto max-w-7xl px-6 py-10">
            <h2 class="font-display text-2xl font-medium text-ink">Because you looked at similar items</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $similarProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalf005a9b650879d5c895a4baa7406938a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf005a9b650879d5c895a4baa7406938a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.storefront.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('storefront.product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $attributes = $__attributesOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__attributesOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf005a9b650879d5c895a4baa7406938a)): ?>
<?php $component = $__componentOriginalf005a9b650879d5c895a4baa7406938a; ?>
<?php unset($__componentOriginalf005a9b650879d5c895a4baa7406938a); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="border-t border-line bg-mist/30">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 py-14 md:grid-cols-3">
            <div>
                <p class="font-display text-lg font-medium text-forest">100% genuine medicines</p>
                <p class="mt-2 text-sm text-ink-soft">Sourced directly from licensed manufacturers and distributors.</p>
            </div>
            <div>
                <p class="font-display text-lg font-medium text-forest">Verified by real pharmacists</p>
                <p class="mt-2 text-sm text-ink-soft">Every prescription order is checked before it ships.</p>
            </div>
            <div>
                <p class="font-display text-lg font-medium text-forest">Same-day delivery</p>
                <p class="mt-2 text-sm text-ink-soft">Order before 6pm and get it at your door the same evening.</p>
            </div>
        </div>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51ea8e071ed038a4c6542abfe5470d94)): ?>
<?php $attributes = $__attributesOriginal51ea8e071ed038a4c6542abfe5470d94; ?>
<?php unset($__attributesOriginal51ea8e071ed038a4c6542abfe5470d94); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51ea8e071ed038a4c6542abfe5470d94)): ?>
<?php $component = $__componentOriginal51ea8e071ed038a4c6542abfe5470d94; ?>
<?php unset($__componentOriginal51ea8e071ed038a4c6542abfe5470d94); ?>
<?php endif; ?>
<?php /**PATH /var/www/susthayan/susthayan/resources/views/storefront/home.blade.php ENDPATH**/ ?>
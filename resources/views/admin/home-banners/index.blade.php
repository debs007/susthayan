<x-layouts.app title="Home Banners">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Home Banners</h1>
        <p class="text-sm text-ink-muted">Two separate sets - the mobile app and the website each have their own banners, since they need very different image shapes. Lower order number shows first.</p>
    </div>

    {{-- Website banners --}}
    <section class="mb-10">
        <h2 class="mb-4 font-display font-semibold">Website</h2>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-admin.banner-list :banners="$webBanners" />
            </div>
            <div>
                <x-admin.banner-upload-form platform="web" :coupons="$coupons" />
            </div>
        </div>
    </section>

    {{-- Mobile app banners --}}
    <section>
        <h2 class="mb-4 font-display font-semibold">Mobile App</h2>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-admin.banner-list :banners="$mobileBanners" />
            </div>
            <div>
                <x-admin.banner-upload-form platform="mobile" :coupons="$coupons" />
            </div>
        </div>
    </section>
</x-layouts.app>

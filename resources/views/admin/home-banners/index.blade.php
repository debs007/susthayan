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
        <p class="text-sm text-ink-muted">The carousel shown at the top of the app's Home and Categories screens. Lower order number shows first.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            @forelse ($banners as $banner)
                <div class="flex gap-4 rounded-xl border border-border bg-canvas-raised p-4">
                    <img src="{{ $banner->image_url }}" alt="" class="h-20 w-32 flex-shrink-0 rounded-lg border border-border object-cover">
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium">{{ $banner->headline ?? '(no headline)' }}</p>
                                <p class="text-xs text-ink-muted">{{ $banner->subtitle }}</p>
                                @if ($banner->coupon)
                                    <p class="mt-1 inline-block rounded bg-primary-50 px-1.5 py-0.5 font-code text-[10px] font-medium text-primary-600">{{ $banner->coupon->code }}</p>
                                @endif
                            </div>
                            @if ($banner->is_active)
                                <span class="text-xs font-medium text-success-600">Live</span>
                            @else
                                <span class="text-xs font-medium text-ink-muted">Hidden</span>
                            @endif
                        </div>
                        <div class="mt-2 flex items-center gap-4 text-xs text-ink-muted">
                            <span>Order: {{ $banner->sort_order }}</span>
                            <form method="POST" action="{{ route('admin.home-banners.toggle-active', $banner) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="font-medium text-ink-muted hover:text-ink">
                                    {{ $banner->is_active ? 'Hide' : 'Unhide' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.home-banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-danger-600 hover:text-danger-700">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-border p-12 text-center text-ink-muted">
                    No banners yet - add the first one.
                </div>
            @endforelse
        </div>

        <div>
            <form method="POST" action="{{ route('admin.home-banners.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Add banner</h2>

                <div>
                    <label for="image" class="block text-sm font-medium text-ink">Banner image <span class="text-danger-500">*</span></label>
                    <input type="file" name="image" id="image" required accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
                </div>

                <x-form.field name="badge_text" label="Badge text" placeholder="e.g. FLAT" />
                <x-form.field name="headline" label="Headline" placeholder="e.g. 20% OFF" />
                <x-form.field name="subtitle" label="Subtitle" placeholder="e.g. On medicines + Free delivery" />
                <x-form.field name="button_text" label="Button text" placeholder="e.g. ORDER NOW" />
                <x-form.select name="coupon_id" label="Link to coupon" placeholder="No coupon - button just shows a message" :options="$coupons->pluck('code', 'id')" />
                <x-form.field name="sort_order" label="Order" type="number" placeholder="0" />

                <p class="text-xs text-ink-muted">All text fields are optional - leave blank for an image-only banner. Linking a coupon makes the button open that coupon's product list in the app; without one, the button shows a "not connected" message.</p>

                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Add banner
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

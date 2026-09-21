@props(['platform', 'coupons'])

<form method="POST" action="{{ route('admin.home-banners.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
    @csrf
    <input type="hidden" name="platform" value="{{ $platform }}">
    <h2 class="font-display font-semibold">Add {{ $platform === 'web' ? 'website' : 'app' }} banner</h2>

    <div>
        <label for="image-{{ $platform }}" class="block text-sm font-medium text-ink">Banner image <span class="text-danger-500">*</span></label>
        <input type="file" name="image" id="image-{{ $platform }}" required accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
        @if ($platform === 'web')
            <p class="mt-1 text-xs text-ink-muted">Website banners are wide (roughly 4:3) - use a landscape image at least 1000&times;750px, or it may get cropped in the site's banner carousel.</p>
        @else
            <p class="mt-1 text-xs text-ink-muted">Use the same portrait/landscape crop as the app's existing banners.</p>
        @endif
    </div>

    <x-form.field name="badge_text" label="Badge text" placeholder="e.g. FLAT" />
    <x-form.field name="headline" label="Headline" placeholder="e.g. 20% OFF" />
    <x-form.field name="subtitle" label="Subtitle" placeholder="e.g. On medicines + Free delivery" />
    <x-form.field name="button_text" label="Button text" placeholder="e.g. ORDER NOW" />
    <x-form.select name="coupon_id" label="Link to coupon" placeholder="No coupon - button just shows a message" :options="$coupons->pluck('code', 'id')" />
    <x-form.field name="sort_order" label="Order" type="number" placeholder="0" />

    <p class="text-xs text-ink-muted">All text fields are optional - leave blank for an image-only banner. Linking a coupon makes the button open that coupon's product list; without one, the button shows a "not connected" message.</p>

    <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
        Add {{ $platform === 'web' ? 'website' : 'app' }} banner
    </button>
</form>

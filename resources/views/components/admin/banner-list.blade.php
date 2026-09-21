@props(['banners'])

<div class="space-y-4">
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

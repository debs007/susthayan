<x-layouts.app title="Photo - {{ $product->name }}">
    <a href="{{ route('franchise.inventory.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to inventory
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mx-auto max-w-md rounded-xl border border-border bg-canvas-raised p-6">
        <h1 class="mb-1 font-display text-lg font-semibold">{{ $product->name }}</h1>
        <p class="mb-5 text-xs text-ink-muted">
            This photo is shared across the whole catalog - every franchise's customers will see it, not just yours.
        </p>

        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mb-4 h-48 w-full rounded-lg border border-border object-contain bg-canvas p-2">
        @else
            <div class="mb-4 flex h-48 items-center justify-center rounded-lg border border-dashed border-border text-sm text-ink-muted">
                No photo yet
            </div>
        @endif

        <form method="POST" action="{{ route('franchise.products.image.upload', $product) }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required class="block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
            <p class="text-xs text-ink-muted">JPG, PNG, or WebP - min 200x200px, up to 4MB.</p>
            <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                {{ $product->image_url ? 'Replace photo' : 'Upload photo' }}
            </button>
        </form>

        @if ($product->image_url)
            <form method="POST" action="{{ route('franchise.products.image.remove', $product) }}" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-lg border border-border px-4 py-2 text-sm font-medium text-ink-muted hover:bg-canvas" onclick="return confirm('Remove this photo?')">
                    Remove photo
                </button>
            </form>
        @endif
    </div>
</x-layouts.app>

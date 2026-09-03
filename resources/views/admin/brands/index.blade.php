<x-layouts.app title="Brands">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Brands</h1>
            <p class="text-sm text-ink-muted">Shown as "Top Brands" in the app, with their logo.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-ink-muted hover:text-ink">← Back to Catalogue</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Logo</th>
                            <th class="px-5 py-3 font-medium">Brand</th>
                            <th class="px-5 py-3 font-medium">Products</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($brands as $brand)
                            <tr>
                                <td class="px-5 py-3">
                                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg border border-border bg-canvas">
                                        @if ($brand->logo_url)
                                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="h-full w-full object-contain">
                                        @else
                                            <span class="text-[10px] text-ink-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium">{{ $brand->name }}</td>
                                <td class="px-5 py-3 font-code text-xs">{{ $brand->products_count }}</td>
                                <td class="px-5 py-3">
                                    @if ($brand->is_active)
                                        <span class="text-xs font-medium text-success-600">Active</span>
                                    @else
                                        <span class="text-xs font-medium text-ink-muted">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.brands.toggle-active', $brand) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                            {{ $brand->is_active ? 'Hide' : 'Unhide' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-ink-muted">No brands yet - add the first one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Add brand</h2>
                <x-form.field name="name" label="Name" required placeholder="e.g. Himalaya" />
                <div>
                    <label for="logo" class="block text-sm font-medium text-ink">Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/webp" class="mt-1.5 block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary-500 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-600">
                    <p class="mt-1.5 text-xs text-ink-muted">Optional - JPG, PNG, or WebP, up to 2MB.</p>
                </div>
                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Add brand
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

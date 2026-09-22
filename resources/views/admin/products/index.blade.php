<x-layouts.app title="Products & Pricing">
    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex-1 max-w-sm">
            <input
                type="text" name="q" value="{{ request('q') }}"
                placeholder="Search by name or barcode..."
                class="w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
            >
        </form>
        <div class="flex gap-3">
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-border px-4 py-2 text-sm font-medium text-ink hover:bg-canvas">
                Manage categories
            </a>
            <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                + Add product
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Schedule</th>
                    <th class="px-5 py-3 font-medium">Global price</th>
                    <th class="px-5 py-3 font-medium">Franchise overrides</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($products as $product)
                    @php
                        $globalPrice = $product->prices->firstWhere('franchise_id', null);
                        $overrideCount = $product->prices->whereNotNull('franchise_id')->count();
                    @endphp
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $product->name }}</p>
                            @if ($product->manufacturer)
                                <p class="text-xs text-ink-muted">{{ $product->manufacturer }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if ($product->drug_schedule !== 'otc')
                                <span class="rounded-full bg-honey-50 px-2 py-0.5 text-xs font-medium uppercase text-honey-600">
                                    Schedule {{ strtoupper($product->drug_schedule) }}
                                </span>
                            @else
                                <span class="text-xs text-ink-muted">OTC</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-code text-xs">
                            {{ $globalPrice ? '₹'.number_format($globalPrice->selling_price, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3 text-xs text-ink-muted">
                            {{ $overrideCount > 0 ? "{$overrideCount} franchise-specific" : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @unless ($product->is_active)
                                <span class="mr-2 rounded bg-danger-50 px-1.5 py-0.5 text-[10px] font-medium text-danger-600">Removed</span>
                            @endunless
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-sm font-medium text-primary-500 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="ml-3 inline" onsubmit="return confirm('{{ $product->is_active ? 'Remove this product from the storefront?' : 'Restore this product to the storefront?' }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium {{ $product->is_active ? 'text-danger-500' : 'text-success-600' }} hover:underline">
                                    {{ $product->is_active ? 'Delete' : 'Restore' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">
                            @if (request('q'))
                                No products match "{{ request('q') }}".
                            @else
                                No products yet. <a href="{{ route('admin.products.create') }}" class="font-medium text-primary-500 hover:underline">Add the first one</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="mt-4">{{ $products->links() }}</div>
    @endif
</x-layouts.app>

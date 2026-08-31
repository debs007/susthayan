<x-layouts.app title="Categories">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Category</th>
                            <th class="px-5 py-3 font-medium">Parent</th>
                            <th class="px-5 py-3 font-medium">Products</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $category->name }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="px-5 py-3 font-code text-xs">{{ $category->products_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-ink-muted">No categories yet - add the first one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Add category</h2>
                <x-form.field name="name" label="Name" required />
                <x-form.select
                    name="parent_id"
                    label="Parent category"
                    placeholder="None - top level"
                    :options="$categories->pluck('name', 'id')"
                />
                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Add category
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

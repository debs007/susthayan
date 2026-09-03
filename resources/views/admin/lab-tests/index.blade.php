<x-layouts.app title="Lab Tests">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Lab Tests</h1>
            <p class="text-sm text-ink-muted">{{ $tests->count() }} tests in the catalog.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.lab-test-categories.index') }}" class="text-sm font-medium text-ink-muted hover:text-ink">Manage categories</a>
            <a href="{{ route('admin.lab-tests.create') }}" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                + Add test
            </a>
        </div>
    </div>

    <form method="GET" class="mb-4 flex items-center gap-2">
        <select name="category" onchange="this.form.submit()" class="rounded-lg border border-border bg-canvas-raised px-3 py-2 text-sm">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="rounded-xl border border-border bg-canvas-raised">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Test</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Visit type</th>
                    <th class="px-5 py-3 text-right font-medium">Price</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($tests as $test)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $test->name }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $test->category->name }}</td>
                        <td class="px-5 py-3">
                            @if ($test->requires_center_visit)
                                <span class="rounded-full bg-orange-50 px-2 py-0.5 text-xs font-medium text-orange-700">Center visit</span>
                            @else
                                <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600">Home visit</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($test->price, 2) }}</td>
                        <td class="px-5 py-3">
                            @if ($test->is_active)
                                <span class="text-xs font-medium text-success-600">Active</span>
                            @else
                                <span class="text-xs font-medium text-ink-muted">Hidden</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.lab-tests.edit', $test) }}" class="text-xs font-medium text-primary-500 hover:text-primary-600">Edit</a>
                                <form method="POST" action="{{ route('admin.lab-tests.toggle-active', $test) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink">
                                        {{ $test->is_active ? 'Hide' : 'Unhide' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-ink-muted">No tests yet - add the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>

<x-layouts.app title="Lab Test Categories">
    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-lg font-semibold">Lab Test Categories</h1>
            <p class="text-sm text-ink-muted">Blood, Urine, Cardiac, and so on - group tests under these before adding them.</p>
        </div>
        <a href="{{ route('admin.lab-tests.index') }}" class="text-sm font-medium text-primary-500 hover:text-primary-600">View all tests →</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border bg-canvas-raised">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                            <th class="px-5 py-3 font-medium">Category</th>
                            <th class="px-5 py-3 font-medium">Tests</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $category->name }}</td>
                                <td class="px-5 py-3 font-code text-xs">{{ $category->lab_tests_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-12 text-center text-ink-muted">No categories yet - add the first one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <form method="POST" action="{{ route('admin.lab-test-categories.store') }}" class="space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
                @csrf
                <h2 class="font-display font-semibold">Add category</h2>
                <x-form.field name="name" label="Name" required placeholder="e.g. Blood Tests" />
                <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                    Add category
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>

@if ($errors->any())
    <div class="mb-8 rounded-lg border border-danger-500/30 bg-danger-50 px-5 py-4 text-sm text-danger-600">
        <p class="mb-1.5 font-medium">
            {{ $errors->count() === 1 ? 'Please fix the following:' : "Please fix the following {$errors->count()} things:" }}
        </p>
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

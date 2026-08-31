@props(['name', 'label', 'checked' => false, 'hint' => null])

<div>
    <label class="flex items-start gap-2.5">
        {{-- Unchecked checkboxes submit nothing at all - this hidden field
             guarantees the name is always present in the request, so a
             'required' boolean rule sees "0" rather than a missing key. --}}
        <input type="hidden" name="{{ $name }}" value="0">
        <input
            type="checkbox"
            name="{{ $name }}"
            value="1"
            @checked(old($name, $checked))
            class="mt-0.5 h-4 w-4 rounded border-border text-primary-500 focus:ring-primary-500"
        >
        <span class="text-sm">
            <span class="font-medium">{{ $label }}</span>
            @if ($hint)
                <span class="block text-xs text-ink-muted">{{ $hint }}</span>
            @endif
        </span>
    </label>
    @error($name)
        <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
    @enderror
</div>

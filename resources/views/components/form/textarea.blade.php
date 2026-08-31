@props(['name', 'label', 'required' => false, 'value' => null, 'rows' => 3])

@php
    $dotName = str_replace([']', '['], ['', '.'], $name);
@endphp

<div>
    <label for="{{ $name }}" class="mb-2 block text-sm font-medium">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary-500 '
                . ($errors->has($dotName) ? 'border-danger-500' : 'border-border'),
        ]) }}
    >{{ old($dotName, $value) }}</textarea>
    @error($dotName)
        <p class="mt-1.5 text-xs text-danger-500">{{ $message }}</p>
    @enderror
</div>

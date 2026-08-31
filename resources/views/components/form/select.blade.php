@props(['name', 'label', 'options', 'required' => false, 'value' => null, 'placeholder' => null])

@php
    $dotName = str_replace([']', '['], ['', '.'], $name);
@endphp

<div>
    <label for="{{ $name }}" class="mb-2 block text-sm font-medium">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border bg-canvas-raised px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary-500 '
                . ($errors->has($dotName) ? 'border-danger-500' : 'border-border'),
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected(old($dotName, $value) == $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($dotName)
        <p class="mt-1.5 text-xs text-danger-500">{{ $message }}</p>
    @enderror
</div>

@props(['name', 'label', 'type' => 'text', 'required' => false, 'value' => null, 'step' => null, 'hint' => null])

@php
    // old()/$errors need dot notation for nested arrays ('items.0.batch_no'),
    // but the name= attribute needs bracket notation ('items[0][batch_no]')
    // to actually submit as a nested array. Converting once here means
    // every caller can just pass whichever notation HTML needs and not
    // have to know about this mismatch.
    $dotName = str_replace([']', '['], ['', '.'], $name);
@endphp

<div>
    <label for="{{ $name }}" class="mb-2 block text-sm font-medium">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($dotName, $value) }}"
        @if ($step) step="{{ $step }}" @endif
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary-500 '
                . ($errors->has($dotName) ? 'border-danger-500' : 'border-border'),
        ]) }}
    >
    @if ($hint && ! $errors->has($dotName))
        <p class="mt-1.5 text-xs text-ink-muted">{{ $hint }}</p>
    @endif
    @error($dotName)
        <p class="mt-1.5 text-xs text-danger-500">{{ $message }}</p>
    @enderror
</div>

@props([
    'name' => 'search',
    'label' => null,
    'value' => null,
    'placeholder' => 'Cari data',
    'helper' => null,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'id' => null,
    'size' => 'md',
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value ?? request($name));
@endphp

<label for="{{ $id }}" class="block space-y-2">
    @if ($label)
        <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }}</span>
    @endif
    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled" class="etc-field-size-{{ $size }}">
        <x-filament::input :id="$id" :name="$name" type="search" :value="$fieldValue" :placeholder="$placeholder" :disabled="$disabled" :readonly="$readonly" {{ $attributes }} />
    </x-filament::input.wrapper>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

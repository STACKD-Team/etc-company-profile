@props([
    'name',
    'label' => null,
    'value' => '1',
    'checked' => false,
    'helper' => null,
    'disabled' => false,
    'error' => null,
    'id' => null,
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $error ??= $errors->first($name);
    $isChecked = (bool) old($name, $checked);
@endphp

<div class="space-y-2">
    <label for="{{ $id }}" class="flex items-start gap-3">
        <x-filament::input.checkbox :id="$id" :name="$name" :value="$value" :checked="$isChecked" :disabled="$disabled" :valid="blank($error)" {{ $attributes->class('mt-1') }} />
        <span class="min-w-0 text-sm text-etc-on-surface">
            @if ($label)<span class="font-heading font-bold">{{ $label }}</span>@endif
            @if ($helper)<span class="mt-1 block text-xs text-etc-on-muted">{{ $helper }}</span>@endif
        </span>
    </label>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@endif
</div>

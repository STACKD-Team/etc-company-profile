@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'id' => null,
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value);
@endphp

<label for="{{ $id }}" class="block space-y-2">
    @if ($label)
        <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</span>
    @endif
    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled" prefix="Rp">
        <x-filament::input :id="$id" :name="$name" type="number" :value="$fieldValue" :placeholder="$placeholder" :required="$required" :disabled="$disabled" :readonly="$readonly" min="0" step="1000" {{ $attributes }} />
    </x-filament::input.wrapper>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

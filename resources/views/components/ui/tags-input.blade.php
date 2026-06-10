@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => 'Pisahkan dengan koma',
    'helper' => 'Masukkan tag dipisahkan koma. Gunakan Filament TagsInput pada Filament Resource/Livewire form untuk pengalaman penuh.',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'id' => null,
    'size' => 'md',
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $error ??= $errors->first($name);
    $displayValue = is_array($value) ? implode(', ', $value) : $value;
    $fieldValue = old($name, $displayValue);
@endphp

<label for="{{ $id }}" class="block space-y-2">
    @if ($label)
        <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</span>
    @endif
    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled" class="etc-field-size-{{ $size }}">
        <x-filament::input :id="$id" :name="$name" type="text" :value="$fieldValue" :placeholder="$placeholder" :required="$required" :disabled="$disabled" :readonly="$readonly" {{ $attributes }} />
    </x-filament::input.wrapper>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

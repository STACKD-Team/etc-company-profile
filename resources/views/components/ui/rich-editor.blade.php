@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'helper' => 'Gunakan Filament RichEditor pada Filament Resource/Livewire form untuk toolbar penuh.',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'id' => null,
    'rows' => 8,
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
    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled">
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}" @required($required) @disabled($disabled) @readonly($readonly) {{ $attributes->class('fi-input block w-full resize-y border-none bg-transparent py-2 text-base text-etc-on-surface outline-none placeholder:text-etc-on-muted focus:ring-0 sm:text-sm') }}>{{ $fieldValue }}</textarea>
    </x-filament::input.wrapper>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

@props([
    'name',
    'label' => null,
    'value' => null,
    'options' => [],
    'placeholder' => null,
    'helper' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'id' => null,
    'list' => null,
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $list ??= $id.'_options';
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value);
@endphp

<label for="{{ $id }}" class="block space-y-2">
    @if ($label)
        <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</span>
    @endif

    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled">
        <x-filament::input :id="$id" :name="$name" type="text" :value="$fieldValue" :placeholder="$placeholder" :required="$required" :disabled="$disabled" :readonly="$readonly" :list="$list" {{ $attributes }} />
    </x-filament::input.wrapper>

    <datalist id="{{ $list }}">
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ is_string($optionValue) ? $optionValue : $optionLabel }}">{{ $optionLabel }}</option>
        @endforeach
    </datalist>

    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

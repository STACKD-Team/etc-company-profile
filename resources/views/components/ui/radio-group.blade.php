@props([
    'name',
    'label' => null,
    'value' => null,
    'options' => [],
    'helper' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'size' => 'md',
])

@php
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value);
@endphp

<fieldset class="etc-selector-size-{{ $size }} space-y-3">
    @if ($label)
        <legend class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</legend>
    @endif

    <div class="grid gap-3">
        @foreach ($options as $optionValue => $optionLabel)
            @php $id = str($name.'_'.$optionValue)->replace(['[', ']'], ['_', ''])->toString(); @endphp
            <label for="{{ $id }}" class="flex items-center gap-3 rounded-selector border-2 border-etc-outline-variant bg-etc-surface px-4 py-3">
                <x-filament::input.radio :id="$id" :name="$name" :value="$optionValue" :checked="(string) $fieldValue === (string) $optionValue" :disabled="$disabled" :valid="blank($error)" />
                <span class="text-sm font-medium text-etc-on-surface">{{ $optionLabel }}</span>
            </label>
        @endforeach
    </div>

    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</fieldset>

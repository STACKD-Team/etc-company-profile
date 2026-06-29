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
    'size' => 'md',
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $list ??= $id.'_options';
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value);
    $normalizedOptions = collect($options)
        ->mapWithKeys(fn ($optionLabel, $optionValue) => [(string) $optionValue => (string) $optionLabel])
        ->all();
    $displayValue = $normalizedOptions[(string) $fieldValue] ?? '';
@endphp

<label
    for="{{ $id }}"
    class="block space-y-2"
    x-data="{
        options: @js($normalizedOptions),
        selected: @js((string) $fieldValue),
        display: @js($displayValue),
        syncValue() {
            const needle = this.display.trim().toLocaleLowerCase()
            const match = Object.entries(this.options).find(([, label]) => label.toLocaleLowerCase() === needle)
            const nextValue = match ? match[0] : ''

            if (this.selected === nextValue) {
                return
            }

            this.selected = nextValue
            this.$nextTick(() => this.$refs.value.dispatchEvent(new Event('change', { bubbles: true })))
        },
    }"
>
    @if ($label)
        <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</span>
    @endif

    <input
        x-ref="value"
        type="hidden"
        name="{{ $name }}"
        x-bind:value="selected"
        data-table-filter-immediate
    >

    <x-filament::input.wrapper :valid="blank($error)" :disabled="$disabled" class="etc-field-size-{{ $size }}">
        <x-filament::input
            :id="$id"
            type="text"
            x-model="display"
            x-on:input.debounce.400ms="syncValue()"
            x-on:change="syncValue()"
            :placeholder="$placeholder"
            :required="$required"
            :disabled="$disabled"
            :readonly="$readonly"
            :list="$list"
            autocomplete="off"
            {{ $attributes }}
        />
    </x-filament::input.wrapper>

    <datalist id="{{ $list }}">
        @foreach ($normalizedOptions as $optionValue => $optionLabel)
            <option value="{{ $optionLabel }}">{{ $optionLabel }}</option>
        @endforeach
    </datalist>

    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</label>

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
    'columns' => null,
    'optionHelpers' => [],
    'optionIcons' => [],
    'optionAttributes' => [],
    'optionWrapperAttributes' => [],
    'optionIconAttributes' => [],
])

@php
    $error ??= $errors->first($name);
    $fieldValue = old($name, $value);
@endphp

<fieldset class="etc-selector-size-{{ $size }} space-y-3">
    @if ($label)
        <legend class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }} @if ($required)<span class="text-etc-magenta">*</span>@endif</legend>
    @endif

    <div @class(['grid gap-3', $columns])>
        @foreach ($options as $optionValue => $optionLabel)
            @php
                $id = str($name.'_'.$optionValue)->replace(['[', ']'], ['_', ''])->toString();
                $wrapperAttributes = new \Illuminate\View\ComponentAttributeBag(array_merge(
                    $optionAttributes[$optionValue] ?? [],
                    $optionWrapperAttributes[$optionValue] ?? [],
                ));
                $iconAttributes = new \Illuminate\View\ComponentAttributeBag($optionIconAttributes[$optionValue] ?? []);
                $optionHelper = $optionHelpers[$optionValue] ?? null;
                $optionIcon = $optionIcons[$optionValue] ?? null;
            @endphp
            <label
                for="{{ $id }}"
                {{ $wrapperAttributes->class('flex items-start gap-3 rounded-selector border-2 border-etc-outline-variant bg-etc-surface px-4 py-3') }}
            >
                <x-filament::input.radio
                    :id="$id"
                    :name="$name"
                    :value="$optionValue"
                    :checked="(string) $fieldValue === (string) $optionValue"
                    :disabled="$disabled"
                    :valid="blank($error)"
                />
                @if ($optionIcon)
                    <span {{ $iconAttributes->class('flex h-10 w-10 shrink-0 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta') }}>
                        <x-ui.icon :name="$optionIcon" class="h-5 w-5" />
                    </span>
                @endif
                <span class="min-w-0">
                    <span class="block text-sm font-medium text-etc-on-surface">{{ $optionLabel }}</span>
                    @if ($optionHelper)
                        <span class="mt-1 block text-xs leading-[var(--etc-leading-normal)] text-etc-on-muted">{{ $optionHelper }}</span>
                    @endif
                </span>
            </label>
        @endforeach
    </div>

    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@elseif ($helper)<p class="text-xs text-etc-on-muted">{{ $helper }}</p>@endif
</fieldset>

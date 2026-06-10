@props([
    'href' => null,
    'color' => 'primary',
    'outlined' => false,
    'icon' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
    'size' => 'md',
    'type' => 'button',
    'tag' => null,
    'target' => null,
])

@php
    $tag ??= $href ? 'a' : 'button';
@endphp

<x-filament::button
    :tag="$tag"
    :href="$href"
    :color="$color"
    :outlined="$outlined"
    :icon="$icon"
    :icon-position="$iconPosition"
    :icon-size="$iconSize"
    :size="$size"
    :type="$type"
    :target="$target"
    {{ $attributes }}
>
    {{ $slot }}
</x-filament::button>

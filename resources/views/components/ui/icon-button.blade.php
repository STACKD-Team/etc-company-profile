@props([
    'href' => null,
    'icon',
    'label' => null,
    'color' => 'gray',
    'outlined' => false,
    'iconSize' => null,
    'size' => 'md',
    'type' => 'button',
    'tag' => null,
    'target' => null,
])

@php
    $tag ??= $href ? 'a' : 'button';
@endphp

<x-filament::icon-button
    :tag="$tag"
    :href="$href"
    :icon="$icon"
    :label="$label"
    :color="$color"
    :icon-size="$iconSize"
    :size="$size"
    :type="$type"
    :target="$target"
    {{ $attributes->class(['etc-icon-button-outlined' => $outlined]) }}
/>

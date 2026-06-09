@props([
    'href' => null,
    'icon',
    'label' => null,
    'color' => 'gray',
    'outlined' => false,
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
    :outlined="$outlined"
    :type="$type"
    :target="$target"
    {{ $attributes }}
/>

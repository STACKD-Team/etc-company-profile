@props([
    'href' => null,
    'color' => 'primary',
    'outlined' => false,
    'icon' => null,
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
    :type="$type"
    :target="$target"
    {{ $attributes }}
>
    {{ $slot }}
</x-filament::button>

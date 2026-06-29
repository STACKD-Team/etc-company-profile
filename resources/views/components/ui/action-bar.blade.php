@props([
    'align' => 'end',
    'wrap' => true,
])

@php
    $alignment = match ($align) {
        'start' => 'justify-start',
        'center' => 'justify-center',
        'between' => 'justify-between',
        default => 'justify-end',
    };

    $wrapClass = $wrap ? 'flex-wrap' : 'flex-nowrap';
@endphp

<div {{ $attributes->class("flex {$wrapClass} items-center gap-2 {$alignment}") }}>
    {{ $slot }}
</div>

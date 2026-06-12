@props([
    'columns' => 2,
])

@php
    $gridClass = match ((int) $columns) {
        1 => 'grid-cols-1',
        3 => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2',
    };
@endphp

<dl {{ $attributes->class("grid {$gridClass} gap-x-6 gap-y-5") }}>
    {{ $slot }}
</dl>

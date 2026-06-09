@props([
    'paginator',
])

@php
    $isLivewireContext = isset($this) && method_exists($this, 'getId');
@endphp

<div {{ $attributes->class('mt-5') }}>
    @if ($isLivewireContext)
        <x-filament::pagination :paginator="$paginator" />
    @else
        <x-filament::section compact>
            {{ $paginator->withQueryString()->links() }}
        </x-filament::section>
    @endif
</div>

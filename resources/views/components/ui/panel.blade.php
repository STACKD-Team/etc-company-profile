@props([
    'heading' => null,
    'description' => null,
    'icon' => null,
    'iconColor' => 'primary',
    'iconSize' => 'lg',
    'compact' => false,
    'secondary' => false,
])

<x-filament::section
    :heading="$heading"
    :description="$description"
    :icon="$icon"
    :icon-color="$iconColor"
    :icon-size="$iconSize"
    :compact="$compact"
    :secondary="$secondary"
    {{ $attributes }}
>
    @isset($actions)
        <x-slot name="afterHeader">{{ $actions }}</x-slot>
    @endisset

    {{ $slot }}

    @isset($footer)
        <x-slot name="footer">{{ $footer }}</x-slot>
    @endisset
</x-filament::section>

@props([
    'id' => null,
    'heading' => null,
    'description' => null,
    'icon' => null,
    'iconColor' => 'primary',
    'width' => 'md',
    'slideOver' => false,
])

<x-filament::modal
    :id="$id"
    :heading="$heading"
    :description="$description"
    :icon="$icon"
    :icon-color="$iconColor"
    :width="$width"
    :slide-over="$slideOver"
    {{ $attributes }}
>
    @isset($trigger)
        <x-slot name="trigger">{{ $trigger }}</x-slot>
    @endisset

    {{ $slot }}

    @isset($footer)
        <x-slot name="footer">{{ $footer }}</x-slot>
    @endisset
</x-filament::modal>

@props([
    'heading',
    'description' => null,
    'icon' => null,
    'iconColor' => 'primary',
])

<x-filament::empty-state
    :heading="$heading"
    :description="$description"
    :icon="$icon"
    :icon-color="$iconColor"
    {{ $attributes }}
>
    @if (! $slot->isEmpty())
        <x-slot name="footer">{{ $slot }}</x-slot>
    @endif
</x-filament::empty-state>

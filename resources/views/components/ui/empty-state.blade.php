@props([
    'heading',
    'description' => null,
    'icon' => null,
    'iconColor' => 'primary',
    'iconSize' => 'lg',
    'compact' => false,
    'contained' => false,
])

<x-filament::empty-state
    :heading="$heading"
    :description="$description"
    :icon="$icon"
    :icon-color="$iconColor"
    :icon-size="$iconSize"
    :compact="$compact"
    :contained="$contained"
    {{ $attributes }}
>
    @if (! $slot->isEmpty())
        <x-slot name="footer">{{ $slot }}</x-slot>
    @endif
</x-filament::empty-state>

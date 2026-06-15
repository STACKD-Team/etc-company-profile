@props([
    'id' => null,
    'heading' => null,
    'description' => null,
    'icon' => null,
    'iconColor' => 'primary',
    'width' => 'md',
    'slideOver' => false,
])

@isset($trigger)
    <div
        {{ $trigger->attributes->class('fi-modal-trigger') }}
        @if ($id && ! $trigger->attributes->get('disabled'))
            data-open-modal="{{ $id }}"
        @endif
    >
        {{ $trigger }}
    </div>
@endisset

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
    {{ $slot }}

    @isset($footer)
        <x-slot name="footer">{{ $footer }}</x-slot>
    @endisset
</x-filament::modal>

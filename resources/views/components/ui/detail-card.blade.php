@props([
    'heading' => null,
    'description' => null,
    'icon' => null,
    'iconColor' => 'primary',
    'compact' => false,
])

<x-ui.panel
    :heading="$heading"
    :description="$description"
    :icon="$icon"
    :icon-color="$iconColor"
    :compact="$compact"
    {{ $attributes }}
>
    @if (isset($status) || isset($actions))
        <x-slot name="actions">
            <div class="flex flex-wrap items-center justify-end gap-2">
                @isset($status)
                    {{ $status }}
                @endisset

                @isset($actions)
                    {{ $actions }}
                @endisset
            </div>
        </x-slot>
    @endif

    <div class="space-y-6">
        {{ $slot }}
    </div>

    @isset($footer)
        <x-slot name="footer">{{ $footer }}</x-slot>
    @endisset
</x-ui.panel>

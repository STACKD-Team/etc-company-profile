@props([
    'label',
    'value',
    'description' => null,
    'icon' => null,
    'status' => null,
    'valueAttributes' => [],
])

@php($valueAttributeBag = new \Illuminate\View\ComponentAttributeBag($valueAttributes))

<x-ui.panel compact {{ $attributes }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 space-y-2">
            <p class="font-heading text-xs font-bold uppercase tracking-normal text-etc-on-muted">
                {{ $label }}
            </p>
            <p {{ $valueAttributeBag->class('break-words font-heading text-2xl font-bold tracking-normal text-etc-charcoal') }}>
                {{ $value }}
            </p>

            @if ($description)
                <p class="text-sm text-etc-on-muted">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($icon || $status)
            <div class="flex shrink-0 items-center gap-2">
                @if ($status)
                    <x-ui.badge :status="$status" />
                @endif

                @if ($icon)
                    <span class="grid h-10 w-10 place-items-center rounded-selector bg-etc-surface text-etc-magenta ring-1 ring-etc-outline-variant/70">
                        {{
                            \Filament\Support\generate_icon_html(
                                $icon,
                                attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'h-5 w-5']),
                                size: \Filament\Support\Enums\IconSize::Large,
                            )
                        }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</x-ui.panel>

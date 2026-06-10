@props([
    'status' => 'info',
    'title' => null,
    'icon' => null,
])

@php
    $tone = match ($status) {
        'success' => 'border-emerald-600/35 bg-emerald-50 text-emerald-900',
        'warning' => 'border-amber-600/35 bg-amber-50 text-amber-900',
        'danger', 'error' => 'border-red-600/35 bg-red-50 text-red-900',
        default => 'border-etc-magenta/35 bg-etc-surface text-etc-on-surface',
    };
    $icon ??= match ($status) {
        'success' => 'heroicon-o-check-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        'danger', 'error' => 'heroicon-o-x-circle',
        default => 'heroicon-o-information-circle',
    };
@endphp

<div
    role="{{ in_array($status, ['danger', 'error'], true) ? 'alert' : 'status' }}"
    {{ $attributes->class(['etc-alert flex items-start gap-3 p-4 text-sm', $tone]) }}
>
    {{
        \Filament\Support\generate_icon_html(
            $icon,
            attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'mt-0.5 shrink-0']),
            size: \Filament\Support\Enums\IconSize::Medium,
        )
    }}
    <div class="min-w-0">
        @if ($title)
            <p class="font-heading font-bold">{{ $title }}</p>
        @endif
        <div @class(['leading-6', 'mt-1' => $title])>{{ $slot }}</div>
    </div>
</div>

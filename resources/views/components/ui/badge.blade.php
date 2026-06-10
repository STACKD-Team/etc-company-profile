@props([
    'status' => null,
    'color' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'size' => 'md',
])

@php
    $normalized = $status ? str($status)->lower()->replace(' ', '_')->toString() : null;
    $color ??= match ($normalized) {
        'paid', 'active', 'ready', 'published', 'processed', 'enrolled', 'completed', 'success' => 'success',
        'pending', 'pending_payment', 'waiting_payment', 'placement_test', 'processing', 'draft', 'received' => 'warning',
        'failed', 'failure', 'rejected', 'cancelled', 'expired', 'archived', 'error' => 'danger',
        'inactive', 'unpublished', 'ignored', 'dropped' => 'gray',
        default => 'primary',
    };
@endphp

<x-filament::badge
    :color="$color"
    :icon="$icon"
    :icon-position="$iconPosition"
    :size="$size"
    {{ $attributes }}
>
    {{ $slot->isEmpty() ? str($status ?? 'status')->replace('_', ' ')->headline() : $slot }}
</x-filament::badge>

@props([
    'label',
    'value' => null,
    'empty' => '-',
])

@php
$hasSlot = ! $slot->isEmpty();
$displayValue = $hasSlot ? null : (filled($value) ? $value : $empty);
@endphp

<div {{ $attributes->class('min-w-0 space-y-1') }}>
    <dt class="font-heading text-xs font-bold uppercase tracking-normal text-etc-on-muted">
        {{ $label }}
    </dt>
    <dd class="min-w-0 break-words text-sm font-medium text-etc-charcoal">
        @if ($hasSlot)
            {{ $slot }}
        @else
            {{ $displayValue }}
        @endif
    </dd>
</div>

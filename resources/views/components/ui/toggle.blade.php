@props([
    'name',
    'label' => null,
    'checked' => false,
    'helper' => null,
    'disabled' => false,
    'error' => null,
    'id' => null,
    'size' => 'md',
])

@php
    $id ??= str($name)->replace(['[', ']'], ['_', ''])->toString();
    $error ??= $errors->first($name);
    $state = old($name, $checked) ? 'true' : 'false';
@endphp

<div class="etc-selector-size-{{ $size }} space-y-2" x-data="{ enabled: {{ $state }} }">
    <div class="flex items-start justify-between gap-4">
        <label for="{{ $id }}" class="min-w-0">
            @if ($label)<span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }}</span>@endif
            @if ($helper)<span class="mt-1 block text-xs text-etc-on-muted">{{ $helper }}</span>@endif
        </label>
        <input x-ref="input" type="hidden" name="{{ $name }}" x-bind:value="enabled ? '1' : '0'">
        <x-filament::toggle id="{{ $id }}" x-on:click="enabled = ! enabled" x-bind:aria-checked="enabled.toString()" x-bind:class="enabled ? 'fi-toggle-on' : 'fi-toggle-off'" :state="$state" :disabled="$disabled" {{ $attributes->class('etc-selector-size-'.$size) }} />
    </div>
    @if ($error)<p class="text-xs font-medium text-red-600">{{ $error }}</p>@endif
</div>

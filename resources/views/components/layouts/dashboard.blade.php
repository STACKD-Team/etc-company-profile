@props([
    'title' => null,
    'area' => 'student',
    'sidebarItems' => null,
    'active' => null,
    'user' => null,
])

@php
    $user ??= auth()->user();
    $displayName = $user?->full_name ?? $user?->name ?? str($area)->headline()->toString();
    $avatar = $user?->avatar ?? null;
    $initial = str($displayName)->trim()->substr(0, 1)->upper()->toString();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'ETC Planet') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800;900&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        @filamentStyles
        {{ \Filament\Support\Facades\FilamentAsset::getTheme('app')?->getHtml() }}
        @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('css/vite-fallback.css') }}">
        @endif
        @stack('styles')
    </head>
    <body
        class="etc-filament-ui h-screen overflow-hidden bg-etc-surface font-body text-etc-on-surface antialiased"
        x-data="{
            sidebarCollapsed: false,
            sidebarMobileOpen: false,
            init() {
                this.sidebarCollapsed = window.localStorage?.getItem('etc-dashboard-sidebar-collapsed') === '1'
            },
            toggleSidebar() {
                if (window.matchMedia('(min-width: 768px)').matches) {
                    this.sidebarCollapsed = ! this.sidebarCollapsed
                    window.localStorage?.setItem('etc-dashboard-sidebar-collapsed', this.sidebarCollapsed ? '1' : '0')
                    return
                }

                this.sidebarMobileOpen = ! this.sidebarMobileOpen
            },
            closeMobileSidebar() {
                this.sidebarMobileOpen = false
            },
        }"
        x-on:keydown.escape.window="closeMobileSidebar()"
        x-on:resize.window="if (window.innerWidth >= 768) closeMobileSidebar()"
    >
        <div class="flex h-screen">
            <div
                x-cloak
                x-show="sidebarMobileOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-etc-charcoal/35 backdrop-blur-sm md:hidden"
                x-on:click="closeMobileSidebar()"
                aria-hidden="true"
            ></div>

            <x-dashboard.sidebar :area="$area" :items="$sidebarItems" :active="$active" />

            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                <header class="border-b-2 border-etc-outline-variant bg-etc-surface/90 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
                    <div class="flex min-h-12 items-center justify-between gap-4">
                        <x-ui.icon-button
                            icon="heroicon-m-bars-3"
                            label="Buka atau ringkas sidebar"
                            outlined
                            x-on:click="toggleSidebar()"
                            aria-controls="dashboard-sidebar"
                            x-bind:aria-expanded="sidebarMobileOpen || ! sidebarCollapsed"
                            data-sidebar-toggle
                        />

                        <x-filament::dropdown placement="bottom-end" teleport width="xs">
                            <x-slot:trigger>
                                <button
                                    type="button"
                                    class="flex min-h-12 max-w-64 items-center gap-3 rounded-field px-2 py-1.5 text-left transition hover:bg-etc-surface-container"
                                    aria-label="Buka menu profil"
                                    data-dashboard-profile-trigger
                                >
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-selector bg-etc-magenta font-heading text-sm font-bold text-etc-surface">
                                        @if ($avatar)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($avatar) }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-heading text-sm font-bold text-etc-on-surface">{{ $displayName }}</span>
                                        <span class="block text-xs text-etc-on-muted">{{ str($area)->headline() }}</span>
                                    </span>
                                    {{ \Filament\Support\generate_icon_html('heroicon-m-chevron-down', attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'shrink-0 text-etc-on-muted'])) }}
                                </button>
                            </x-slot:trigger>

                            <x-filament::dropdown.list>
                                @if (\Illuminate\Support\Facades\Route::has('auth.logout'))
                                    <x-filament::dropdown.list.item
                                        tag="form"
                                        method="POST"
                                        :action="route('auth.logout')"
                                        icon="heroicon-m-arrow-right-start-on-rectangle"
                                        color="danger"
                                    >
                                        Logout
                                    </x-filament::dropdown.list.item>
                                @endif
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto px-6 py-6 lg:px-8 lg:py-8">
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            @isset($eyebrow)
                                <p class="mb-1 font-heading text-xs font-bold uppercase text-etc-magenta">{{ $eyebrow }}</p>
                            @endisset
                            <h1 class="font-heading text-2xl font-bold text-etc-on-surface md:text-3xl">{{ $title ?? str($area)->headline()->toString() }}</h1>
                        </div>

                        @isset($headerActions)
                            <div class="flex flex-wrap items-center gap-3">
                                {{ $headerActions }}
                            </div>
                        @endisset
                    </div>

                    {{ $slot }}
                </main>
            </div>
        </div>
        @filamentScripts(withCore: true)
        @stack('scripts')
    </body>
</html>

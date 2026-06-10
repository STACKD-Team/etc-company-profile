@props([
    'title' => null,
    'brand' => 'ETC Planet',
    'navbarItems' => null,
    'navbarActive' => null,
    'footerLinkGroups' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', $brand) }}</title>

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
    <body class="etc-filament-ui min-h-screen bg-etc-surface font-body text-etc-on-surface antialiased selection:bg-etc-magenta selection:text-white">
        <x-site.navbar :title="$brand" :items="$navbarItems" :active="$navbarActive" />

        <main>
            {{ $slot }}
        </main>

        <x-site.footer :brand="$brand" :link-groups="$footerLinkGroups" />
        <x-site.chatbot />
        @filamentScripts(withCore: true)
        @stack('scripts')
    </body>
</html>

@props([
    'title' => null,
    'brand' => 'ETC Planet',
    'navbarItems' => null,
    'navbarActive' => null,
    'footerLinkGroups' => null,
    'showNavbar' => true,
    'showFooter' => true,
    'showChatbot' => true,
    'bodyClass' => '',
    'mainClass' => '',
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
    <body @class(['etc-filament-ui min-h-screen bg-etc-surface font-body text-etc-on-surface antialiased selection:bg-etc-magenta selection:text-white', $bodyClass])>
        @if ($showNavbar)
            <x-site.navbar :title="$brand" :items="$navbarItems" :active="$navbarActive" />
        @endif

        <main @class([$mainClass])>
            {{ $slot }}
        </main>

        @if ($showFooter)
            <x-site.footer :brand="$brand" :link-groups="$footerLinkGroups" />
        @endif

        @if ($showChatbot)
            <x-site.chatbot />
        @endif
        @filamentScripts(withCore: true)
        @stack('scripts')
    </body>
</html>

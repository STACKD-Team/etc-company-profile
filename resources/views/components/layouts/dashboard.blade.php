@props([
    'title' => null,
    'area' => 'student',
    'sidebarItems' => null,
    'active' => null,
    'user' => null,
])

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

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="h-screen overflow-hidden bg-etc-surface font-body text-etc-on-surface antialiased">
        <div class="flex h-screen">
            <x-dashboard.sidebar :area="$area" :items="$sidebarItems" :active="$active" :user="$user">
                @isset($sidebarActions)
                    <x-slot:actions>
                        {{ $sidebarActions }}
                    </x-slot:actions>
                @endisset
            </x-dashboard.sidebar>

            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                <header class="border-b border-etc-outline-variant/60 bg-white/75 px-6 py-5 backdrop-blur lg:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            @isset($eyebrow)
                                <p class="mb-1 font-heading text-xs font-bold uppercase text-etc-magenta">{{ $eyebrow }}</p>
                            @endisset
                            <h1 class="font-heading text-2xl font-bold text-etc-on-surface md:text-3xl">{{ $title ?? str($area)->headline()->toString() }}</h1>
                        </div>

                        @isset($headerActions)
                            <div class="flex items-center gap-3">
                                {{ $headerActions }}
                            </div>
                        @endisset
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto px-6 py-6 pb-28 lg:px-8 md:pb-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

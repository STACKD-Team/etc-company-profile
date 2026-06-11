@props([
    'title' => 'ETC Planet',
    'items' => null,
    'active' => null,
    'loginUrl' => null,
    'registerUrl' => null,
])

@php
    $routeUrl = static function (?string $routeName, string $fallback = '#'): string {
        if (! $routeName || ! \Illuminate\Support\Facades\Route::has($routeName)) {
            return $fallback;
        }

        return route($routeName);
    };

    $items = collect($items ?? [
        ['label' => 'Beranda', 'route' => 'public.home', 'url' => '/', 'key' => 'home', 'icon' => 'home'],
        ['label' => 'Program', 'route' => 'public.programs.index', 'url' => '/programs', 'key' => 'program', 'icon' => 'school'],
        ['label' => 'Reels', 'route' => 'public.reels.index', 'url' => '#reels', 'key' => 'reels', 'icon' => 'smart_display'],
        ['label' => 'Tentang Kami', 'route' => 'public.about', 'url' => '#tentang', 'key' => 'about', 'icon' => 'groups'],
        ['label' => 'Kontak', 'route' => 'public.contact.index', 'url' => '#kontak', 'key' => 'contact', 'icon' => 'call'],
    ])->map(function (array $item) use ($routeUrl) {
        $item['key'] ??= str($item['label'] ?? 'item')->slug()->toString();
        $item['url'] = $routeUrl($item['route'] ?? null, $item['url'] ?? '#');
        $item['icon'] ??= 'circle';

        return $item;
    });

    $currentActive = $active ?: $items->first(function (array $item) {
        $routeName = $item['route'] ?? null;

        if ($routeName && \Illuminate\Support\Facades\Route::has($routeName) && request()->routeIs($routeName)) {
            return true;
        }

        $path = trim(parse_url($item['url'] ?? '#', PHP_URL_PATH) ?: '', '/');

        return $path !== '' && request()->is($path . '*');
    })['key'] ?? 'home';

    $loginUrl ??= $routeUrl('auth.login', '/login');
    $registerUrl ??= $routeUrl('registrations.programs.index', $routeUrl('public.contact.index', '#'));
@endphp

<header {{ $attributes->class('sticky top-0 z-50 border-b border-etc-surface/10 bg-etc-charcoal text-white shadow-panel') }}>
    <nav aria-label="Navigasi utama" class="mx-auto flex h-20 max-w-[1200px] items-center justify-between px-6 lg:px-8">
        <x-ui.button
            :href="$routeUrl('public.home', '/')"
            color="gray"
            class="!min-h-0 !rounded-none !bg-transparent !p-0 !font-heading !text-2xl !font-black !tracking-normal !text-white !shadow-none hover:!bg-transparent"
        >
            {{ $title }}
        </x-ui.button>

        <div class="hidden items-center gap-10 md:flex">
            @foreach ($items as $item)
                @php($isActive = $currentActive === $item['key'])
                <x-ui.button
                    :href="$item['url']"
                    color="gray"
                    size="sm"
                    @class([
                        '!min-h-0 !rounded-none !bg-transparent !p-0 !font-heading !text-[14px] !font-bold !shadow-none transition duration-200 hover:!bg-transparent',
                        '!text-etc-magenta' => $isActive,
                        '!text-white/75 hover:!text-white' => ! $isActive,
                    ])
                    aria-current="{{ $isActive ? 'page' : 'false' }}"
                >
                    <span @class(['border-b-2 pb-3', 'border-etc-magenta' => $isActive, 'border-transparent' => ! $isActive])>
                        {{ $item['label'] }}
                    </span>
                </x-ui.button>
            @endforeach
        </div>

        <div class="flex items-center gap-7">
            <x-ui.button
                :href="$loginUrl"
                color="gray"
                size="sm"
                class="!hidden !min-h-0 !rounded-pill !bg-transparent !px-1 !py-2 !font-heading !text-[14px] !font-bold !text-white/75 !shadow-none transition hover:!bg-transparent hover:!text-white md:!inline-flex"
            >
                Masuk
            </x-ui.button>
            <x-ui.button
                :href="$registerUrl"
                size="xl"
                class="!rounded-pill !px-4 !py-3 !text-[14px] shadow-soft"
            >
                Daftar Sekarang
            </x-ui.button>
        </div>
    </nav>
</header>

<nav aria-label="Navigasi mobile" class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-around rounded-t-card border-t border-etc-surface/10 bg-etc-charcoal px-4 py-3 shadow-panel md:hidden">
    @foreach ($items->take(4) as $item)
        @php($isActive = $currentActive === $item['key'])
        <x-ui.button
            :href="$item['url']"
            color="gray"
            size="sm"
            @class([
                '!flex !min-h-0 !w-16 !flex-col !items-center !justify-center !gap-1 !rounded-field !bg-transparent !p-2 !font-heading !text-xs !font-bold !uppercase !leading-[var(--etc-leading-tight)] !shadow-none transition hover:!bg-etc-surface/5',
                'scale-105 !bg-etc-surface/5 !text-etc-magenta' => $isActive,
                '!text-white/60 active:!bg-etc-surface/10' => ! $isActive,
            ])
            aria-current="{{ $isActive ? 'page' : 'false' }}"
        >
            <span class="material-symbols-outlined text-xl" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
            <span>{{ $item['label'] === 'Tentang Kami' ? 'Tentang' : $item['label'] }}</span>
        </x-ui.button>
    @endforeach
</nav>

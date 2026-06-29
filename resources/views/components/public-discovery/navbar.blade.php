@props(['active' => null])

@php
    $routeUrl = static function (?string $routeName, string $fallback = '#'): string {
        return $routeName && \Illuminate\Support\Facades\Route::has($routeName)
            ? route($routeName)
            : $fallback;
    };

    $prepareItems = static fn (array $items) => collect($items)->map(function (array $item) use ($routeUrl) {
        $item['url'] = $routeUrl($item['route'] ?? null, $item['url'] ?? '#');

        return $item;
    });

    $mainItems = $prepareItems(config('public_discovery.navbar_main_items', []));
    $moreItems = $prepareItems(config('public_discovery.navbar_more_items', []));
    $mobileMoreItems = $mainItems->skip(3)->concat($moreItems);
    $moreActive = $moreItems->contains(fn (array $item) => ($item['key'] ?? null) === $active);
    $mobileMoreActive = $mobileMoreItems->contains(fn (array $item) => ($item['key'] ?? null) === $active);
    $loginUrl = $routeUrl('auth.login', '/login');
    $registerUrl = $routeUrl('public.programs.index', $routeUrl('public.contact.index', '#'));
@endphp

<header class="public-discovery-navbar sticky top-0 z-50 border-b border-etc-surface/10 bg-etc-charcoal text-white shadow-panel" data-public-discovery-navbar>
    <nav aria-label="Navigasi utama" class="mx-auto flex h-20 max-w-[1200px] items-center justify-between px-6 lg:px-8">
        <x-ui.button
            :href="$routeUrl('public.home', '/')"
            color="gray"
            aria-label="ETC Planet"
            class="!min-h-0 !rounded-none !bg-transparent !p-0 !shadow-none hover:!bg-transparent"
        >
            <img src="{{ asset('images/logo.png') }}" alt="ETC Planet" class="h-12 w-12 rounded-full object-cover shadow-soft">
        </x-ui.button>

        <div class="hidden items-center gap-10 md:flex">
            @foreach ($mainItems as $item)
                @php($isActive = $active === $item['key'])
                <x-ui.button
                    :href="$item['url']"
                    color="gray"
                    size="sm"
                    @class([
                        '!min-h-0 !rounded-none !bg-transparent !p-0 !font-heading !text-[13px] !font-bold !shadow-none transition duration-200 hover:!bg-transparent',
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

            <div class="public-discovery-navbar__menu" data-public-nav-menu>
                <x-ui.button
                    type="button"
                    color="gray"
                    size="sm"
                    @class([
                        '!min-h-0 !rounded-none !bg-transparent !p-0 !font-heading !text-[13px] !font-bold !shadow-none transition duration-200 hover:!bg-transparent',
                        '!text-etc-magenta' => $moreActive,
                        '!text-white/75 hover:!text-white' => ! $moreActive,
                    ])
                    data-public-nav-menu-toggle
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span @class(['flex items-center gap-1 border-b-2 pb-3', 'border-etc-magenta' => $moreActive, 'border-transparent' => ! $moreActive])>
                        Jelajahi
                        <span class="material-symbols-outlined public-discovery-navbar__chevron" data-public-nav-menu-chevron>expand_more</span>
                    </span>
                </x-ui.button>

                <div class="public-discovery-navbar__dropdown hidden" data-public-nav-menu-panel>
                    <p class="public-discovery-navbar__dropdown-label">Kenali ETC Planet</p>
                    @foreach ($moreItems as $item)
                        @php($isActive = $active === $item['key'])
                        <x-ui.button
                            :href="$item['url']"
                            color="gray"
                            size="sm"
                            @class([
                                'public-discovery-navbar__dropdown-link',
                                'is-active' => $isActive,
                            ])
                            aria-current="{{ $isActive ? 'page' : 'false' }}"
                        >
                            <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                            <span>{{ $item['label'] }}</span>
                            <span class="material-symbols-outlined public-discovery-navbar__arrow">arrow_forward</span>
                        </x-ui.button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center gap-7">
            <x-ui.button
                :href="$loginUrl"
                color="gray"
                size="sm"
                class="!hidden !min-h-0 !rounded-pill !bg-transparent !px-1 !py-2 !font-heading !text-[13px] !font-bold !text-white/75 !shadow-none transition hover:!bg-transparent hover:!text-white md:!inline-flex"
            >
                Masuk
            </x-ui.button>
            <x-ui.button :href="$registerUrl" size="xl" class="!rounded-pill !px-8 !py-3 !text-[14px] shadow-soft">
                Daftar Sekarang
            </x-ui.button>
        </div>
    </nav>
</header>

<div class="public-discovery-mobile-menu hidden" data-public-nav-mobile-panel>
    <p class="public-discovery-navbar__dropdown-label">Halaman lainnya</p>
    <div class="grid grid-cols-2 gap-2">
        @foreach ($mobileMoreItems as $item)
            <x-ui.button
                :href="$item['url']"
                color="gray"
                size="sm"
                class="public-discovery-mobile-menu__link"
            >
                <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </x-ui.button>
        @endforeach
    </div>
</div>

<nav aria-label="Navigasi mobile" class="public-discovery-mobile-nav fixed inset-x-0 bottom-0 z-50 items-center justify-around rounded-t-card border-t border-etc-surface/10 bg-etc-charcoal px-4 py-3 shadow-panel md:hidden">
    @foreach ($mainItems->take(3) as $item)
        @php($isActive = $active === $item['key'])
        <x-ui.button
            :href="$item['url']"
            color="gray"
            size="sm"
            @class([
                'public-discovery-mobile-nav__link !min-h-0 !w-16 !flex-col !items-center !justify-center !gap-1 !rounded-card !bg-transparent !p-2 !font-heading !text-[10px] !font-bold !uppercase !shadow-none transition hover:!bg-etc-surface/5',
                'scale-105 !bg-etc-surface/5 !text-etc-magenta' => $isActive,
                '!text-white/60 active:!bg-etc-surface/10' => ! $isActive,
            ])
            aria-current="{{ $isActive ? 'page' : 'false' }}"
        >
            <span class="material-symbols-outlined text-xl" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
            <span>{{ $item['label'] }}</span>
        </x-ui.button>
    @endforeach

    <x-ui.button
        type="button"
        color="gray"
        size="sm"
        @class([
            'public-discovery-mobile-nav__link !min-h-0 !w-16 !flex-col !items-center !justify-center !gap-1 !rounded-card !bg-transparent !p-2 !font-heading !text-[10px] !font-bold !uppercase !shadow-none transition hover:!bg-etc-surface/5',
            'scale-105 !bg-etc-surface/5 !text-etc-magenta' => $mobileMoreActive,
            '!text-white/60 active:!bg-etc-surface/10' => ! $mobileMoreActive,
        ])
        data-public-nav-mobile-toggle
        aria-expanded="false"
    >
        <span class="material-symbols-outlined">apps</span>
        <span>Lainnya</span>
    </x-ui.button>
</nav>

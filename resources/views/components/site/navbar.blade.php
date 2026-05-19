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
        ['label' => 'Program', 'route' => null, 'url' => '/#program', 'key' => 'program', 'icon' => 'school'],
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

    $loginUrl ??= $routeUrl('auth.login', '#');
    $registerUrl ??= $routeUrl('public.contact.index', '/contact');
@endphp

<header {{ $attributes->class('sticky top-0 z-50 bg-[#2b2b2b] text-white shadow-[0_10px_24px_rgba(0,0,0,0.22)]') }}>
    <nav aria-label="Navigasi utama" class="mx-auto flex h-[86px] max-w-[1120px] items-center justify-between px-5 lg:px-0">
        <a href="{{ $routeUrl('public.home', '/') }}" class="font-heading text-[20px] font-black tracking-normal text-white">
            {{ $title }}
        </a>

        <div class="hidden items-center gap-10 md:flex">
            @foreach ($items as $item)
                @php($isActive = $currentActive === $item['key'])
                <a
                    href="{{ $item['url'] }}"
                    @class([
                        'px-0 py-2 font-heading text-[13px] font-bold transition duration-200',
                        'text-etc-magenta' => $isActive,
                        'text-zinc-300 hover:text-white' => ! $isActive,
                    ])
                    @if ($isActive) aria-current="page" @endif
                >
                    <span @class(['border-b-2 pb-3', 'border-etc-magenta' => $isActive, 'border-transparent' => ! $isActive])>
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-7">
            <a href="{{ $loginUrl }}" class="hidden rounded-full px-1 py-2 font-heading text-[13px] font-bold text-zinc-300 transition hover:text-white md:inline-flex">
                Masuk
            </a>
            <a href="{{ $registerUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-etc-magenta px-8 py-3 font-heading text-[14px] font-bold text-white shadow-soft transition hover:bg-etc-primary">
                Daftar Sekarang
            </a>
        </div>
    </nav>
</header>

<nav aria-label="Navigasi mobile" class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-around rounded-t-3xl border-t border-white/10 bg-[#2b2b2b] px-4 py-3 shadow-[0_-12px_30px_rgba(0,0,0,0.28)] md:hidden">
    @foreach ($items->take(4) as $item)
        @php($isActive = $currentActive === $item['key'])
        <a
            href="{{ $item['url'] }}"
            @class([
                'flex w-16 flex-col items-center justify-center gap-1 rounded-2xl p-2 font-heading text-[10px] font-bold uppercase transition',
                'scale-105 bg-white/5 text-etc-magenta' => $isActive,
                'text-zinc-400 active:bg-white/10' => ! $isActive,
            ])
            @if ($isActive) aria-current="page" @endif
        >
            <span class="material-symbols-outlined text-xl" @if ($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
            <span>{{ $item['label'] === 'Tentang Kami' ? 'Tentang' : $item['label'] }}</span>
        </a>
    @endforeach
</nav>

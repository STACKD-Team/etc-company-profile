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
        ['label' => 'Beranda', 'route' => 'home', 'url' => '/', 'key' => 'home', 'icon' => 'home'],
        ['label' => 'Program', 'route' => 'programs.index', 'url' => '#program', 'key' => 'program', 'icon' => 'school'],
        ['label' => 'Reels', 'route' => 'reels.index', 'url' => '#reels', 'key' => 'reels', 'icon' => 'smart_display'],
        ['label' => 'Tentang Kami', 'route' => 'about', 'url' => '#tentang', 'key' => 'about', 'icon' => 'groups'],
        ['label' => 'Kontak', 'route' => 'contact', 'url' => '#kontak', 'key' => 'contact', 'icon' => 'call'],
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

    $loginUrl ??= $routeUrl('login');
    $registerUrl ??= $routeUrl('registrations.create', '#daftar');
@endphp

<header {{ $attributes->class('sticky top-0 z-50 border-b border-white/10 bg-etc-charcoal text-white shadow-[0_12px_32px_rgba(39,23,28,0.18)]') }}>
    <nav aria-label="Navigasi utama" class="mx-auto flex h-20 max-w-[1200px] items-center justify-between px-6 lg:px-8">
        <a href="{{ $routeUrl('home', '/') }}" class="font-heading text-2xl font-black tracking-normal text-white">
            {{ $title }}
        </a>

        <div class="hidden items-center gap-7 md:flex">
            @foreach ($items as $item)
                @php($isActive = $currentActive === $item['key'])
                <a
                    href="{{ $item['url'] }}"
                    @class([
                        'rounded-full px-1 py-2 font-heading text-sm font-bold transition duration-200',
                        'text-etc-magenta' => $isActive,
                        'text-zinc-300 hover:text-white' => ! $isActive,
                    ])
                    @if ($isActive) aria-current="page" @endif
                >
                    <span @class(['border-b-2 pb-1', 'border-etc-magenta' => $isActive, 'border-transparent' => ! $isActive])>
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ $loginUrl }}" class="hidden rounded-full px-4 py-2 font-heading text-sm font-bold text-zinc-300 transition hover:bg-white/5 hover:text-white md:inline-flex">
                Masuk
            </a>
            <a href="{{ $registerUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white shadow-soft transition hover:bg-etc-primary">
                Daftar Sekarang
            </a>
        </div>
    </nav>
</header>

<nav aria-label="Navigasi mobile" class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-around rounded-t-3xl border-t border-white/10 bg-etc-charcoal px-4 py-3 shadow-[0_-12px_30px_rgba(0,0,0,0.28)] md:hidden">
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

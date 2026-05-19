@props([
    'brand' => 'ETC Planet',
    'address' => 'Jl. S. Parman No. 202B, Padang',
    'linkGroups' => null,
    'socialLinks' => null,
    'contact' => null,
])

@php
    $routeUrl = static function (?string $routeName, string $fallback = '#'): string {
        if (! $routeName || ! \Illuminate\Support\Facades\Route::has($routeName)) {
            return $fallback;
        }

        return route($routeName);
    };

    $linkUrl = static fn (array $link): string => $routeUrl($link['route'] ?? null, $link['url'] ?? '#');

    $linkGroups = $linkGroups ?? [
        'Navigasi' => [
            ['label' => 'Beranda', 'route' => 'home', 'url' => '/'],
            ['label' => 'Program', 'route' => 'programs.index', 'url' => '#program'],
            ['label' => 'Reels', 'route' => 'reels.index', 'url' => '#reels'],
            ['label' => 'Tentang Kami', 'route' => 'about', 'url' => '#tentang'],
        ],
        'Program' => [
            ['label' => 'English for Kids', 'url' => '#program'],
            ['label' => 'English Conversation', 'url' => '#program'],
            ['label' => 'TOEFL/TOEIC/IELTS', 'url' => '#program'],
            ['label' => 'Private Class', 'url' => '#program'],
        ],
    ];

    $socialLinks = $socialLinks ?? [
        ['label' => 'Website', 'url' => '#', 'svg' => 'footer-globe'],
        ['label' => 'Bagikan', 'url' => '#', 'svg' => 'footer-share'],
        ['label' => 'Pesan', 'url' => '#', 'svg' => 'footer-chat'],
    ];

    $contact = $contact ?? [
        'phone' => '+62 812-0000-0000',
        'email' => 'hello@etcplanet.test',
        'hours' => 'Senin-Sabtu, 09.00-18.30',
    ];
@endphp

<footer {{ $attributes->class('bg-etc-charcoal pb-24 pt-16 text-white md:pb-8') }}>
    <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-10 px-6 md:grid-cols-4 lg:px-8">
        <div class="space-y-4">
            <div class="font-heading text-2xl font-black">{{ $brand }}</div>
            <p class="max-w-xs text-sm leading-6 text-zinc-400">
                Lembaga kursus bahasa terpercaya di Padang dengan pengalaman belajar yang ramah, interaktif, dan profesional.
            </p>
            <p class="text-sm leading-6 text-zinc-400">
                {{ $address }}
            </p>
            <div class="flex gap-3">
                @foreach ($socialLinks as $link)
                    <a href="{{ $link['url'] ?? '#' }}" aria-label="{{ $link['label'] ?? 'Social link' }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-zinc-300 transition hover:bg-etc-magenta hover:text-white">
                        @if ($link['svg'] ?? null)
                            <x-ui.icon :name="$link['svg']" class="h-5 w-5" />
                        @else
                            <span class="material-symbols-outlined text-xl">{{ $link['icon'] ?? 'link' }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        @foreach ($linkGroups as $heading => $links)
            <div>
                <h2 class="font-heading text-sm font-bold uppercase text-white">{{ $heading }}</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($links as $link)
                        <li>
                            <a href="{{ $linkUrl($link) }}" class="text-sm text-zinc-400 transition hover:text-etc-magenta">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div>
            <h2 class="font-heading text-sm font-bold uppercase text-white">Kontak</h2>
            <ul class="mt-4 space-y-3 text-sm text-zinc-400">
                <li class="flex gap-3">
                    <x-ui.icon name="contact-location" class="h-5 w-5 shrink-0" />
                    <span>{{ $address }}</span>
                </li>
                <li class="flex gap-3">
                    <x-ui.icon name="contact-phone" class="h-5 w-5 shrink-0" />
                    <span>{{ $contact['phone'] ?? '-' }}</span>
                </li>
                <li class="flex gap-3">
                    <x-ui.icon name="contact-mail" class="h-5 w-5 shrink-0" />
                    <span>{{ $contact['email'] ?? '-' }}</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="mx-auto mt-12 max-w-[1200px] border-t border-white/10 px-6 pt-6 text-sm text-zinc-500 lg:px-8">
        &copy; {{ now()->year }} {{ $brand }}. All rights reserved.
    </div>
</footer>

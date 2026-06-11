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
            ['label' => 'Beranda', 'route' => 'public.home', 'url' => '/'],
            ['label' => 'Program', 'route' => 'public.programs.index', 'url' => '/programs'],
            ['label' => 'Reels', 'route' => 'public.reels.index', 'url' => '#reels'],
            ['label' => 'Tentang Kami', 'route' => 'public.about', 'url' => '#tentang'],
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

<footer {{ $attributes->class('relative bg-etc-charcoal pb-24 pt-16 text-white md:pb-16') }}>
    <div class="mx-auto grid max-w-[1120px] grid-cols-1 gap-12 px-5 md:grid-cols-[260px_1fr] lg:px-0">
        <div class="space-y-5">
            <div class="font-heading text-[18px] font-black">{{ $brand }}</div>
            <p class="text-[13px] leading-7 text-white/60">
                &copy; {{ now()->year }} ETC Planet.<br>
                {{ $address }}
            </p>
            <x-ui.button
                href="https://www.instagram.com/etcplanet/"
                target="_blank"
                color="gray"
                aria-label="Instagram ETC Planet"
                class="!inline-flex !h-8 !min-h-8 !w-8 !items-center !justify-center !rounded-md !border !border-white/20 !bg-transparent !p-0 !text-white/75 !shadow-none transition hover:!border-etc-magenta hover:!bg-transparent hover:!text-etc-magenta"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5Z" fill="currentColor"/>
                    <path d="M12 7.25A4.75 4.75 0 1 1 12 16.75 4.75 4.75 0 0 1 12 7.25Zm0 2A2.75 2.75 0 1 0 12 14.75 2.75 2.75 0 0 0 12 9.25Z" fill="currentColor"/>
                    <path d="M17.25 6.75a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" fill="currentColor"/>
                </svg>
            </x-ui.button>
        </div>

        <div class="pt-1 md:pl-20">
            <ul class="space-y-5 text-[13px]">
                @foreach ([
                    ['label' => 'Kebijakan Privasi', 'url' => '#'],
                    ['label' => 'Syarat & Ketentuan', 'url' => '#'],
                    ['label' => 'FAQ', 'route' => 'public.faq.index', 'url' => '#'],
                    ['label' => 'Karir', 'url' => '#'],
                ] as $link)
                    <li>
                        <x-ui.button
                            :href="$linkUrl($link)"
                            color="gray"
                            size="sm"
                            class="!min-h-0 !rounded-none !bg-transparent !p-0 !text-white/50 !underline !decoration-etc-magenta/60 !underline-offset-4 !shadow-none transition hover:!bg-transparent hover:!text-etc-magenta"
                        >
                            {{ $link['label'] }}
                        </x-ui.button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</footer>

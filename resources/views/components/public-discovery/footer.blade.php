@php
    $routeUrl = static function (string $routeName, string $fallback = '#'): string {
        return \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : $fallback;
    };

    $discoveryLinks = [
        ['label' => 'Tentang ETC', 'route' => 'public.about'],
        ['label' => 'Team Pengajar', 'route' => 'public.team.index'],
        ['label' => 'Fasilitas', 'route' => 'public.facilities.index'],
        ['label' => 'Galeri', 'route' => 'public.gallery.index'],
    ];

    $visitorLinks = [
        ['label' => 'Program', 'route' => 'public.programs.index'],
        ['label' => 'Reels', 'route' => 'public.reels.index'],
        ['label' => 'FAQ', 'route' => 'public.faq.index'],
        ['label' => 'Kontak', 'route' => 'public.contact.index'],
    ];
@endphp

<footer class="public-discovery-footer" data-public-discovery-footer>
    <div class="public-discovery-footer__accent" aria-hidden="true"></div>

    <div class="public-discovery-footer__content">
        <div class="public-discovery-footer__brand">
            <x-ui.button
                :href="$routeUrl('public.home', '/')"
                color="gray"
                class="!min-h-0 !rounded-none !bg-transparent !p-0 !font-heading !text-2xl !font-black !text-white !shadow-none hover:!bg-transparent"
            >
                ETC Planet
            </x-ui.button>
            <p class="mt-4 max-w-sm text-sm leading-7 text-white/60">
                Tempat belajar bahasa Inggris di Padang dengan suasana kelas yang aktif, hangat, dan terarah.
            </p>
            <div class="mt-6 flex items-start gap-3 text-sm leading-6 text-white/65">
                <span class="material-symbols-outlined mt-0.5 text-lg text-etc-magenta">location_on</span>
                <span>Jl. S. Parman No. 202B, Ulak Karang Selatan, Padang</span>
            </div>
        </div>

        <div>
            <p class="public-discovery-footer__heading">Kenali ETC</p>
            <ul class="mt-5 space-y-3">
                @foreach ($discoveryLinks as $link)
                    <li>
                        <x-ui.button
                            :href="$routeUrl($link['route'])"
                            color="gray"
                            size="sm"
                            class="public-discovery-footer__link"
                        >
                            {{ $link['label'] }}
                        </x-ui.button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="public-discovery-footer__heading">Untuk Pengunjung</p>
            <ul class="mt-5 space-y-3">
                @foreach ($visitorLinks as $link)
                    <li>
                        <x-ui.button
                            :href="$routeUrl($link['route'])"
                            color="gray"
                            size="sm"
                            class="public-discovery-footer__link"
                        >
                            {{ $link['label'] }}
                        </x-ui.button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="public-discovery-footer__contact">
            <p class="public-discovery-footer__heading">Terhubung</p>
            <p class="mt-5 text-sm leading-7 text-white/60">
                Punya pertanyaan tentang program, jadwal, atau placement test?
            </p>
            <x-ui.button
                :href="$routeUrl('public.contact.index')"
                size="lg"
                class="mt-5 !rounded-pill !px-6"
                icon="heroicon-m-chat-bubble-left-right"
            >
                Hubungi ETC
            </x-ui.button>

            <x-ui.button
                href="https://www.instagram.com/etcplanet/"
                target="_blank"
                color="gray"
                size="sm"
                aria-label="Instagram ETC Planet"
                class="public-discovery-footer__instagram"
            >
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5Z" fill="currentColor"/>
                    <path d="M12 7.25A4.75 4.75 0 1 1 12 16.75 4.75 4.75 0 0 1 12 7.25Zm0 2A2.75 2.75 0 1 0 12 14.75 2.75 2.75 0 0 0 12 9.25Z" fill="currentColor"/>
                    <path d="M17.25 6.75a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" fill="currentColor"/>
                </svg>
                <span>@etcplanet</span>
            </x-ui.button>
        </div>
    </div>

    <div class="public-discovery-footer__bottom">
        <p>&copy; {{ now()->year }} ETC Planet. Seluruh hak cipta dilindungi.</p>
        <p>Education Tutorial Centre Padang</p>
    </div>
</footer>

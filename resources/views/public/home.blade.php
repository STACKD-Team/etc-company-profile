<x-layouts.public title="Beranda">
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path, string $fallback = 'images/hero-img.jpeg'): string => $media->mediaUrl($path, $fallback);
        $formatMoney = static fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
        $partners = $partners ?? collect();
    @endphp

    <section class="public-section bg-etc-surface">
        <div class="pointer-events-none absolute right-0 top-0 h-80 w-80 translate-x-1/3 rounded-[42%_58%_49%_51%] bg-etc-surface-container blur-3xl"></div>
        <div class="public-shell grid min-h-[680px] items-center gap-12 lg:grid-cols-[1fr_0.9fr]">
            <div class="public-reveal" data-public-reveal>
                <p class="public-eyebrow">ETC Planet Padang</p>
                <h1 class="public-title mt-4 max-w-3xl">Belajar bahasa yang dekat, praktis, dan percaya diri</h1>
                <p class="public-subtitle mt-6 max-w-2xl">
                    Kursus bahasa untuk anak, remaja, mahasiswa, dan profesional dengan kelas kecil, instructor aktif, dan placement test offline agar siswa mulai dari level yang tepat.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-ui.button :href="route('registrations.programs.index')" size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                        Daftar Sekarang
                    </x-ui.button>
                    <x-ui.button :href="route('public.programs.index')" color="gray" outlined size="xl">
                        Lihat Program
                    </x-ui.button>
                </div>

                <div class="mt-8 grid max-w-2xl gap-3 sm:grid-cols-3">
                    @foreach ([
                        ['icon' => 'groups', 'value' => $stats['students'].' Siswa'],
                        ['icon' => 'school', 'value' => $stats['instructors'].' Instruktur'],
                        ['icon' => 'article', 'value' => $stats['programs'].' Program'],
                    ] as $item)
                        <div class="flex items-center gap-2 border-t-2 border-etc-outline-variant pt-4 font-heading text-sm font-bold text-etc-on-surface">
                            <span class="material-symbols-outlined text-xl text-etc-magenta">{{ $item['icon'] }}</span>
                            <span>{{ $item['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-[440px] public-reveal" data-public-reveal>
                <div class="absolute -left-8 top-12 h-32 w-32 rounded-[54%_46%_39%_61%] bg-etc-surface-high"></div>
                <img src="{{ asset('images/hero-img.jpeg') }}" alt="Guru ETC Planet mengajar siswa di kelas" class="relative aspect-[4/5] w-full rounded-card border-2 border-etc-outline-variant object-cover shadow-panel">
                <div class="public-card absolute -bottom-5 left-4 right-4 flex items-center gap-4 p-4 md:left-auto md:right-6 md:w-72">
                    <span class="flex h-10 w-10 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                        <span class="material-symbols-outlined">verified</span>
                    </span>
                    <div>
                        <p class="font-heading text-sm font-bold">Placement Test</p>
                        <p class="text-sm text-etc-on-muted">Level belajar lebih tepat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-etc-charcoal py-10 text-white">
        <div class="public-shell grid grid-cols-2 gap-5 md:grid-cols-4">
            @foreach ([
                ['value' => $stats['students'], 'label' => 'Siswa Terdata'],
                ['value' => $stats['instructors'], 'label' => 'Instruktur'],
                ['value' => $stats['programs'], 'label' => 'Program Aktif'],
                ['value' => $stats['satisfaction'], 'label' => 'Kepuasan Siswa'],
            ] as $stat)
                <div class="border-l-2 border-white/10 pl-5">
                    <p class="font-heading text-3xl font-bold">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-xs font-bold uppercase tracking-[0.16em] text-white/60">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="program" class="public-section bg-etc-surface-low">
        <div class="public-shell">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">Program ETC</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Program kursus unggulan</h2>
                <p class="public-subtitle mt-4">Lihat pilihan program aktif, cek biaya, dan ambil promo yang sedang tersedia sebelum mulai pendaftaran.</p>
            </div>

            @if ($programs->isNotEmpty())
                <div class="mt-12 grid gap-5 md:grid-cols-3">
                    @foreach ($programs as $program)
                        @php
                            $promotion = $program->currentPromotion();
                            $finalPrice = $promotion?->finalPrice($program->price) ?? (float) $program->price;
                        @endphp

                        <article class="public-card group overflow-hidden public-reveal" data-public-reveal data-sprint1-program-card>
                            <div class="relative h-48 overflow-hidden bg-etc-surface-container" data-program-cover>
                                <img src="{{ $assetUrl($program->thumbnail, 'images/pu1-img.jpg') }}" alt="Cover program {{ $program->name }}" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-etc-charcoal/75 via-etc-charcoal/10 to-transparent"></div>
                                <x-ui.badge color="primary" class="absolute left-4 top-4 !bg-etc-surface !text-etc-magenta">
                                    {{ str($program->category)->replace('_', ' ')->headline() }}
                                </x-ui.badge>
                                @if ($promotion)
                                    <x-ui.badge color="primary" class="absolute right-4 top-4 !bg-etc-magenta !text-white" data-promo-badge>
                                        {{ $promotion->displayBadge() }}
                                    </x-ui.badge>
                                @endif
                            </div>

                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-heading text-xl font-bold leading-tight text-etc-on-surface">{{ $program->name }}</h3>
                                    <x-ui.badge color="gray" class="shrink-0">{{ str($program->target_age ?? 'all')->headline() }}</x-ui.badge>
                                </div>
                                <p class="mt-4 min-h-20 text-sm leading-6 text-etc-on-muted">{{ $program->description ?: 'Program aktif ETC Planet dengan kelas kecil dan pendampingan instruktur.' }}</p>
                                <div class="mt-5 border-t-2 border-etc-outline-variant pt-4">
                                    <p class="font-heading text-xs font-bold uppercase tracking-[0.16em] text-etc-on-muted">Mulai Dari</p>
                                    @if ($promotion)
                                        <p class="mt-2 font-heading text-sm font-bold text-etc-on-muted line-through">{{ $formatMoney($program->price) }}</p>
                                        <p class="font-heading text-2xl font-bold text-etc-magenta" data-promo-final-price>{{ $formatMoney($finalPrice) }}</p>
                                    @else
                                        <p class="mt-2 font-heading text-2xl font-bold text-etc-magenta">{{ $formatMoney($program->price) }}</p>
                                    @endif
                                </div>
                                <div class="mt-5 flex items-center justify-between gap-3">
                                    <x-ui.button :href="route('public.programs.show', $program)" color="gray" outlined size="xl">
                                        Detail
                                    </x-ui.button>
                                    <x-ui.button :href="route('registrations.programs.index', ['program' => $program->id])" size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                                        Daftar
                                    </x-ui.button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-12">
                    <x-ui.empty-state
                        heading="Data program sedang disiapkan"
                        description="Kamu tetap bisa konsultasi program yang cocok melalui form kontak ETC Planet."
                        icon="heroicon-o-academic-cap"
                        contained
                    />
                </div>
            @endif
        </div>
    </section>

    <section class="public-section bg-etc-surface">
        <div class="public-shell">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">Alur Daftar</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Pendaftaran dibuat singkat dan jelas</h2>
            </div>
            <div class="mt-12 grid gap-4 md:grid-cols-5">
                @foreach ([
                    ['title' => 'Pilih Program', 'desc' => 'Bandingkan kelas dan pilih target belajar.', 'icon' => 'touch_app'],
                    ['title' => 'Isi Formulir', 'desc' => 'Lengkapi data calon siswa.', 'icon' => 'apps'],
                    ['title' => 'Pembayaran', 'desc' => 'Selesaikan biaya awal.', 'icon' => 'payments'],
                    ['title' => 'Placement Test', 'desc' => 'Ikuti tes offline di ETC.', 'icon' => 'assignment'],
                    ['title' => 'Mulai Belajar', 'desc' => 'Masuk kelas sesuai level.', 'icon' => 'school'],
                ] as $step)
                    <article class="public-card p-5 text-center public-reveal" data-public-reveal>
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-selector bg-etc-magenta text-white">
                            <span class="material-symbols-outlined">{{ $step['icon'] }}</span>
                        </span>
                        <h3 class="mt-4 font-heading text-base font-bold">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $step['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($partners->isNotEmpty())
        <section class="public-section bg-etc-surface-low" data-partner-section>
            <div class="public-shell">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end public-reveal" data-public-reveal>
                    <div>
                        <p class="public-eyebrow">Kerja Sama ETC</p>
                        <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Partner belajar dan pengembangan bahasa</h2>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-etc-on-muted">Kolaborasi yang membantu program ETC Planet tetap dekat dengan kebutuhan sekolah, komunitas, dan dunia kerja.</p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    @foreach ($partners as $partner)
                        @php($website = $partner->meta['website'] ?? null)
                        <article class="public-card p-5 public-reveal" data-public-reveal>
                            <div class="flex items-center gap-4">
                                <img src="{{ $assetUrl($partner->image, 'images/hero-img.jpeg') }}" alt="Logo {{ $partner->title }}" class="h-14 w-14 rounded-card border-2 border-etc-outline-variant bg-etc-surface object-cover p-2">
                                <div>
                                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">{{ $partner->meta['category'] ?? 'Partner' }}</p>
                                    <h3 class="mt-1 font-heading text-lg font-bold">{{ $partner->title }}</h3>
                                </div>
                            </div>
                            <p class="mt-4 text-sm leading-7 text-etc-on-muted">{{ $partner->body ?: 'Partner ETC Planet dalam pengembangan pembelajaran bahasa.' }}</p>
                            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-bold text-etc-on-muted">
                                @if ($partner->meta['since'] ?? null)
                                    <x-ui.badge color="gray">Sejak {{ $partner->meta['since'] }}</x-ui.badge>
                                @endif
                                @if ($website)
                                    <x-ui.button
                                        :href="$website"
                                        target="_blank"
                                        color="gray"
                                        size="sm"
                                        class="!min-h-0 !rounded-none !bg-transparent !p-0 !font-heading !text-[13px] !font-bold !text-etc-magenta !underline !decoration-etc-magenta/45 !underline-offset-4 !shadow-none hover:!bg-transparent hover:!text-etc-primary"
                                    >
                                        Kunjungi partner
                                    </x-ui.button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="reels" class="public-section bg-etc-charcoal text-white">
        <div class="public-shell">
            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end public-reveal" data-public-reveal>
                <div>
                    <p class="public-eyebrow">Reels ETC</p>
                    <h2 class="mt-3 font-heading text-4xl font-bold">Cuplikan suasana belajar</h2>
                    <p class="mt-3 max-w-xl text-white/65">Intip kelas, event, dan tips singkat dari video pendek ETC Planet.</p>
                </div>
                <x-ui.button :href="route('public.reels.index')" color="gray" outlined size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                    Lihat Semua
                </x-ui.button>
            </div>

            @if ($reels->isNotEmpty())
                <div class="mt-10 grid gap-5 md:grid-cols-4">
                    @foreach ($reels as $reel)
                        <a href="{{ route('public.reels.show', $reel) }}" class="public-card-dark group overflow-hidden text-left public-reveal" data-public-reveal>
                            <div class="relative aspect-[9/14] overflow-hidden bg-black">
                                <video preload="metadata" muted playsinline poster="{{ $assetUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}" class="h-full w-full object-cover opacity-90">
                                    <source src="{{ $assetUrl($reel->video_path, 'videos/video1.mp4') }}" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                                <x-ui.badge color="primary" class="absolute left-3 top-3 !bg-etc-magenta !text-white">{{ $reel->category }}</x-ui.badge>
                                <span class="absolute left-1/2 top-1/2 flex h-12 w-12 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-selector bg-etc-surface text-etc-magenta opacity-0 shadow-soft group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-3xl">play_arrow</span>
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="font-heading text-sm font-bold leading-6 text-white">{{ $reel->title }}</h3>
                                <div class="mt-3 flex items-center justify-between text-xs text-white/60">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">visibility</span>{{ number_format((int) $reel->views_count) }}</span>
                                    <span class="flex items-center gap-1 text-etc-magenta"><span class="material-symbols-outlined text-sm">favorite</span>{{ number_format((int) $reel->likes_count) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-10">
                    <x-ui.empty-state
                        heading="Reels ETC Planet akan tampil di sini"
                        description="Video pendek akan muncul setelah admin mempublikasikannya."
                        icon="heroicon-o-video-camera"
                        contained
                    />
                </div>
            @endif
        </div>
    </section>

    <section class="public-section bg-etc-surface-low">
        <div class="public-shell text-center">
            <div class="mx-auto max-w-2xl public-reveal" data-public-reveal>
                <p class="public-eyebrow">Team</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Pengajar profesional</h2>
                <p class="public-subtitle mt-4">Belajar bersama instructor yang ramah, aktif memberi feedback, dan terbiasa mendampingi berbagai level siswa.</p>
            </div>

            @if ($instructors->isNotEmpty())
                <div class="mt-12 grid gap-8 md:grid-cols-4">
                    @foreach ($instructors as $teacher)
                        <article class="public-reveal" data-public-reveal>
                            <img src="{{ $assetUrl($teacher->avatar, 'images/Ms. Debby.jpeg') }}" alt="Foto {{ $teacher->name }}" class="mx-auto h-36 w-36 rounded-full border-2 border-etc-outline-variant object-cover shadow-soft">
                            <h3 class="mt-5 font-heading text-lg font-bold">{{ $teacher->name }}</h3>
                            <p class="mt-1 font-heading text-sm font-bold text-etc-magenta">{{ $teacher->instructor_position }}</p>
                            <p class="mt-1 text-sm text-etc-on-muted">{{ $teacher->instructor_specialization }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-10">
                    <x-ui.empty-state
                        heading="Profil pengajar pilihan akan tampil di sini"
                        description="Data instructor public akan muncul setelah tersedia."
                        icon="heroicon-o-users"
                        contained
                    />
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>

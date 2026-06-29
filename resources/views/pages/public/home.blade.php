<x-layouts.public title="Beranda" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="home" />
    @php
        $media = app(\App\Services\PublicDiscoveryService::class);
        $assetUrl = static fn (?string $path, string $fallback = 'images/hero-img.jpeg', string $resourceType = 'image'): string => $media->mediaUrl($path, $fallback, $resourceType);
        $formatMoney = static fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
        $partners = $partners ?? collect();
        $testimonials = $testimonials ?? collect();
        $heroImage = $assetUrl($profile?->image, 'images/hero-img.jpeg');
        $statItems = collect([
            ['value' => $stats['students'], 'label' => 'Siswa Terdata'],
            ['value' => $stats['instructors'], 'label' => 'Instruktur'],
            ['value' => $stats['programs'], 'label' => 'Program Aktif'],
            ['value' => $stats['satisfaction'], 'label' => 'Kepuasan Siswa'],
        ])->filter(fn (array $stat): bool => filled($stat['value']));
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

                <div class="mt-8 flex flex-col flex-wrap gap-3 sm:flex-row">
                    <x-ui.button :href="route('public.programs.index')" size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                        Daftar Sekarang
                    </x-ui.button>
                    <x-ui.button :href="route('public.programs.index')" color="gray" outlined size="xl">
                        Lihat Program
                    </x-ui.button>
                    <x-ui.button :href="route('public.contact.index')" color="gray" outlined size="xl" icon="heroicon-m-chat-bubble-left-right">
                        Tanya ETC
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
                <img src="{{ $heroImage }}" alt="Suasana belajar ETC Planet" class="relative aspect-[4/5] w-full rounded-card border-2 border-etc-outline-variant object-cover shadow-panel" data-home-hero-image>
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

    <section class="public-home-stats">
        <div class="public-shell public-home-stats__grid">
            @foreach ($statItems as $stat)
                @php
                    $counterValue = (string) $stat['value'];
                    $counterTarget = (float) preg_replace('/[^\d.]/', '', $counterValue);
                    $counterSuffix = str_contains($counterValue, '%') ? '%' : '';
                @endphp
                <div class="public-home-stats__item public-reveal" data-public-reveal>
                    <p
                        class="public-home-stats__value"
                        data-public-stat-counter
                        data-counter-target="{{ $counterTarget }}"
                        data-counter-suffix="{{ $counterSuffix }}"
                    >{{ $stat['value'] }}</p>
                    <p class="public-home-stats__label">{{ $stat['label'] }}</p>
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
                <div class="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-3" data-home-program-grid>
                    @foreach ($programs->take(6) as $program)
                        @php
                            $promotion = $program->currentPromotion();
                            $finalPrice = $promotion?->finalPrice($program->price) ?? (float) $program->price;
                        @endphp

                        <article class="public-home-program-card public-card group overflow-hidden public-reveal" data-public-reveal data-sprint1-program-card>
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

                            <div class="public-home-program-card__body p-5">
                                <div class="public-home-program-card__heading">
                                    <h3 class="font-heading text-xl font-bold leading-tight text-etc-on-surface">{{ $program->name }}</h3>
                                    <x-ui.badge color="gray" class="shrink-0">{{ str($program->target_age ?? 'all')->headline() }}</x-ui.badge>
                                </div>
                                <p class="public-home-program-card__description">{{ $program->description ?: 'Program aktif ETC Planet dengan kelas kecil dan pendampingan instruktur.' }}</p>
                                <div class="public-home-program-card__pricing">
                                    <p class="font-heading text-xs font-bold uppercase tracking-[0.16em] text-etc-on-muted">Mulai Dari</p>
                                    <div class="public-home-program-card__price-stack">
                                        @if ($promotion)
                                            <p class="font-heading text-sm font-bold text-etc-on-muted line-through">{{ $formatMoney($program->price) }}</p>
                                            <p class="font-heading text-2xl font-bold text-etc-magenta" data-promo-final-price>{{ $formatMoney($finalPrice) }}</p>
                                        @else
                                            <span class="public-home-program-card__price-placeholder" aria-hidden="true">&nbsp;</span>
                                            <p class="font-heading text-2xl font-bold text-etc-magenta">{{ $formatMoney($program->price) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="public-home-program-card__actions">
                                    <x-ui.button :href="route('public.programs.show', $program)" color="gray" outlined size="xl">
                                        Detail
                                    </x-ui.button>
                                    <x-ui.button :href="route('registrations.create', ['program' => $program->id])" size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                                        Daftar
                                    </x-ui.button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-8 text-center public-reveal" data-public-reveal>
                    <x-ui.button
                        :href="route('public.programs.index')"
                        color="gray"
                        size="xl"
                        icon="heroicon-m-arrow-right"
                        icon-position="after"
                        class="public-section-action public-section-action--light"
                    >
                        Lihat Semua Program
                    </x-ui.button>
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

    <section class="public-registration-flow">
        <div class="public-shell">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">Alur Daftar</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Pendaftaran dibuat singkat dan jelas</h2>
                <p class="public-subtitle mt-4">Lima langkah terarah dari memilih program sampai masuk kelas sesuai level kemampuan.</p>
            </div>
            <div class="public-registration-flow__track" data-public-registration-flow>
                @foreach ([
                    ['title' => 'Pilih Program', 'desc' => 'Bandingkan kelas dan pilih target belajar.', 'icon' => 'touch_app'],
                    ['title' => 'Isi Formulir', 'desc' => 'Lengkapi data calon siswa.', 'icon' => 'apps'],
                    ['title' => 'Pembayaran', 'desc' => 'Selesaikan biaya awal.', 'icon' => 'payments'],
                    ['title' => 'Placement Test', 'desc' => 'Ikuti tes offline di ETC.', 'icon' => 'assignment'],
                    ['title' => 'Mulai Belajar', 'desc' => 'Masuk kelas sesuai level.', 'icon' => 'school'],
                ] as $index => $step)
                    <article
                        class="public-registration-flow__step public-reveal"
                        data-public-reveal
                        data-registration-flow-step="{{ $index + 1 }}"
                    >
                        <span class="public-registration-flow__icon">
                            <span class="material-symbols-outlined">{{ $step['icon'] }}</span>
                        </span>
                        <h3 class="public-registration-flow__title">{{ $step['title'] }}</h3>
                        <p class="public-registration-flow__description">{{ $step['desc'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="mt-10 text-center public-reveal" data-public-reveal>
                <x-ui.button
                    :href="route('public.programs.index')"
                    size="xl"
                    icon="heroicon-m-arrow-right"
                    icon-position="after"
                    data-registration-flow-start
                >
                    Mulai Pendaftaran
                </x-ui.button>
            </div>
        </div>
    </section>

    @if ($partners->isNotEmpty())
        <section class="public-section bg-etc-surface-low" data-partner-section>
            <div class="public-shell">
                <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                    <p class="public-eyebrow">Kerja Sama ETC</p>
                    <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Partner belajar dan pengembangan bahasa</h2>
                    <p class="public-subtitle mx-auto mt-4 max-w-2xl">Kolaborasi yang membantu program ETC Planet tetap dekat dengan kebutuhan sekolah, komunitas, dan dunia kerja.</p>
                </div>

                <div class="public-home-carousel public-reveal" data-public-reveal data-public-carousel>
                    <div class="public-home-carousel__viewport" data-carousel-viewport tabindex="0" aria-label="Daftar partner ETC Planet">
                        <div class="public-home-carousel__track" data-carousel-track>
                    @foreach ($partners as $partner)
                        <article class="public-home-carousel__slide public-home-partner-logo" data-carousel-slide>
                            <img src="{{ $assetUrl($partner->image, 'images/logo.png') }}" alt="Logo {{ $partner->title }}" class="public-home-partner-logo__image">
                            <h3 class="public-home-partner-logo__name">{{ $partner->title }}</h3>
                        </article>
                    @endforeach
                        </div>
                    </div>
                    <div class="public-home-carousel__controls" data-carousel-controls>
                        <x-ui.icon-button icon="heroicon-m-chevron-left" label="Partner sebelumnya" size="xl" class="public-home-carousel__arrow" data-carousel-prev />
                        <x-ui.icon-button icon="heroicon-m-chevron-right" label="Partner berikutnya" size="xl" class="public-home-carousel__arrow" data-carousel-next />
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section id="reels" class="public-section bg-etc-charcoal text-white">
        <div class="public-shell">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">Reels ETC</p>
                <h2 class="mt-3 font-heading text-4xl font-bold">Cuplikan suasana belajar</h2>
                <p class="mx-auto mt-4 max-w-2xl text-white/65">Intip kelas, event, dan tips singkat dari video pendek ETC Planet.</p>
            </div>

            @if ($reels->isNotEmpty())
                <div
                    class="public-home-carousel public-home-carousel--four public-home-carousel--dark public-reveal"
                    data-public-reveal
                    data-public-carousel
                    data-carousel-autoplay="false"
                    data-home-reels-carousel
                >
                    <div class="public-home-carousel__viewport" data-carousel-viewport tabindex="0" aria-label="Cuplikan reels ETC Planet">
                        <div class="public-home-carousel__track" data-carousel-track>
                    @foreach ($reels as $reel)
                        <a
                            href="{{ route('public.reels.index', ['reel' => $reel->getKey()]) }}"
                            class="public-home-carousel__slide public-home-reel-card public-card-dark group overflow-hidden text-left"
                            data-carousel-slide
                            data-home-reel-link
                            aria-label="Putar reel {{ $reel->title }}"
                        >
                            <div class="relative aspect-[9/14] overflow-hidden bg-black">
                                <video preload="metadata" muted playsinline poster="{{ $assetUrl($reel->thumbnail_path, 'images/pu1-img (3).jpg') }}" class="h-full w-full object-cover opacity-90">
                                    <source src="{{ $assetUrl($reel->video_path, 'videos/video1.mp4', 'video') }}" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
                                <x-ui.badge color="primary" class="absolute left-3 top-3 !bg-etc-magenta !text-white">{{ $reel->category }}</x-ui.badge>
                                <span class="absolute left-1/2 top-1/2 flex h-12 w-12 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-selector bg-etc-surface text-etc-magenta opacity-0 shadow-soft group-hover:opacity-100">
                                    <span class="material-symbols-outlined text-3xl">play_arrow</span>
                                </span>
                            </div>
                            <div class="p-4">
                                <h3 class="font-heading text-sm font-bold leading-6 text-white">{{ $reel->title }}</h3>
                                <p class="mt-3 font-heading text-xs font-bold uppercase tracking-[0.14em] text-etc-magenta">Tonton reel</p>
                            </div>
                        </a>
                    @endforeach
                        </div>
                    </div>
                    <div class="public-home-carousel__controls" data-carousel-controls>
                        <x-ui.icon-button icon="heroicon-m-chevron-left" label="Reel sebelumnya" size="xl" class="public-home-carousel__arrow public-home-carousel__arrow--dark" data-carousel-prev />
                        <x-ui.icon-button icon="heroicon-m-chevron-right" label="Reel berikutnya" size="xl" class="public-home-carousel__arrow public-home-carousel__arrow--dark" data-carousel-next />
                    </div>
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

            <div class="mt-8 text-center public-reveal" data-public-reveal>
                <x-ui.button
                    :href="route('public.reels.index')"
                    color="gray"
                    size="xl"
                    icon="heroicon-m-arrow-right"
                    icon-position="after"
                    class="public-section-action public-section-action--dark"
                >
                    Lihat Semua
                </x-ui.button>
            </div>
        </div>
    </section>

    <section class="public-testimonials" data-public-testimonials>
        <div class="public-shell">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">Cerita Siswa</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Apa Kata Mereka?</h2>
                <p class="public-subtitle mt-4">Cerita pengalaman belajar dari siswa dan orang tua yang telah bertumbuh bersama ETC Planet.</p>
            </div>

            @if ($testimonials->isNotEmpty())
                <div class="public-home-carousel public-reveal" data-public-reveal data-public-carousel>
                    <div class="public-home-carousel__viewport" data-carousel-viewport tabindex="0" aria-label="Testimoni siswa ETC Planet">
                        <div class="public-home-carousel__track" data-carousel-track>
                    @foreach ($testimonials as $testimonial)
                        @php
                            $rating = max(1, min(5, (int) ($testimonial->meta['rating'] ?? 5)));
                            $initials = collect(preg_split('/\s+/', trim($testimonial->title)))
                                ->filter()
                                ->take(2)
                                ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <article class="public-home-carousel__slide public-testimonial-card" data-carousel-slide data-testimonial-card>
                            <span class="material-symbols-outlined public-testimonial-card__quote" aria-hidden="true">format_quote</span>
                            <div class="flex items-center gap-4">
                                @if ($testimonial->image)
                                    <img src="{{ $assetUrl($testimonial->image, 'images/foto_profile.jpg') }}" alt="Foto {{ $testimonial->title }}" class="public-testimonial-card__avatar object-cover">
                                @else
                                    <span class="public-testimonial-card__avatar">{{ $initials ?: 'ETC' }}</span>
                                @endif
                                <div>
                                    <h3 class="font-heading text-base font-bold text-etc-on-surface">{{ $testimonial->title }}</h3>
                                    @if (filled($testimonial->meta['role'] ?? null))
                                        <p class="mt-1 text-xs text-etc-on-muted">{{ $testimonial->meta['role'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="public-testimonial-card__rating" aria-label="{{ $rating }} dari 5 bintang" data-testimonial-rating="{{ $rating }}">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span class="material-symbols-outlined">{{ $star <= $rating ? 'star' : 'star_outline' }}</span>
                                @endfor
                            </div>
                            <p class="relative mt-5 text-sm italic leading-7 text-etc-on-muted">"{{ $testimonial->body }}"</p>
                        </article>
                    @endforeach
                        </div>
                    </div>
                    <div class="public-home-carousel__controls" data-carousel-controls>
                        <x-ui.icon-button icon="heroicon-m-chevron-left" label="Testimoni sebelumnya" size="xl" class="public-home-carousel__arrow" data-carousel-prev />
                        <x-ui.icon-button icon="heroicon-m-chevron-right" label="Testimoni berikutnya" size="xl" class="public-home-carousel__arrow" data-carousel-next />
                    </div>
                </div>
            @else
                <div class="mt-10">
                    <x-ui.empty-state
                        heading="Testimoni belum tersedia"
                        description="Cerita siswa dan orang tua akan tampil setelah dipublikasikan."
                        icon="heroicon-o-chat-bubble-bottom-center-text"
                        contained
                    />
                </div>
            @endif
        </div>
    </section>

    <section class="public-section bg-etc-surface-low">
        <div class="public-shell">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">Team</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Pengajar profesional</h2>
                <p class="public-subtitle mx-auto mt-4 max-w-2xl">Belajar bersama instructor yang ramah, aktif memberi feedback, dan terbiasa mendampingi berbagai level siswa.</p>
            </div>

            @if ($instructors->isNotEmpty())
                <div class="public-home-carousel public-home-carousel--four public-reveal" data-public-reveal data-public-carousel>
                    <div class="public-home-carousel__viewport" data-carousel-viewport tabindex="0" aria-label="Daftar pengajar ETC Planet">
                        <div class="public-home-carousel__track text-center" data-carousel-track>
                    @foreach ($instructors as $teacher)
                        <article class="public-home-carousel__slide public-home-instructor-card" data-carousel-slide>
                            <img src="{{ $assetUrl($teacher->avatar, 'images/Ms. Debby.jpeg') }}" alt="Foto {{ $teacher->name }}" class="mx-auto h-36 w-36 rounded-full object-cover shadow-soft">
                            <h3 class="mt-5 font-heading text-lg font-bold">{{ $teacher->name }}</h3>
                            <p class="mt-1 font-heading text-sm font-bold text-etc-magenta">{{ $teacher->instructor_position }}</p>
                            <p class="mt-1 text-sm text-etc-on-muted">{{ $teacher->instructor_specialization }}</p>
                        </article>
                    @endforeach
                        </div>
                    </div>
                    <div class="public-home-carousel__controls" data-carousel-controls>
                        <x-ui.icon-button icon="heroicon-m-chevron-left" label="Pengajar sebelumnya" size="xl" class="public-home-carousel__arrow" data-carousel-prev />
                        <x-ui.icon-button icon="heroicon-m-chevron-right" label="Pengajar berikutnya" size="xl" class="public-home-carousel__arrow" data-carousel-next />
                    </div>
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

            <div class="mt-8 text-center public-reveal" data-public-reveal>
                <x-ui.button
                    :href="route('public.team.index')"
                    color="gray"
                    size="xl"
                    icon="heroicon-m-arrow-right"
                    icon-position="after"
                    class="public-section-action public-section-action--light"
                >
                    Lihat Semua
                </x-ui.button>
            </div>
        </div>
    </section>

    <section class="public-section bg-etc-surface" data-home-faq>
        <div class="public-shell-narrow">
            <div class="mx-auto max-w-2xl text-center public-reveal" data-public-reveal>
                <p class="public-eyebrow">FAQ</p>
                <h2 class="mt-3 font-heading text-4xl font-bold text-etc-on-surface">Pertanyaan yang sering ditanyakan</h2>
                <p class="public-subtitle mx-auto mt-4 max-w-2xl">Temukan jawaban singkat seputar program, jadwal, biaya, pendaftaran, dan placement test ETC Planet.</p>
            </div>

            @if ($faqs !== [])
                <div class="public-faq-list mt-12" data-public-faq>
                    @foreach ($faqs as $index => $faq)
                        <article class="public-faq-item public-reveal" data-public-reveal data-faq-item>
                            <x-ui.button
                                type="button"
                                color="gray"
                                class="public-faq-question"
                                data-faq-toggle
                                aria-expanded="false"
                                aria-controls="home-faq-answer-{{ $index }}"
                            >
                                <span>{{ $faq['question'] }}</span>
                                <span class="material-symbols-outlined public-faq-arrow" data-faq-arrow>expand_more</span>
                            </x-ui.button>

                            <div
                                id="home-faq-answer-{{ $index }}"
                                class="public-faq-answer hidden"
                                data-faq-answer
                            >
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 text-center public-reveal" data-public-reveal>
                    <x-ui.button
                        :href="route('public.faq.index')"
                        color="gray"
                        size="xl"
                        icon="heroicon-m-arrow-right"
                        icon-position="after"
                        class="public-section-action public-section-action--light"
                    >
                        Lihat Semua FAQ
                    </x-ui.button>
                </div>
            @else
                <div class="mt-10">
                    <x-ui.empty-state
                        heading="FAQ belum tersedia"
                        description="Pertanyaan dan jawaban resmi ETC Planet akan tampil setelah dipublikasikan."
                        icon="heroicon-o-question-mark-circle"
                        contained
                    />
                </div>
            @endif
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>

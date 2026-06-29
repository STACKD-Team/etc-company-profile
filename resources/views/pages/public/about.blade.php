<x-layouts.public title="Tentang ETC Planet" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="about" />
    @php
        $meta = array_replace($profile?->meta ?? [], $settings ?? []);
        $missions = collect($meta['mission'] ?? [])
            ->when(is_string($meta['mission'] ?? null), fn ($items) => collect(preg_split('/\r\n|\r|\n/', $meta['mission'], flags: PREG_SPLIT_NO_EMPTY)))
            ->filter();
        $values = collect($meta['values'] ?? [])
            ->when(is_string($meta['values'] ?? null), fn ($items) => collect(preg_split('/\r\n|\r|\n|,/', $meta['values'], flags: PREG_SPLIT_NO_EMPTY)))
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter();
        $generalInfo = $profile?->body ?: ($meta['general_info'] ?? null);
        $hasProfile = $profile || filled($generalInfo) || filled($meta['vision'] ?? null) || $missions->isNotEmpty() || $values->isNotEmpty();
        $media = app(\App\Services\PublicDiscoveryService::class);
    @endphp

    @if ($hasProfile)
        <section class="public-section bg-etc-surface">
            <div class="public-shell grid items-center gap-10 lg:grid-cols-[0.95fr_1.05fr]">
                <div class="public-reveal" data-public-reveal>
                    <p class="public-eyebrow">Tentang Kami</p>
                    <h1 class="public-title mt-4">{{ $profile?->title ?? 'Tentang ETC Planet' }}</h1>
                    @if ($generalInfo)
                        <p class="public-subtitle mt-6 whitespace-pre-line">{{ $generalInfo }}</p>
                    @endif
                </div>
                <div class="relative public-reveal" data-public-reveal>
                    <div class="absolute -right-4 -top-4 h-28 w-28 rounded-[42%_58%_49%_51%] bg-etc-surface-high"></div>
                    <img src="{{ $media->mediaUrl($profile?->image, 'images/hero-img.jpeg') }}" alt="Suasana belajar ETC Planet" class="relative aspect-[4/3] w-full rounded-card border-2 border-etc-outline-variant object-cover shadow-panel">
                </div>
            </div>
        </section>

        @if (filled($meta['vision'] ?? null) || $missions->isNotEmpty())
            <section class="public-section bg-etc-surface-low">
                <div class="public-shell grid gap-5 md:grid-cols-2">
                    @if (filled($meta['vision'] ?? null))
                        <article class="public-card p-6 public-reveal" data-public-reveal>
                            <span class="flex h-12 w-12 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                                <span class="material-symbols-outlined">visibility</span>
                            </span>
                            <h2 class="mt-5 font-heading text-2xl font-bold">Visi</h2>
                            <p class="mt-4 leading-7 text-etc-on-muted">{{ $meta['vision'] }}</p>
                        </article>
                    @endif

                    @if ($missions->isNotEmpty())
                        <article class="public-card p-6 public-reveal" data-public-reveal>
                            <span class="flex h-12 w-12 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                                <span class="material-symbols-outlined">flag</span>
                            </span>
                            <h2 class="mt-5 font-heading text-2xl font-bold">Misi</h2>
                            <ul class="mt-4 space-y-3 text-etc-on-muted">
                                @foreach ($missions as $mission)
                                    <li class="flex gap-3">
                                        <span class="material-symbols-outlined mt-0.5 text-base text-etc-magenta">check_circle</span>
                                        <span>{{ $mission }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    @endif
                </div>
            </section>
        @endif

        @if ($values->isNotEmpty())
            <section class="public-section bg-etc-surface">
                <div class="public-shell-narrow text-center public-reveal" data-public-reveal>
                    <p class="public-eyebrow">Nilai Belajar</p>
                    <h2 class="mt-3 font-heading text-4xl font-bold">Budaya kelas yang hangat dan terarah</h2>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        @foreach ($values as $value)
                            <x-ui.badge color="gray">{{ $value }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @else
        <section class="public-section bg-etc-surface">
            <div class="public-shell-narrow">
                <x-ui.empty-state
                    heading="Profil ETC Planet belum dipublikasikan"
                    description="Informasi resmi tentang ETC Planet akan tampil di halaman ini setelah tersedia."
                    icon="heroicon-o-building-office"
                    contained
                />
            </div>
        </section>
    @endif

    <x-public-discovery.page-end />
</x-layouts.public>

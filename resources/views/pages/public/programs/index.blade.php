@php
    $categoryLabels = [
        'english' => 'English',
        'mandarin' => 'Mandarin',
        'japanese' => 'Japanese',
        'test_prep' => 'Test Prep',
        'soft_skills' => 'Soft Skills',
        'other' => 'Lainnya',
    ];

    $typeLabels = [
        'regular' => 'Regular',
        'private' => 'Private',
        'one_on_one' => 'One on One',
    ];

    $targetAgeLabels = [
        'kids' => 'Kids',
        'teen' => 'Teen',
        'adult' => 'Adult',
        'university' => 'University',
        'all' => 'Semua Usia',
    ];

    $media = app(\App\Services\PublicDiscoveryService::class);
    $assetUrl = static fn (?string $path, string $fallback = 'images/pu1-img.jpg'): string => $media->mediaUrl($path, $fallback);
    $formatLabel = static fn (?string $value, array $labels): string => $labels[$value] ?? str($value ?: '-')->replace('_', ' ')->headline()->toString();
    $formatRupiah = static fn ($value): string => 'Rp '.number_format((float) $value, 0, ',', '.');
@endphp

<x-layouts.public title="Program" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="program" />
    <section class="public-section bg-etc-surface">
        <div class="public-shell">
            <div class="grid gap-6 lg:grid-cols-[1fr_280px] lg:items-end public-reveal" data-public-reveal>
                <div>
                    <p class="public-eyebrow">Program ETC Planet</p>
                    <h1 class="public-title mt-4 max-w-3xl">Pilih kelas yang paling pas untuk target belajarmu</h1>
                    <p class="public-subtitle mt-5 max-w-2xl">Bandingkan program aktif berdasarkan kategori, target usia, durasi, biaya, dan promo yang sedang berjalan.</p>
                </div>
                <x-ui.button :href="route('public.programs.index').'#program-list'" size="xl" icon="heroicon-m-arrow-down" icon-position="after">
                    Mulai Pendaftaran
                </x-ui.button>
            </div>

            <div class="mt-8 public-reveal" data-public-reveal>
                <p class="font-heading text-xs font-bold uppercase tracking-[0.16em] text-etc-on-muted">Filter kategori</p>
                <nav aria-label="Filter kategori program" class="mt-3 flex flex-wrap gap-2">
                    <x-ui.button
                        :href="route('public.programs.index')"
                        :color="$selectedCategory === '' ? 'primary' : 'gray'"
                        :outlined="$selectedCategory !== ''"
                        size="md"
                        class="!rounded-selector"
                    >
                        Semua Program
                    </x-ui.button>
                    @foreach ($categories as $category)
                        <x-ui.button
                            :href="route('public.programs.index', ['category' => $category])"
                            :color="$selectedCategory === $category ? 'primary' : 'gray'"
                            :outlined="$selectedCategory !== $category"
                            size="md"
                            class="!rounded-selector"
                        >
                            {{ $formatLabel($category, $categoryLabels) }}
                        </x-ui.button>
                    @endforeach
                </nav>
            </div>

            <div id="program-list" class="mt-8 grid scroll-mt-28 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($programs as $program)
                    @php
                        $promotion = $program->currentPromotion();
                        $discount = $promotion?->discountAmount($program->price) ?? 0;
                        $finalPrice = $promotion?->finalPrice($program->price) ?? (float) $program->price;
                        $registerUrl = \Illuminate\Support\Facades\Route::has('registrations.create')
                            ? route('registrations.create', ['program' => $program->id])
                            : route('public.contact.index', ['program' => $program->id]);
                    @endphp

                    <article class="public-program-card public-card group overflow-hidden public-reveal" data-public-reveal data-sprint1-program-card>
                        <div class="relative aspect-[16/10] overflow-hidden bg-etc-charcoal" data-program-cover>
                            <img src="{{ $assetUrl($program->thumbnail) }}" alt="Cover program {{ $program->name }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-etc-charcoal/80 via-etc-charcoal/15 to-transparent"></div>

                            <div class="absolute left-4 right-4 top-4 flex items-start justify-between gap-3">
                                <x-ui.badge color="primary" class="!bg-etc-surface !text-etc-magenta">
                                    {{ $formatLabel($program->category, $categoryLabels) }}
                                </x-ui.badge>
                                @if ($promotion)
                                    <x-ui.badge color="primary" class="!bg-etc-magenta !text-white" data-promo-badge>
                                        {{ $promotion->displayBadge() }}
                                    </x-ui.badge>
                                @endif
                            </div>

                            <div class="absolute bottom-4 left-4 right-4">
                                <p class="font-heading text-xs font-bold uppercase text-white/75">{{ $formatLabel($program->type, $typeLabels) }}</p>
                                <h2 class="mt-1 font-heading text-2xl font-bold leading-tight text-white">{{ $program->name }}</h2>
                            </div>
                        </div>

                        <div class="public-program-card__body p-5">
                            <p class="public-program-card__description">{{ $program->description ?: 'Program belajar ETC Planet yang dirancang untuk kebutuhan siswa.' }}</p>

                            <dl class="public-program-card__specs">
                                <div class="public-program-card__spec">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Durasi</dt>
                                    <dd class="mt-1 font-heading font-bold">{{ $program->duration_meetings ?? 0 }} pertemuan</dd>
                                </div>
                                <div class="public-program-card__spec">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Target</dt>
                                    <dd class="mt-1 font-heading font-bold">{{ $formatLabel($program->target_age, $targetAgeLabels) }}</dd>
                                </div>
                                <div class="public-program-card__spec">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Tipe</dt>
                                    <dd class="mt-1 font-heading font-bold">{{ $formatLabel($program->type, $typeLabels) }}</dd>
                                </div>
                                <div class="public-program-card__spec">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Kapasitas</dt>
                                    <dd class="mt-1 font-heading font-bold">Maks. {{ $program->max_students ?? 0 }} siswa</dd>
                                </div>
                            </dl>

                            <div class="public-program-card__pricing">
                                <div class="flex items-start justify-between gap-4">
                                    <span class="text-sm font-bold text-etc-on-muted">Harga program</span>
                                    <div class="public-program-card__price-stack text-right">
                                        @if ($promotion)
                                            <p class="font-heading text-sm font-bold text-etc-on-muted line-through">{{ $formatRupiah($program->price) }}</p>
                                            <p class="font-heading text-xl font-bold text-etc-magenta" data-promo-final-price>{{ $formatRupiah($finalPrice) }}</p>
                                            <p class="mt-1 text-xs font-bold text-etc-on-muted">Hemat {{ $formatRupiah($discount) }}</p>
                                        @else
                                            <span class="public-program-card__price-placeholder" aria-hidden="true">&nbsp;</span>
                                            <p class="font-heading text-xl font-bold text-etc-magenta">{{ $formatRupiah($program->price) }}</p>
                                            <span class="public-program-card__price-placeholder" aria-hidden="true">&nbsp;</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="public-program-card__registration-fee">
                                    <span class="text-etc-on-muted">Biaya pendaftaran</span>
                                    <span class="font-bold">{{ $formatRupiah($program->registration_fee) }}</span>
                                </div>
                            </div>

                            <div class="public-program-card__actions">
                                <x-ui.button :href="route('public.programs.show', $program)" color="gray" outlined size="xl">
                                    Lihat Detail
                                </x-ui.button>
                                <x-ui.button :href="$registerUrl" size="xl" icon="heroicon-m-arrow-right" icon-position="after">
                                    Daftar
                                </x-ui.button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="md:col-span-2 xl:col-span-3">
                        <x-ui.empty-state
                            heading="Program untuk kategori ini belum tersedia."
                            description="Coba kategori lain atau hubungi ETC Planet untuk rekomendasi kelas yang paling cocok."
                            icon="heroicon-o-academic-cap"
                            contained
                        />
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <x-public-discovery.page-end />
</x-layouts.public>

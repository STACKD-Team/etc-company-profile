@php
    $learningOutcomes = $detailContent['learning_outcomes'] ?? [];
    $trustBadges = $detailContent['trust_badges'] ?? [];
    $aboutHeading = $detailContent['about_heading'] ?? 'Tentang Program';
    $learningHeading = $detailContent['learning_heading'] ?? 'Yang Akan Kamu Pelajari';
    $registerUrl = \Illuminate\Support\Facades\Route::has('registrations.programs.index')
        ? route('registrations.programs.index', ['program' => $program->id])
        : route('public.contact.index', ['program' => $program->id]);
    $media = app(\App\Services\PublicDiscoveryService::class);
    $coverUrl = $media->mediaUrl($program->thumbnail, 'images/hero-img.jpeg');
    $categoryLabel = str($program->category)->replace('_', ' ')->headline()->toString();
    $typeLabel = str($program->type)->replace('_', ' ')->headline()->toString();
    $targetAgeLabel = str($program->target_age)->replace('_', ' ')->headline()->toString();
    $description = $program->description ?: 'Detail program sedang disiapkan oleh admin.';
    $scheduleDays = $featuredClass?->schedule_days ?: 'Jadwal menyusul';
    $scheduleTime = $featuredClass?->schedule_time ?: 'Waktu akan dikonfirmasi';
    $instructorName = $featuredInstructor?->full_name ?: $featuredInstructor?->name ?: 'Instructor akan dikonfirmasi';
    $instructorPosition = $featuredInstructor?->instructor_position ?: 'Instructor Utama';
    $instructorSpecialization = $featuredInstructor?->instructor_specialization ?: $categoryLabel;
    $promotion = $program->currentPromotion();
    $discount = $promotion?->discountAmount($program->price) ?? 0;
    $finalPrice = $promotion?->finalPrice($program->price) ?? (float) $program->price;
    $formatRupiah = static fn ($value): string => 'Rp '.number_format((float) $value, 0, ',', '.');
@endphp

<x-layouts.public :title="$program->name" navbar-active="program">
    <section class="relative isolate overflow-hidden bg-etc-charcoal text-white">
        <img src="{{ $coverUrl }}" alt="Cover program {{ $program->name }}" class="absolute inset-0 h-full w-full object-cover opacity-35" data-program-cover>
        <div class="absolute inset-0 bg-gradient-to-r from-etc-charcoal via-etc-charcoal/85 to-etc-charcoal/55"></div>

        <div class="public-shell relative grid min-h-[640px] items-end gap-8 py-16 lg:grid-cols-[1fr_360px]">
            <div class="public-reveal" data-public-reveal>
                <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-white/70" aria-label="Breadcrumb">
                    <x-ui.button
                        :href="route('public.home')"
                        color="gray"
                        size="sm"
                        class="!min-h-0 !rounded-none !bg-transparent !p-0 !text-white/70 !shadow-none hover:!bg-transparent hover:!text-white"
                    >
                        Beranda
                    </x-ui.button>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <x-ui.button
                        :href="route('public.programs.index')"
                        color="gray"
                        size="sm"
                        class="!min-h-0 !rounded-none !bg-transparent !p-0 !text-white/70 !shadow-none hover:!bg-transparent hover:!text-white"
                    >
                        Program
                    </x-ui.button>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <span class="font-bold text-white">{{ $program->name }}</span>
                </nav>

                <div class="mb-6 flex flex-wrap gap-2">
                    <x-ui.badge color="primary" class="!bg-etc-surface !text-etc-magenta">{{ $categoryLabel }}</x-ui.badge>
                    <x-ui.badge color="gray" class="!bg-etc-surface/10 !text-white">{{ $typeLabel }}</x-ui.badge>
                    <x-ui.badge color="gray" class="!bg-etc-surface/10 !text-white">{{ $targetAgeLabel }}</x-ui.badge>
                    @if ($promotion)
                        <x-ui.badge color="primary" class="!bg-etc-magenta !text-white" data-promo-badge>{{ $promotion->displayBadge() }}</x-ui.badge>
                    @endif
                </div>

                <h1 class="max-w-4xl font-heading text-4xl font-bold leading-tight md:text-6xl">{{ $program->name }}</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">{{ $description }}</p>

                <div class="mt-8 grid max-w-3xl grid-cols-3 overflow-hidden rounded-card border-2 border-etc-surface/15 bg-etc-surface/10">
                    <div class="border-r-2 border-etc-surface/15 p-4">
                        <p class="font-heading text-xs font-bold uppercase text-white/60">Durasi</p>
                        <p class="mt-2 font-heading text-lg font-bold">{{ $program->duration_meetings ?? 16 }}x</p>
                    </div>
                    <div class="border-r-2 border-etc-surface/15 p-4">
                        <p class="font-heading text-xs font-bold uppercase text-white/60">Kapasitas</p>
                        <p class="mt-2 font-heading text-lg font-bold">{{ $program->max_students ?? 10 }} siswa</p>
                    </div>
                    <div class="p-4">
                        <p class="font-heading text-xs font-bold uppercase text-white/60">Target</p>
                        <p class="mt-2 font-heading text-lg font-bold">{{ $targetAgeLabel }}</p>
                    </div>
                </div>
            </div>

            <aside class="public-card overflow-hidden border-t-4 border-t-etc-magenta bg-etc-surface text-etc-on-surface public-reveal" data-public-reveal data-sprint1-pricing-panel>
                <div class="p-6">
                    <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Investasi belajar</p>

                    @if ($promotion)
                        <div class="mt-5 rounded-card bg-etc-surface-container p-4">
                            <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $promotion->title }}</p>
                            @if ($promotion->description)
                                <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $promotion->description }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="mt-5">
                        @if ($promotion)
                            <p class="font-heading text-base font-bold text-etc-on-muted line-through">{{ $formatRupiah($program->price) }}</p>
                            <p class="mt-1 font-heading text-4xl font-bold text-etc-magenta" data-promo-final-price>{{ $formatRupiah($finalPrice) }}</p>
                            <p class="mt-2 text-sm font-bold text-etc-on-muted">Potongan {{ $formatRupiah($discount) }}</p>
                        @else
                            <p class="font-heading text-4xl font-bold">{{ $formatRupiah($program->price) }}</p>
                        @endif
                    </div>

                    <div class="mt-5 rounded-card bg-etc-surface-container p-4">
                        <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Biaya pendaftaran</p>
                        <p class="mt-1 font-heading text-xl font-bold">{{ $formatRupiah($program->registration_fee) }}</p>
                    </div>

                    @if ($promotion?->terms)
                        <div class="mt-4 rounded-card border-2 border-etc-outline-variant p-4" data-promo-terms>
                            <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Syarat promo</p>
                            <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $promotion->terms }}</p>
                        </div>
                    @endif

                    <div class="mt-6">
                        <x-ui.button :href="$registerUrl" size="xl" class="w-full" icon="heroicon-m-arrow-right" icon-position="after">
                            Daftar Program Ini
                        </x-ui.button>
                    </div>

                    <p class="mt-4 text-center text-xs leading-5 text-etc-on-muted">
                        Placement test dilakukan offline supaya level kelas lebih akurat.
                    </p>
                </div>
            </aside>
        </div>
    </section>

    <section class="public-section bg-etc-surface">
        <div class="public-shell grid gap-6 lg:grid-cols-[1fr_320px]">
            <div class="space-y-6">
                <article class="public-card p-6 public-reveal" data-public-reveal>
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                            <span class="material-symbols-outlined">forum</span>
                        </span>
                        <h2 class="font-heading text-2xl font-bold">{{ $aboutHeading }}</h2>
                    </div>
                    <div class="mt-5 max-w-3xl space-y-4 leading-8 text-etc-on-muted">
                        <p>{{ $description }}</p>
                        <p>Kelas dirancang untuk membantu siswa belajar lebih aktif, terarah, dan percaya diri melalui latihan yang dekat dengan kebutuhan sehari-hari.</p>
                    </div>
                </article>

                <article class="public-card p-6 public-reveal" data-public-reveal>
                    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
                        <div>
                            <p class="public-eyebrow">Learning outcomes</p>
                            <h2 class="mt-2 font-heading text-2xl font-bold">{{ $learningHeading }}</h2>
                        </div>
                        <x-ui.badge color="gray">{{ count($learningOutcomes) }} fokus belajar</x-ui.badge>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        @forelse ($learningOutcomes as $outcome)
                            <div class="flex min-h-20 items-start gap-3 rounded-card border-2 border-etc-outline-variant bg-etc-surface-container p-4">
                                <span class="material-symbols-outlined mt-0.5 text-lg text-etc-magenta">check_circle</span>
                                <p class="text-sm leading-6 text-etc-on-muted">{{ $outcome }}</p>
                            </div>
                        @empty
                            <div class="rounded-card border-2 border-etc-outline-variant bg-etc-surface-container p-4 md:col-span-2">
                                <p class="text-sm leading-6 text-etc-on-muted">Tim ETC akan menyesuaikan fokus belajar dengan level dan tujuan siswa saat konsultasi awal.</p>
                            </div>
                        @endforelse
                    </div>
                </article>

                <div class="grid gap-5 md:grid-cols-2">
                    <article class="public-card p-5 public-reveal" data-public-reveal>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="public-eyebrow">Schedule</p>
                                <h2 class="mt-2 font-heading text-2xl font-bold">Jadwal Kelas</h2>
                            </div>
                            <span class="material-symbols-outlined text-3xl text-etc-magenta">calendar_month</span>
                        </div>
                        <div class="mt-6 rounded-card bg-etc-surface-container p-4">
                            <p class="font-heading text-lg font-bold">{{ $scheduleDays }}</p>
                            <p class="mt-1 text-sm text-etc-on-muted">{{ $scheduleTime }}</p>
                            @if ($featuredClass?->room)
                                <x-ui.badge color="gray" class="mt-4">{{ $featuredClass->room }}</x-ui.badge>
                            @endif
                        </div>
                    </article>

                    <article class="public-card p-5 public-reveal" data-public-reveal>
                        <p class="public-eyebrow">Mentor</p>
                        <div class="mt-5 flex items-center gap-4">
                            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full border-2 border-etc-outline-variant bg-etc-charcoal font-heading text-2xl font-bold text-white">
                                {{ str($instructorName)->substr(0, 1)->upper() }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $instructorPosition }}</p>
                                <h2 class="mt-1 font-heading text-xl font-bold leading-snug">{{ $instructorName }}</h2>
                                <p class="mt-2 text-sm text-etc-magenta">{{ $instructorSpecialization }}</p>
                            </div>
                        </div>
                        <p class="mt-5 text-sm leading-6 text-etc-on-muted">Siswa akan dibimbing dengan latihan terstruktur, koreksi langsung, dan aktivitas komunikasi yang aktif.</p>
                    </article>
                </div>
            </div>

            <aside class="public-card h-fit p-5 public-reveal lg:sticky lg:top-28" data-public-reveal>
                <p class="public-eyebrow">Kenapa pilih kelas ini?</p>
                <ul class="mt-5 space-y-3">
                    @forelse ($trustBadges as $badge)
                        <li class="flex items-center gap-3 text-sm text-etc-on-muted">
                            <span class="material-symbols-outlined text-xl text-etc-magenta">{{ $badge['icon'] ?? 'verified' }}</span>
                            <span>{{ $badge['label'] ?? '' }}</span>
                        </li>
                    @empty
                        <li class="flex items-center gap-3 text-sm text-etc-on-muted">
                            <span class="material-symbols-outlined text-xl text-etc-magenta">verified</span>
                            <span>Kelas kecil dengan arahan instructor.</span>
                        </li>
                        <li class="flex items-center gap-3 text-sm text-etc-on-muted">
                            <span class="material-symbols-outlined text-xl text-etc-magenta">assignment</span>
                            <span>Placement test membantu penempatan level.</span>
                        </li>
                    @endforelse
                </ul>
                <div class="mt-6">
                    <x-ui.button :href="$registerUrl" color="gray" outlined size="xl" class="w-full">
                        Ambil kelas ini
                    </x-ui.button>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>

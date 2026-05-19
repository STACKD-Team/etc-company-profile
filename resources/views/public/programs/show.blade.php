@php
    $learningOutcomes = $detailContent['learning_outcomes'] ?? [];
    $trustBadges = $detailContent['trust_badges'] ?? [];
    $aboutHeading = $detailContent['about_heading'] ?? 'Tentang Program';
    $learningHeading = $detailContent['learning_heading'] ?? 'Yang Akan Kamu Pelajari';
    $registerUrl = \Illuminate\Support\Facades\Route::has('registrations.programs.index')
        ? route('registrations.programs.index', ['program' => $program->id])
        : route('public.contact.index', ['program' => $program->id]);
    $categoryLabel = str($program->category)->replace('_', ' ')->headline()->toString();
    $typeLabel = str($program->type)->replace('_', ' ')->headline()->toString();
    $targetAgeLabel = str($program->target_age)->replace('_', ' ')->headline()->toString();
    $description = $program->description ?: 'Detail program sedang disiapkan oleh admin.';
    $scheduleDays = $featuredClass?->schedule_days ?: 'Jadwal menyusul';
    $scheduleTime = $featuredClass?->schedule_time ?: 'Waktu akan dikonfirmasi';
    $instructorName = $featuredInstructor?->full_name ?: $featuredInstructor?->name ?: 'Instructor akan dikonfirmasi';
    $instructorPosition = $featuredInstructor?->instructor_position ?: 'Instructor Utama';
    $instructorSpecialization = $featuredInstructor?->instructor_specialization ?: $categoryLabel;
@endphp

<x-layouts.public :title="$program->name">
    <div class="bg-etc-surface text-etc-on-surface">
        <section class="relative isolate overflow-hidden border-b border-etc-outline-variant/60 bg-white">
            <div class="absolute inset-y-0 right-0 hidden w-[38%] bg-etc-surface-high lg:block"></div>
            <div class="absolute bottom-0 left-0 right-0 h-14 origin-bottom-left -skew-y-2 bg-etc-surface"></div>

            <div class="relative mx-auto max-w-[1200px] px-6 pb-20 pt-10 lg:px-8 lg:pb-24">
                <nav class="mb-10 flex flex-wrap items-center gap-2 text-sm text-etc-on-muted" aria-label="Breadcrumb">
                    <span>Beranda</span>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <span>Program</span>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <span class="font-bold text-etc-on-surface">{{ $program->name }}</span>
                </nav>

                <div class="grid items-end gap-10 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <div>
                        <div class="mb-7 flex flex-wrap gap-3">
                            <span class="rounded-full bg-etc-surface-container px-4 py-2 font-heading text-sm font-bold text-etc-magenta">{{ $categoryLabel }}</span>
                            <span class="rounded-full border border-etc-outline-variant bg-white px-4 py-2 font-heading text-sm font-bold text-etc-on-muted">{{ $typeLabel }}</span>
                            <span class="rounded-full border border-etc-outline-variant bg-white px-4 py-2 font-heading text-sm font-bold text-etc-on-muted">{{ $targetAgeLabel }}</span>
                        </div>

                        <h1 class="max-w-4xl font-heading text-4xl font-black leading-tight tracking-normal text-etc-on-surface md:text-6xl">
                            {{ $program->name }}
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-etc-on-muted">
                            {{ $description }}
                        </p>
                    </div>

                    <div class="grid grid-cols-3 overflow-hidden rounded-card border border-etc-outline-variant/70 bg-etc-surface-low shadow-soft">
                        <div class="border-r border-etc-outline-variant/70 p-4">
                            <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Durasi</p>
                            <p class="mt-2 font-heading text-lg font-black text-etc-on-surface">{{ $program->duration_meetings ?? 16 }}x</p>
                        </div>
                        <div class="border-r border-etc-outline-variant/70 p-4">
                            <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Kapasitas</p>
                            <p class="mt-2 font-heading text-lg font-black text-etc-on-surface">{{ $program->max_students ?? 10 }}</p>
                        </div>
                        <div class="p-4">
                            <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Kelas</p>
                            <p class="mt-2 font-heading text-lg font-black text-etc-on-surface">{{ $typeLabel }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-6 py-14 lg:px-8 lg:py-18">
            <div class="mx-auto grid max-w-[1200px] gap-8 lg:grid-cols-[minmax(0,1fr)_372px]">
                <div class="space-y-8">
                    <article class="rounded-card border border-etc-outline-variant/60 bg-white p-7 shadow-soft md:p-8">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined flex h-11 w-11 items-center justify-center rounded-full bg-etc-surface-container text-etc-magenta">forum</span>
                            <h2 class="font-heading text-2xl font-black text-etc-on-surface">{{ $aboutHeading }}</h2>
                        </div>
                        <div class="mt-6 max-w-3xl space-y-4 text-base leading-8 text-etc-on-muted">
                            <p>{{ $description }}</p>
                            <p>Kelas dirancang untuk membantu siswa belajar lebih aktif, terarah, dan percaya diri melalui latihan yang dekat dengan kebutuhan sehari-hari.</p>
                        </div>
                    </article>

                    <article class="rounded-card border border-etc-outline-variant/60 bg-white p-7 shadow-soft md:p-8">
                        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
                            <div>
                                <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Learning outcomes</p>
                                <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $learningHeading }}</h2>
                            </div>
                            <span class="inline-flex w-fit rounded-full bg-etc-surface-container px-4 py-2 font-heading text-xs font-bold text-etc-magenta">
                                {{ count($learningOutcomes) }} fokus belajar
                            </span>
                        </div>

                        <div class="mt-7 grid gap-4 md:grid-cols-2">
                            @foreach ($learningOutcomes as $outcome)
                                <div class="flex min-h-20 items-start gap-3 rounded-lg border border-etc-outline-variant/50 bg-etc-surface-low p-4">
                                    <span class="material-symbols-outlined mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-white text-lg text-etc-magenta">check</span>
                                    <p class="text-sm leading-6 text-etc-on-muted">{{ $outcome }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <div class="grid gap-6 md:grid-cols-2">
                        <article class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Schedule</p>
                                    <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">Jadwal Kelas</h2>
                                </div>
                                <span class="material-symbols-outlined flex h-12 w-12 items-center justify-center rounded-full bg-etc-surface-container text-2xl text-etc-magenta">calendar_month</span>
                            </div>
                            <div class="mt-7 rounded-lg bg-etc-surface-low p-4">
                                <p class="font-heading text-lg font-black text-etc-on-surface">{{ $scheduleDays }}</p>
                                <p class="mt-1 text-sm text-etc-on-muted">{{ $scheduleTime }}</p>
                                @if ($featuredClass?->room)
                                    <p class="mt-4 inline-flex rounded-full bg-white px-3 py-1 font-heading text-xs font-bold text-etc-on-muted">{{ $featuredClass->room }}</p>
                                @endif
                            </div>
                        </article>

                        <article class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
                            <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Mentor</p>
                            <div class="mt-5 flex items-center gap-4">
                                <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-full border-4 border-etc-surface-container bg-etc-charcoal font-heading text-2xl font-black text-white">
                                    {{ str($instructorName)->substr(0, 1)->upper() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-heading text-xs font-bold uppercase tracking-normal text-etc-on-muted">{{ $instructorPosition }}</p>
                                    <h2 class="mt-1 font-heading text-xl font-black leading-snug text-etc-on-surface">{{ $instructorName }}</h2>
                                    <p class="mt-2 text-sm text-etc-magenta">{{ $instructorSpecialization }}</p>
                                </div>
                            </div>
                            <p class="mt-5 text-sm leading-6 text-etc-on-muted">
                                Siswa akan dibimbing dengan latihan terstruktur, koreksi langsung, dan aktivitas komunikasi yang aktif.
                            </p>
                        </article>
                    </div>
                </div>

                <aside class="h-fit overflow-hidden rounded-card border border-etc-outline-variant/60 border-t-4 border-t-etc-magenta bg-white shadow-panel lg:sticky lg:top-28">
                    <div class="p-7">
                        <p class="font-heading text-sm font-bold uppercase text-etc-magenta">Investasi belajar</p>
                        <div class="mt-5 flex items-end gap-2">
                            <span class="pb-2 font-heading text-xl font-black text-etc-on-surface">Rp</span>
                            <p class="font-heading text-4xl font-black text-etc-on-surface">
                                {{ number_format((float) $program->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="mt-5 rounded-lg bg-etc-surface-low p-4">
                            <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Biaya pendaftaran</p>
                            <p class="mt-1 font-heading text-xl font-black text-etc-magenta">Rp {{ number_format((float) $program->registration_fee, 0, ',', '.') }}</p>
                        </div>

                        <a href="{{ $registerUrl }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white shadow-soft transition hover:bg-etc-primary">
                            Daftar Program Ini
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </a>

                        <p class="mt-4 text-center text-xs leading-5 text-etc-on-muted">
                            Placement test tetap dilakukan offline dan akan dikonfirmasi setelah pembayaran diverifikasi admin.
                        </p>
                    </div>

                    <div class="border-t border-etc-outline-variant/50 bg-etc-surface-container px-6 py-5">
                        <ul class="space-y-3">
                            @foreach ($trustBadges as $badge)
                                <li class="flex items-center gap-3 text-sm text-etc-on-muted">
                                    <span class="material-symbols-outlined text-xl text-etc-magenta">{{ $badge['icon'] ?? 'verified' }}</span>
                                    <span>{{ $badge['label'] ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>
        </section>
    </div>
</x-layouts.public>

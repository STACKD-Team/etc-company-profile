@php
    $displayName = $instructor->full_name ?? $instructor->name;
    $statCards = [
        ['label' => 'Siswa Diajar', 'value' => $stats['students'], 'icon' => 'groups'],
        ['label' => 'Kelas Berjalan', 'value' => $stats['ongoing'], 'icon' => 'play_circle'],
        ['label' => 'Kelas Mendatang', 'value' => $stats['upcoming'], 'icon' => 'event_upcoming'],
        ['label' => 'Kelas Selesai', 'value' => $stats['completed'], 'icon' => 'task_alt'],
    ];
@endphp

<x-layouts.dashboard title="Dashboard Instructor" area="instructor" active="dashboard" :user="$instructor">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-box border-2 border-etc-charcoal bg-etc-charcoal p-6 text-etc-surface shadow-panel md:p-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div>
                    <p class="font-heading text-xs font-bold uppercase tracking-widest text-etc-magenta">Ruang Mengajar</p>
                    <h2 class="mt-3 font-heading text-3xl font-black">Halo, {{ $displayName }}</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-etc-surface/70">
                        Pantau kelas dan selesaikan assessment siswa dari satu tempat.
                    </p>
                </div>
                <x-ui.button :href="route('instructor.report-cards.index')" icon="heroicon-m-pencil-square">
                    Buka Assessment
                </x-ui.button>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan kelas instructor">
            @foreach ($statCards as $card)
                <article class="rounded-box border-2 border-etc-outline-variant bg-etc-surface p-5 shadow-soft">
                    <span class="flex h-12 w-12 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                        <span class="material-symbols-outlined">{{ $card['icon'] }}</span>
                    </span>
                    <p class="mt-5 text-sm font-semibold text-etc-on-muted">{{ $card['label'] }}</p>
                    <strong class="mt-1 block font-heading text-3xl font-black text-etc-on-surface">{{ $card['value'] }}</strong>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(320px,0.75fr)]">
            <x-ui.panel heading="Kelas Prioritas" description="Kelas berjalan dan mendatang muncul lebih dahulu." icon="heroicon-o-academic-cap">
                @if ($classes->isEmpty())
                    <x-ui.empty-state
                        heading="Belum ada kelas"
                        description="Kelas yang ditugaskan admin akan tampil di sini."
                        icon="heroicon-o-academic-cap"
                    />
                @else
                    <div class="divide-y-2 divide-etc-outline-variant">
                        @foreach ($classes as $class)
                            <a href="{{ route('instructor.classes.show', $class) }}" class="group flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate font-heading font-bold text-etc-on-surface group-hover:text-etc-magenta">{{ $class->name }}</h3>
                                        <x-ui.badge :status="$class->status" />
                                    </div>
                                    <p class="mt-1 text-sm text-etc-on-muted">
                                        {{ $class->program?->name ?? 'Program belum ditentukan' }}
                                        <span aria-hidden="true">·</span>
                                        {{ $class->enrollments_count }} siswa
                                    </p>
                                </div>
                                <div class="shrink-0 text-sm text-etc-on-muted sm:text-right">
                                    <p>{{ $class->schedule_days ?? 'Hari belum diatur' }}</p>
                                    <p class="mt-1 font-semibold text-etc-on-surface">{{ $class->schedule_time ?? 'Jam belum diatur' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <x-slot:footer>
                        <x-ui.button :href="route('instructor.classes.index')" outlined>
                            Lihat Semua Kelas
                        </x-ui.button>
                    </x-slot:footer>
                @endif
            </x-ui.panel>

            <x-ui.panel
                heading="Assessment Perlu Dilengkapi"
                :description="$stats['incomplete_assessments'].' assessment belum lengkap.'"
                icon="heroicon-o-clipboard-document-check"
            >
                @if ($assessments->isEmpty())
                    <x-ui.empty-state
                        heading="Assessment sudah rapi"
                        description="Tidak ada assessment yang tertunda."
                        icon="heroicon-o-check-circle"
                        icon-color="success"
                    />
                @else
                    <div class="space-y-3">
                        @foreach ($assessments as $enrollment)
                            @php($reportCard = $enrollment->reportCard)
                            <div class="rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-heading text-sm font-bold text-etc-on-surface">
                                            {{ $enrollment->user?->full_name ?? $enrollment->user?->name ?? '-' }}
                                        </p>
                                        <p class="mt-1 truncate text-xs text-etc-on-muted">{{ $enrollment->courseClass?->name ?? '-' }}</p>
                                    </div>
                                    <x-ui.badge :status="$enrollment->assessment_state">
                                        {{ $enrollment->assessment_state === 'not_started' ? 'Belum mulai' : 'Belum lengkap' }}
                                    </x-ui.badge>
                                </div>
                                <div class="mt-4">
                                    <x-ui.button
                                        :href="$reportCard ? route('instructor.report-cards.edit', $reportCard) : route('instructor.report-cards.create', $enrollment)"
                                        size="sm"
                                        outlined
                                    >
                                        {{ $reportCard ? 'Lanjutkan' : 'Mulai' }}
                                    </x-ui.button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.panel>
        </section>
    </div>
</x-layouts.dashboard>

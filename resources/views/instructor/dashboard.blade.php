@php
    $displayName = $instructor->full_name ?? $instructor->name;
    $statCards = [
        ['label' => 'Siswa Diajar', 'value' => $stats['students'], 'icon' => 'heroicon-o-user-group'],
        ['label' => 'Kelas Berjalan', 'value' => $stats['ongoing'], 'icon' => 'heroicon-o-play-circle'],
        ['label' => 'Kelas Mendatang', 'value' => $stats['upcoming'], 'icon' => 'heroicon-o-calendar-days'],
        ['label' => 'Kelas Selesai', 'value' => $stats['completed'], 'icon' => 'heroicon-o-check-circle'],
        ['label' => 'Perlu Dinilai', 'value' => $stats['incomplete_assessments'], 'icon' => 'heroicon-o-clipboard-document-check'],
    ];
@endphp

<x-layouts.dashboard
    :title="'Halo, '.$displayName"
    description="Pantau kelas yang kamu ajar dan lanjutkan assessment siswa dari satu ruang kerja."
    area="instructor"
    active="dashboard"
    :user="$instructor"
>
    <x-slot:eyebrow>Instructor Workspace</x-slot:eyebrow>
    <x-slot:headerActions>
        <x-ui.button :href="route('instructor.report-cards.index')" icon="heroicon-m-pencil-square">
            Buka Assessment
        </x-ui.button>
    </x-slot:headerActions>

    <div class="space-y-6" data-instructor-dashboard>
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="Ringkasan kelas instructor">
            @foreach ($statCards as $card)
                <x-ui.panel
                    compact
                    class="motion-safe:transition motion-safe:duration-200 motion-safe:hover:-translate-y-0.5 motion-safe:hover:shadow-panel"
                    data-instructor-stat="{{ $loop->iteration }}"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-etc-on-muted">{{ $card['label'] }}</p>
                            <strong class="mt-2 block font-heading text-3xl font-bold text-etc-on-surface">{{ $card['value'] }}</strong>
                        </div>
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-selector bg-etc-surface-container text-etc-magenta">
                            {{ \Filament\Support\generate_icon_html($card['icon'], attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'h-5 w-5'])) }}
                        </span>
                    </div>
                </x-ui.panel>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(320px,0.75fr)]">
            <x-ui.panel
                heading="Kelas Prioritas"
                description="Kelas berjalan dan mendatang muncul lebih dahulu."
                icon="heroicon-o-academic-cap"
                data-instructor-priority-classes
            >
                <x-slot:actions>
                    <x-ui.button :href="route('instructor.classes.index')" size="sm" outlined>
                        Semua Kelas
                    </x-ui.button>
                </x-slot:actions>

                @if ($classes->isEmpty())
                    <x-ui.empty-state
                        heading="Belum ada kelas"
                        description="Kelas yang ditugaskan admin akan tampil di sini."
                        icon="heroicon-o-academic-cap"
                    />
                @else
                    <div class="divide-y-2 divide-etc-outline-variant">
                        @foreach ($classes as $class)
                            <a
                                href="{{ route('instructor.classes.show', $class) }}"
                                class="group flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between motion-safe:transition motion-safe:duration-200"
                            >
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate font-heading font-bold text-etc-on-surface group-hover:text-etc-magenta">{{ $class->name }}</h3>
                                        <x-ui.badge :status="$class->status" />
                                    </div>
                                    <p class="mt-1 text-sm text-etc-on-muted">
                                        {{ $class->program?->name ?? 'Program belum ditentukan' }}
                                        <span aria-hidden="true">&middot;</span>
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
                @endif
            </x-ui.panel>

            <x-ui.panel
                heading="Assessment Perlu Dilengkapi"
                :description="$stats['incomplete_assessments'].' assessment belum lengkap.'"
                icon="heroicon-o-clipboard-document-check"
                data-instructor-assessments
            >
                <x-slot:actions>
                    <x-ui.button :href="route('instructor.report-cards.index')" size="sm" outlined>
                        Lihat Semua
                    </x-ui.button>
                </x-slot:actions>

                @if ($assessments->isEmpty())
                    <x-ui.empty-state
                        heading="Assessment sudah rapi"
                        description="Tidak ada assessment yang tertunda."
                        icon="heroicon-o-check-circle"
                        icon-color="success"
                    />
                @else
                    <div class="divide-y-2 divide-etc-outline-variant">
                        @foreach ($assessments as $enrollment)
                            @php($reportCard = $enrollment->reportCard)
                            <div class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate font-heading text-sm font-bold text-etc-on-surface">
                                            {{ $enrollment->user?->full_name ?? $enrollment->user?->name ?? '-' }}
                                        </p>
                                        <x-ui.badge :status="$enrollment->assessment_state" size="sm">
                                            {{ $enrollment->assessment_state === 'not_started' ? 'Belum mulai' : 'Belum lengkap' }}
                                        </x-ui.badge>
                                    </div>
                                    <p class="mt-1 truncate text-xs text-etc-on-muted">{{ $enrollment->courseClass?->name ?? '-' }}</p>
                                </div>
                                <x-ui.button
                                    :href="$reportCard ? route('instructor.report-cards.edit', $reportCard) : route('instructor.report-cards.create', $enrollment)"
                                    size="sm"
                                    outlined
                                >
                                    {{ $reportCard ? 'Lanjutkan' : 'Mulai' }}
                                </x-ui.button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.panel>
        </section>
    </div>
</x-layouts.dashboard>

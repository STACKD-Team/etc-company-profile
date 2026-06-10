@php
    $statusLabels = [
        'active' => 'Sedang Berjalan',
        'completed' => 'Selesai',
        'dropped' => 'Berhenti',
    ];
@endphp

<x-layouts.dashboard title="Riwayat Pembelajaran" area="student" active="classes" :user="$student">
    <x-ui.panel heading="Riwayat Pembelajaran" description="Semua kelas yang pernah dan sedang diikuti siswa.">
        <div class="space-y-4">
            @forelse ($enrollments as $enrollment)
                @php
                    $class = $enrollment->courseClass;
                    $reportCard = $enrollment->reportCard;
                    $publishedReport = $reportCard?->is_published ? $reportCard : null;
                @endphp
                <article class="rounded-card border border-etc-outline-variant/70 bg-white p-5">
                    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_220px] xl:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.badge :status="$enrollment->status">{{ $statusLabels[$enrollment->status] ?? str($enrollment->status)->headline() }}</x-ui.badge>
                                @if ($publishedReport)
                                    <x-ui.badge status="published">Rapor Published</x-ui.badge>
                                @else
                                    <x-ui.badge status="unpublished">Rapor Belum Terbit</x-ui.badge>
                                @endif
                            </div>

                            <h2 class="mt-3 font-heading text-xl font-bold text-etc-on-surface">{{ $class?->program?->name ?? 'Program ETC Planet' }} - {{ $class?->name ?? 'Kelas ETC' }}</h2>

                            <dl class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-card bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Instruktur</dt>
                                    <dd class="mt-1 text-sm font-semibold text-etc-on-surface">{{ $class?->instructor?->full_name ?? $class?->instructor?->name ?? 'Belum ditentukan' }}</dd>
                                </div>
                                <div class="rounded-card bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Mulai</dt>
                                    <dd class="mt-1 text-sm font-semibold text-etc-on-surface">{{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }}</dd>
                                </div>
                                <div class="rounded-card bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Selesai</dt>
                                    <dd class="mt-1 text-sm font-semibold text-etc-on-surface">{{ $enrollment->completed_at?->format('d M Y') ?? 'Masih berjalan' }}</dd>
                                </div>
                                <div class="rounded-card bg-etc-surface-low p-3">
                                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Jadwal</dt>
                                    <dd class="mt-1 text-sm font-semibold text-etc-on-surface">{{ $class?->schedule_days ?? '-' }} {{ $class?->schedule_time ?? '' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex flex-col gap-3">
                            @if ($class)
                                <x-ui.button :href="route('student.classes.show', $class)" outlined icon="heroicon-m-eye">Detail Kelas</x-ui.button>
                            @endif
                            @if ($publishedReport)
                                <x-ui.button :href="route('student.report-cards.show', $publishedReport)" icon="heroicon-m-document-text">Lihat Rapor</x-ui.button>
                            @else
                                <p class="rounded-card bg-etc-surface-low p-3 text-sm text-etc-on-muted">Rapor akan tampil setelah dipublikasikan admin.</p>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <x-ui.empty-state
                    heading="Belum ada riwayat pembelajaran"
                    description="Riwayat akan tampil setelah siswa masuk kelas."
                    icon="heroicon-o-clock"
                />
            @endforelse
        </div>
    </x-ui.panel>
</x-layouts.dashboard>

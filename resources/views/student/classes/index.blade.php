@php
    $statusLabels = [
        'active' => 'Sedang Berjalan',
        'completed' => 'Selesai',
        'dropped' => 'Berhenti',
    ];

    $groupedEnrollments = collect([
        'active' => $enrollments->where('status', 'active'),
        'completed' => $enrollments->where('status', 'completed'),
        'dropped' => $enrollments->where('status', 'dropped'),
    ]);
@endphp

<x-layouts.dashboard title="Kelas Saya" area="student" active="classes" :user="$student">
    <div class="space-y-6">
        <x-ui.panel heading="Kelas Saya" description="Kelas dipisahkan berdasarkan status agar mudah dipantau siswa dan orang tua.">
            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($groupedEnrollments as $status => $items)
                    <div class="rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4 shadow-soft">
                        <p class="text-xs font-bold uppercase text-etc-on-muted">{{ $statusLabels[$status] }}</p>
                        <p class="mt-2 font-heading text-3xl font-bold text-etc-on-surface">{{ $items->count() }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.panel>

        @foreach ($groupedEnrollments as $status => $items)
            <x-ui.panel :heading="$statusLabels[$status]" :description="$status === 'active' ? 'Kelas yang sedang diikuti.' : 'Riwayat kelas dengan status '.$statusLabels[$status].'.'">
                <div class="grid gap-4">
                    @forelse ($items as $enrollment)
                        @php($class = $enrollment->courseClass)
                        <article class="student-reveal rounded-box border-2 border-etc-outline-variant bg-etc-surface p-5 shadow-soft" data-reveal-card>
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-ui.badge :status="$enrollment->status">{{ $statusLabels[$enrollment->status] ?? str($enrollment->status)->headline() }}</x-ui.badge>
                                        @if ($enrollment->reportCard?->is_published)
                                            <x-ui.badge status="published">Rapor Terbit</x-ui.badge>
                                        @endif
                                    </div>
                                    <h2 class="mt-3 truncate font-heading text-xl font-bold text-etc-on-surface">{{ $class?->program?->name }} - {{ $class?->name }}</h2>
                                    <p class="mt-2 text-sm text-etc-on-muted">
                                        {{ $class?->schedule_days ?? 'Jadwal belum ditentukan' }}
                                        @if ($class?->schedule_time)
                                            <span aria-hidden="true">&bull;</span> {{ $class->schedule_time }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-sm text-etc-on-muted">Instruktur: {{ $class?->instructor?->full_name ?? $class?->instructor?->name ?? 'Belum ditentukan' }}</p>
                                </div>
                                <div class="flex flex-wrap gap-3 md:justify-end">
                                    @if ($class)
                                        <x-ui.button :href="route('student.classes.show', $class)" outlined icon="heroicon-m-eye">Detail</x-ui.button>
                                    @endif
                                    @if ($enrollment->reportCard?->is_published)
                                        <x-ui.button :href="route('student.report-cards.show', $enrollment->reportCard)" icon="heroicon-m-document-text">Rapor</x-ui.button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <x-ui.empty-state
                            :heading="'Belum ada kelas '.$statusLabels[$status]"
                            description="Data akan tampil setelah admin memperbarui enrollment siswa."
                            icon="heroicon-o-academic-cap"
                        />
                    @endforelse
                </div>
            </x-ui.panel>
        @endforeach
    </div>
</x-layouts.dashboard>

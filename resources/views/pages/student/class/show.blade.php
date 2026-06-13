@php
    $statusLabels = [
        'active' => 'Sedang Berjalan',
        'completed' => 'Selesai',
        'dropped' => 'Berhenti',
    ];

    $classTitle = trim(($class->program?->name ? $class->program->name.' - ' : '').$class->name);
@endphp

<x-layouts.dashboard title="Detail Kelas" area="student" active="classes" :user="$student">
    <x-ui.resource-header
        title="Detail Kelas"
        :subtitle="$classTitle"
        :back-url="route('student.classes.index')"
    >
        <x-slot name="status">
            <x-ui.badge :status="$enrollment->status">
                {{ $statusLabels[$enrollment->status] ?? str($enrollment->status)->headline() }}
            </x-ui.badge>
            @if ($enrollment->reportCard?->is_published)
                <x-ui.badge status="published">Rapor Terbit</x-ui.badge>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-ui.button :href="route('student.learning-history.index')" icon="heroicon-m-clock">
                Riwayat Belajar
            </x-ui.button>
            @if ($enrollment->reportCard?->is_published)
                <x-ui.button :href="route('student.report-cards.show', $enrollment->reportCard)" icon="heroicon-m-document-text">
                    Lihat Rapor
                </x-ui.button>
            @endif
        </x-slot>
    </x-ui.resource-header>

    <div class="space-y-6">
        <x-ui.detail-card heading="Ringkasan Kelas" description="Informasi kelas yang terhubung dengan enrollment siswa.">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_260px]">
                <div>
                    <h2 class="font-heading text-2xl font-black text-etc-on-surface">{{ $classTitle }}</h2>
                    <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $class->program?->description ?? 'Detail program akan diperbarui oleh admin.' }}</p>
                </div>
                <div class="rounded-box bg-etc-surface-container p-4 shadow-soft">
                    <p class="text-xs font-bold uppercase text-etc-on-muted">Periode belajar</p>
                    <p class="mt-2 font-heading text-sm font-bold text-etc-on-surface">
                        {{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }} sampai {{ $enrollment->completed_at?->format('d M Y') ?? 'sekarang' }}
                    </p>
                </div>
            </div>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Jadwal dan Pengajar" description="Detail operasional kelas.">
            <x-ui.description-list columns="4">
                <x-ui.description-item
                    label="Instruktur"
                    :value="$class->instructor?->full_name ?? $class->instructor?->name"
                />
                <x-ui.description-item
                    label="Jadwal"
                    :value="trim(($class->schedule_days ?? '-').' '.($class->schedule_time ?? ''))"
                />
                <x-ui.description-item
                    label="Ruangan"
                    :value="$class->room_label"
                />
                <x-ui.description-item
                    label="Status Kelas"
                    :value="str($class->status ?? '-')->headline()->toString()"
                />
                <x-ui.description-item
                    label="Tanggal Mulai"
                    :value="$class->start_date?->format('d M Y')"
                />
                <x-ui.description-item
                    label="Tanggal Selesai"
                    :value="$class->end_date?->format('d M Y')"
                />
                <x-ui.description-item
                    label="Status Enrollment"
                    :value="$statusLabels[$enrollment->status] ?? str($enrollment->status)->headline()->toString()"
                />
                <x-ui.description-item label="Rapor">
                    @if ($enrollment->reportCard?->is_published)
                        <x-ui.badge status="published">Rapor Terbit</x-ui.badge>
                    @else
                        <x-ui.badge status="unpublished">Belum Terbit</x-ui.badge>
                    @endif
                </x-ui.description-item>
            </x-ui.description-list>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>

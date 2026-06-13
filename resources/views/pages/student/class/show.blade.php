@php
    $statusLabels = [
        'active' => 'Sedang Berjalan',
        'completed' => 'Selesai',
        'dropped' => 'Berhenti',
    ];
@endphp

<x-layouts.dashboard title="Detail Kelas" area="student" active="classes" :user="$student">
    <div class="space-y-6">
        <x-ui.panel heading="Detail Kelas" description="Informasi kelas yang terhubung dengan enrollment siswa.">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_260px]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.badge :status="$enrollment->status">{{ $statusLabels[$enrollment->status] ?? str($enrollment->status)->headline() }}</x-ui.badge>
                        @if ($enrollment->reportCard?->is_published)
                            <x-ui.badge status="published">Rapor Terbit</x-ui.badge>
                        @endif
                    </div>
                    <h2 class="mt-4 font-heading text-2xl font-black text-etc-on-surface">{{ $class->program?->name }} - {{ $class->name }}</h2>
                    <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $class->program?->description ?? 'Detail program akan diperbarui oleh admin.' }}</p>
                </div>
                <div class="rounded-box bg-etc-surface-container p-4 shadow-soft">
                    <p class="text-xs font-bold uppercase text-etc-on-muted">Periode belajar</p>
                    <p class="mt-2 font-heading text-sm font-bold text-etc-on-surface">{{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }} sampai {{ $enrollment->completed_at?->format('d M Y') ?? 'sekarang' }}</p>
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Jadwal dan Pengajar" description="Detail operasional kelas.">
            <dl class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-box bg-etc-surface-container p-4">
                    <dt class="text-sm font-bold text-etc-on-muted">Instruktur</dt>
                    <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ $class->instructor?->full_name ?? $class->instructor?->name ?? '-' }}</dd>
                </div>
                <div class="rounded-box bg-etc-surface-container p-4">
                    <dt class="text-sm font-bold text-etc-on-muted">Jadwal</dt>
                    <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ $class->schedule_days ?? '-' }} {{ $class->schedule_time ?? '' }}</dd>
                </div>
                <div class="rounded-box bg-etc-surface-container p-4">
                    <dt class="text-sm font-bold text-etc-on-muted">Ruangan</dt>
                    <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ $class->room_label ?? '-' }}</dd>
                </div>
                <div class="rounded-box bg-etc-surface-container p-4">
                    <dt class="text-sm font-bold text-etc-on-muted">Status Kelas</dt>
                    <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ str($class->status ?? '-')->headline() }}</dd>
                </div>
            </dl>
        </x-ui.panel>

        <div class="flex flex-wrap gap-3">
            <x-ui.button :href="route('student.classes.index')" outlined icon="heroicon-m-arrow-left">Kembali</x-ui.button>
            <x-ui.button :href="route('student.learning-history.index')" icon="heroicon-m-clock">Riwayat Belajar</x-ui.button>
            @if ($enrollment->reportCard?->is_published)
                <x-ui.button :href="route('student.report-cards.show', $enrollment->reportCard)" icon="heroicon-m-document-text">Lihat Rapor</x-ui.button>
            @endif
        </div>
    </div>
</x-layouts.dashboard>

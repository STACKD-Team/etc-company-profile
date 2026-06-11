@php
    $displayName = $student->full_name ?? $student->name;
    $courseName = $activeProgram?->name ?? 'Belum ada kelas aktif';
    $className = $activeCourseClass?->name ? "{$activeProgram?->name} - {$activeCourseClass->name}" : $courseName;
    $instructor = $activeCourseClass?->instructor;
    $instructorName = $instructor?->full_name ?? $instructor?->name ?? 'Instruktur belum ditentukan';
    $schedule = $activeCourseClass?->schedule_days && $activeCourseClass?->schedule_time
        ? $activeCourseClass->schedule_days.', '.$activeCourseClass->schedule_time
        : 'Jadwal belum ditentukan';

    $paymentLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Lunas',
        'placement_test' => 'Menunggu Placement Test',
        'enrolled' => 'Aktif Belajar',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
        'waiting_payment' => 'Menunggu Pembayaran',
        'expired' => 'Kedaluwarsa',
        'failed' => 'Gagal',
    ];

    $paymentStatus = $latestPayment?->status;
    $paymentLabel = $paymentStatus ? ($paymentLabels[$paymentStatus] ?? str($paymentStatus)->replace('_', ' ')->headline()) : 'Belum ada pembayaran';
    $paymentAmount = $latestPayment?->payment_amount ? 'Rp '.number_format((float) $latestPayment->payment_amount, 0, ',', '.') : '-';
    $latestReportClass = $latestReport?->enrollment?->courseClass;

    $statCards = [
        ['label' => 'Kelas Aktif', 'value' => $stats['active_classes'], 'icon' => 'stat-class'],
        ['label' => 'Total Pertemuan', 'value' => $stats['total_meetings'], 'icon' => 'stat-meeting'],
        ['label' => 'Nilai Terakhir', 'value' => $stats['average_grade'], 'icon' => 'stat-grade'],
        ['label' => 'Rapor Terbit', 'value' => $stats['certificates'], 'icon' => 'stat-certificate'],
    ];
@endphp

<x-layouts.dashboard title="Dashboard Siswa" area="student" active="dashboard" :user="$student">
    <div data-student-dashboard-page class="space-y-6">
        <section class="overflow-hidden rounded-box bg-etc-charcoal p-6 text-etc-surface shadow-panel md:p-8">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px] xl:items-end">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-surface/60">Student Portal</p>
                    <h2 class="mt-3 font-heading text-3xl font-bold md:text-4xl">Halo, {{ $displayName }}!</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-etc-surface/75">Pantau kelas, pembayaran, riwayat belajar, dan rapor dari satu tempat yang ringkas.</p>
                </div>
                <div class="rounded-box border-2 border-etc-surface/15 bg-etc-surface/10 p-4 shadow-soft">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase text-etc-surface/60">Status hari ini</p>
                            <p class="mt-2 font-heading text-lg font-bold">{{ $currentEnrollment ? 'Kelas aktif tersedia' : 'Menunggu penempatan kelas' }}</p>
                        </div>
                        <x-ui.badge :status="$currentEnrollment?->status ?? 'waiting_payment'" size="sm">
                            {{ $currentEnrollment ? str($currentEnrollment->status)->headline() : 'Belum Aktif' }}
                        </x-ui.badge>
                    </div>
                    <p class="mt-2 text-sm leading-6 text-etc-surface/70">{{ $currentEnrollment ? $className : 'Admin akan memperbarui dashboard setelah proses pendaftaran selesai.' }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan progres belajar">
            @foreach ($statCards as $card)
                <article class="student-reveal rounded-box border-2 border-etc-outline-variant bg-etc-surface p-5 shadow-soft" data-reveal-card>
                    <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-selector bg-etc-surface-container">
                        <x-ui.icon :name="$card['icon']" class="h-6 w-6" />
                    </span>
                    <p class="text-sm text-etc-on-muted">{{ $card['label'] }}</p>
                    <strong class="mt-2 block font-heading text-3xl font-bold text-etc-on-surface" data-stat-value>{{ $card['value'] }}</strong>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
            <div class="space-y-6">
                <x-ui.panel heading="Kelas Berlangsung" description="Kelas aktif dan progres belajar terbaru.">
                    @if ($currentEnrollment)
                        <div class="grid gap-5 lg:grid-cols-[260px_minmax(0,1fr)]">
                            <div class="relative min-h-56 overflow-hidden rounded-box bg-etc-surface-container shadow-soft">
                                <img src="{{ asset('images/foto_english_student.jpg') }}" alt="Siswa belajar bahasa" class="h-full w-full object-cover">
                                <div class="absolute left-4 top-4">
                                    <x-ui.badge :status="$currentEnrollment->status">{{ str($currentEnrollment->status)->headline() }}</x-ui.badge>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-heading text-2xl font-bold text-etc-on-surface">{{ $className }}</h3>
                                <p class="mt-3 text-sm leading-6 text-etc-on-muted">{{ $activeProgram?->description ?? 'Detail kelas akan diperbarui oleh admin.' }}</p>

                                <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-box bg-etc-surface-container p-4">
                                        <dt class="text-xs font-bold uppercase text-etc-on-muted">Instruktur</dt>
                                        <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ $instructorName }}</dd>
                                    </div>
                                    <div class="rounded-box bg-etc-surface-container p-4">
                                        <dt class="text-xs font-bold uppercase text-etc-on-muted">Jadwal</dt>
                                        <dd class="mt-1 font-heading text-sm font-bold text-etc-on-surface">{{ $schedule }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-6">
                                    <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                                        <span class="text-etc-on-muted">Progress: Pertemuan {{ $courseProgress['completed'] }}/{{ $courseProgress['total'] }}</span>
                                        <strong class="font-heading text-etc-magenta">{{ $courseProgress['percent'] }}%</strong>
                                    </div>
                                    <div class="h-3 overflow-hidden rounded-full bg-etc-surface-container">
                                        <span class="student-progress-bar block h-full rounded-full bg-etc-magenta" data-progress-bar data-progress-target="{{ $courseProgress['percent'] }}"></span>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    <x-ui.button :href="route('student.classes.show', $activeCourseClass)" icon="heroicon-m-eye">
                                        Detail Kelas
                                    </x-ui.button>
                                    <x-ui.button :href="route('student.learning-history.index')" outlined icon="heroicon-m-clock">
                                        Riwayat Belajar
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>
                    @else
                        <x-ui.empty-state
                            heading="Belum ada kelas aktif"
                            description="Kelas akan tampil setelah pembayaran dan penempatan kelas selesai diproses."
                            icon="heroicon-o-academic-cap"
                        >
                            <x-ui.button :href="route('student.help.index')" outlined>Bantuan penempatan</x-ui.button>
                        </x-ui.empty-state>
                    @endif
                </x-ui.panel>

                <x-ui.panel heading="Riwayat Belajar Ringkas" description="Ringkasan kelas yang pernah dan sedang diikuti.">
                    <div class="space-y-3">
                        @forelse ($recentLearningHistory as $enrollment)
                            @php
                                $historyClass = $enrollment->courseClass;
                                $historyReport = $enrollment->reportCard;
                            @endphp
                            <article class="student-reveal flex flex-col gap-3 rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4 shadow-soft sm:flex-row sm:items-center sm:justify-between" data-reveal-card>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-ui.badge :status="$enrollment->status">{{ str($enrollment->status)->headline() }}</x-ui.badge>
                                        @if ($historyReport?->is_published)
                                            <x-ui.badge status="published">Rapor Terbit</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="mt-2 truncate font-heading text-sm font-bold text-etc-on-surface">{{ $historyClass?->program?->name }} - {{ $historyClass?->name }}</p>
                                    <p class="mt-1 text-xs text-etc-on-muted">{{ $enrollment->enrolled_at?->format('d M Y') ?? '-' }} sampai {{ $enrollment->completed_at?->format('d M Y') ?? 'sekarang' }}</p>
                                </div>
                                <x-ui.button :href="route('student.learning-history.index')" outlined size="sm">Lihat</x-ui.button>
                            </article>
                        @empty
                            <x-ui.empty-state heading="Belum ada riwayat belajar" description="Riwayat akan tampil setelah siswa masuk kelas." icon="heroicon-o-clock" />
                        @endforelse
                    </div>
                </x-ui.panel>
            </div>

            <aside class="space-y-6">
                <x-ui.panel heading="Pembayaran Terakhir" description="Status pembayaran terbaru.">
                    @if ($latestPayment)
                        <div class="space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-heading text-sm font-bold text-etc-on-surface">{{ $latestPayment->registration_code }}</p>
                                    <p class="mt-1 text-sm text-etc-on-muted">{{ $latestPayment->program?->name ?? 'Program ETC Planet' }}</p>
                                </div>
                                <x-ui.badge :status="$latestPayment->status">{{ $paymentLabel }}</x-ui.badge>
                            </div>
                            <div class="rounded-box bg-etc-surface-container p-4">
                                <p class="text-xs font-bold uppercase text-etc-on-muted">Nominal Akhir</p>
                                <p class="mt-1 font-heading text-2xl font-bold text-etc-on-surface">{{ $paymentAmount }}</p>
                            </div>
                            <x-ui.button :href="route('student.payments.show', $latestPayment)" class="w-full" icon="heroicon-m-credit-card">
                                Detail Pembayaran
                            </x-ui.button>
                        </div>
                    @else
                        <x-ui.empty-state heading="Belum ada pembayaran" description="Status pembayaran akan tampil setelah pendaftaran dibuat." icon="heroicon-o-credit-card" />
                    @endif
                </x-ui.panel>

                <x-ui.panel heading="Rapor Terbaru" description="Hanya rapor yang sudah dipublikasikan admin.">
                    @if ($latestReport)
                        <div class="space-y-4">
                            <div class="rounded-box bg-etc-surface-container p-4">
                                <p class="font-heading text-sm font-bold text-etc-on-surface">{{ $latestReportClass?->program?->name ?? 'Program ETC Planet' }}</p>
                                <p class="mt-1 text-sm text-etc-on-muted">{{ $latestReportClass?->name ?? 'Kelas ETC' }} - {{ $latestReport->issued_at?->format('d M Y') ?? 'Tanggal belum tersedia' }}</p>
                                <p class="mt-4 text-xs font-bold uppercase text-etc-on-muted">Nilai Akhir</p>
                                <p class="mt-1 font-heading text-3xl font-bold text-etc-magenta">{{ $latestReport->final_grade ?? '-' }}</p>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                                <x-ui.button :href="route('student.report-cards.show', $latestReport)" icon="heroicon-m-eye">Lihat Rapor</x-ui.button>
                                @if ($latestReport->pdf_path)
                                    <x-ui.button :href="route('student.report-cards.download', $latestReport)" outlined icon="heroicon-m-arrow-down-tray">Unduh</x-ui.button>
                                @endif
                            </div>
                        </div>
                    @else
                        <x-ui.empty-state heading="Belum ada rapor terbit" description="Rapor akan tampil setelah dipublikasikan admin." icon="heroicon-o-document-text" />
                    @endif
                </x-ui.panel>

                <x-ui.panel heading="Bantuan Siswa" description="Butuh bantuan tentang kelas, pembayaran, atau rapor?">
                    <div class="rounded-box bg-etc-charcoal p-4 text-etc-surface shadow-soft">
                        <p class="font-heading text-sm font-bold">Chatbot bantuan</p>
                        <p class="mt-2 text-sm leading-6 text-etc-surface/70">Kirim pertanyaan melalui halaman bantuan. Untuk saat ini admin akan membantu dari kategori yang tersedia.</p>
                    </div>
                    <x-ui.button href="#" class="mt-4 w-full" outlined icon="heroicon-m-chat-bubble-left-right" data-dashboard-action="Buka halaman Bantuan dari menu saat tersedia untuk tindak lanjut.">
                        Buka Bantuan
                    </x-ui.button>
                </x-ui.panel>
            </aside>
        </section>
    </div>
</x-layouts.dashboard>

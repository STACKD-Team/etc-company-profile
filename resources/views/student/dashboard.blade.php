@php
    $sidebarItems = [
        ['label' => 'Dashboard', 'route' => 'student.dashboard', 'url' => '#', 'key' => 'dashboard', 'svg' => 'nav-dashboard'],
        ['label' => 'Profil Saya', 'route' => 'student.profile.show', 'url' => '#', 'key' => 'profile', 'svg' => 'nav-profile'],
        ['label' => 'Kelas Saya', 'route' => 'student.classes.index', 'url' => '#', 'key' => 'classes', 'svg' => 'nav-class'],
        ['label' => 'Rapor', 'route' => 'student.report-cards.index', 'url' => '#', 'key' => 'reports', 'svg' => 'nav-report'],
        ['label' => 'Riwayat Pembayaran', 'route' => 'student.payments.index', 'url' => '#', 'key' => 'payments', 'svg' => 'nav-payment'],
    ];

    $displayName = $student->full_name ?? $student->name;
    $courseName = $activeProgram?->name ?? 'Belum ada kelas aktif';
    $className = $activeCourseClass?->name ? "{$activeProgram?->name} - {$activeCourseClass->name}" : $courseName;
    $instructor = $activeCourseClass?->instructor;
    $instructorName = $instructor?->full_name ?? $instructor?->name ?? 'Instruktur belum ditentukan';
    $schedule = $activeCourseClass?->schedule_days && $activeCourseClass?->schedule_time
        ? $activeCourseClass->schedule_days.', '.\Illuminate\Support\Str::of((string) $activeCourseClass->schedule_time)->substr(0, 5).' WIB'
        : 'Jadwal belum ditentukan';

    $statCards = [
        ['label' => 'Kelas Aktif', 'value' => $stats['active_classes'], 'icon' => 'stat-class'],
        ['label' => 'Total Pertemuan', 'value' => $stats['total_meetings'], 'icon' => 'stat-meeting'],
        ['label' => 'Rata-rata Nilai', 'value' => $stats['average_grade'], 'icon' => 'stat-grade'],
        ['label' => 'Sertifikat', 'value' => $stats['certificates'], 'icon' => 'stat-certificate'],
    ];
@endphp

<x-layouts.dashboard title="Dashboard Siswa" area="student" active="dashboard" :user="$student" :sidebar-items="$sidebarItems">
    <x-slot:sidebarActions>
        <a href="#" data-dashboard-action="Bantuan sedang disiapkan." class="flex min-h-12 items-center justify-center gap-2 rounded-full border border-zinc-600 px-4 py-3 font-heading text-sm font-bold text-zinc-300 transition hover:border-white hover:text-white">
            <x-ui.icon name="help" class="h-4 w-4" />
            Bantuan
        </a>
    </x-slot:sidebarActions>

    <div data-student-dashboard-page class="space-y-6">
        <section class="rounded-card bg-etc-charcoal p-6 text-white shadow-panel md:p-8">
            <h2 class="font-heading text-3xl font-black">Halo, {{ $displayName }}!</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-300">Selamat datang kembali di ETC Planet. Mari lanjutkan progres belajarmu hari ini.</p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan progres belajar">
            @foreach ($statCards as $card)
                <article class="student-stat-card rounded-card bg-white p-5 shadow-soft" data-stat-card>
                    <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-etc-surface-low">
                        <x-ui.icon :name="$card['icon']" class="h-6 w-6" />
                    </span>
                    <p class="text-sm font-semibold text-etc-on-muted">{{ $card['label'] }}</p>
                    <strong class="mt-2 block font-heading text-3xl font-black text-etc-on-surface" data-stat-value>{{ $card['value'] }}</strong>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="section-play" class="h-5 w-5" />
                    <h2 class="font-heading text-xl font-bold text-etc-on-surface">Kelas Berlangsung</h2>
                </div>

                <article class="overflow-hidden rounded-card bg-white shadow-panel">
                    @if ($currentEnrollment)
                        <div class="grid md:grid-cols-[280px_minmax(0,1fr)]">
                            <div class="relative min-h-56 bg-etc-surface-container">
                                <img src="{{ asset('images/foto_english_student.jpg') }}" alt="Siswa belajar bahasa" class="h-full w-full object-cover">
                                <span class="absolute left-4 top-4 rounded-full bg-etc-magenta px-3 py-1 font-heading text-xs font-bold uppercase text-white">{{ $activeProgram?->category ?? 'Program' }}</span>
                            </div>
                            <div class="p-6">
                                <h3 class="font-heading text-2xl font-bold text-etc-on-surface">{{ $className }}</h3>
                                <p class="mt-3 text-sm text-etc-on-muted">{{ $activeProgram?->description ?? 'Detail kelas akan diperbarui oleh admin.' }}</p>

                                <div class="mt-5 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-etc-magenta font-heading text-sm font-bold text-white">
                                        {{ \Illuminate\Support\Str::of($instructorName)->trim()->substr(0, 1)->upper() }}
                                    </div>
                                    <span class="text-sm font-semibold text-etc-on-muted">{{ $instructorName }}</span>
                                </div>

                                <div class="mt-6">
                                    <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                                        <span class="text-etc-on-muted">Progres: Pertemuan {{ $courseProgress['completed'] }}/{{ $courseProgress['total'] }}</span>
                                        <strong class="font-heading text-etc-magenta">{{ $courseProgress['percent'] }}%</strong>
                                    </div>
                                    <div class="h-3 overflow-hidden rounded-full bg-etc-surface-container">
                                        <span class="student-progress-bar block h-full rounded-full bg-etc-magenta" data-progress-bar data-progress-target="{{ $courseProgress['percent'] }}"></span>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="flex items-center gap-2 text-sm font-semibold text-etc-on-muted">
                                        <x-ui.icon name="course-date" class="h-4 w-4" />
                                        {{ $schedule }}
                                    </p>
                                    <a href="#" data-dashboard-action="Membuka sesi kelas..." class="inline-flex min-h-11 items-center justify-center rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
                                        Gabung Sesi
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <h3 class="font-heading text-xl font-bold text-etc-on-surface">Belum ada kelas aktif.</h3>
                            <p class="mt-2 text-sm text-etc-on-muted">Kelas akan tampil setelah pendaftaran dan penempatan selesai diverifikasi.</p>
                        </div>
                    @endif
                </article>
            </div>

            <aside class="space-y-4">
                <div class="flex items-center gap-3">
                    <x-ui.icon name="section-download" class="h-5 w-5" />
                    <h2 class="font-heading text-xl font-bold text-etc-on-surface">Rapor Terakhir</h2>
                </div>

                <div class="rounded-card bg-white p-5 shadow-panel">
                    @forelse ($publishedReports as $report)
                        <div @class(['flex items-center gap-4 py-4', 'border-b border-etc-outline-variant/60' => ! $loop->last])>
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-etc-surface-low">
                                <x-ui.icon :name="$loop->first ? 'report-primary' : 'report-secondary'" class="h-6 w-6" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate font-heading text-sm text-etc-on-surface">{{ $report->term ?? 'Rapor Terakhir' }}</strong>
                                <p class="mt-1 truncate text-sm text-etc-on-muted">{{ $report->enrollment?->courseClass?->program?->name ?? 'Program ETC Planet' }}</p>
                            </div>
                            <a href="#" data-dashboard-action="Rapor sedang disiapkan." aria-label="Unduh rapor {{ $report->term ?? 'terakhir' }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-etc-surface-low text-etc-magenta transition hover:bg-etc-magenta hover:text-white">
                                <span class="material-symbols-outlined text-lg">download</span>
                            </a>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <p class="font-heading text-base font-bold text-etc-on-surface">Belum ada rapor terbit.</p>
                            <p class="mt-2 text-sm text-etc-on-muted">Rapor akan tampil setelah dipublikasikan admin.</p>
                        </div>
                    @endforelse

                    <a href="#" data-dashboard-action="Halaman semua rapor belum tersedia." class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-pill border border-etc-outline-variant px-4 py-3 font-heading text-sm font-bold text-etc-on-muted transition hover:border-etc-magenta hover:text-etc-magenta">
                        Lihat Semua Rapor
                    </a>
                </div>
            </aside>
        </section>
    </div>
</x-layouts.dashboard>

<x-layouts.dashboard title="Dashboard Admin" area="admin" active="dashboard">
    <x-slot:eyebrow>Operasional ETC Planet</x-slot:eyebrow>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Siswa', 'value' => number_format($summary['students_count']), 'icon' => 'groups', 'tone' => 'text-etc-magenta'],
            ['label' => 'Pendaftaran Hari Ini', 'value' => number_format($summary['new_registrations_count']), 'icon' => 'how_to_reg', 'tone' => 'text-etc-magenta'],
            ['label' => 'Pendapatan Terverifikasi', 'value' => 'Rp '.number_format($summary['paid_revenue'], 0, ',', '.'), 'icon' => 'payments', 'tone' => 'text-emerald-700'],
            ['label' => 'Kelas Aktif', 'value' => number_format($summary['active_classes_count']), 'icon' => 'meeting_room', 'tone' => 'text-etc-magenta'],
        ] as $stat)
            <article class="rounded-card border border-etc-outline-variant/60 bg-white p-5 shadow-soft">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $stat['label'] }}</p>
                        <p class="mt-3 font-heading text-3xl font-black text-etc-on-surface">{{ $stat['value'] }}</p>
                    </div>
                    <span class="material-symbols-outlined rounded-full bg-etc-surface-container p-3 text-2xl {{ $stat['tone'] }}">{{ $stat['icon'] }}</span>
                </div>
            </article>
        @endforeach
    </div>

    <section class="mt-6 rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
        <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-center">
            <div>
                <h2 class="font-heading text-xl font-black text-etc-on-surface">Pendaftaran Terbaru</h2>
                <p class="mt-1 text-sm text-etc-on-muted">Data diambil dari tabel registrations.</p>
            </div>
            <a href="{{ \Illuminate\Support\Facades\Route::has('admin.registrations.index') ? route('admin.registrations.index') : '#' }}" class="inline-flex min-h-10 items-center justify-center rounded-full border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-surface hover:border-etc-magenta hover:text-etc-magenta">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead>
                    <tr class="border-b border-etc-outline-variant/60 text-xs uppercase text-etc-on-muted">
                        <th class="py-3 pr-4 font-heading font-bold">Kode</th>
                        <th class="py-3 pr-4 font-heading font-bold">Nama</th>
                        <th class="py-3 pr-4 font-heading font-bold">Program</th>
                        <th class="py-3 pr-4 font-heading font-bold">Status</th>
                        <th class="py-3 font-heading font-bold">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-etc-outline-variant/50">
                    @forelse ($latestRegistrations as $registration)
                        <tr>
                            <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $registration->registration_code }}</td>
                            <td class="py-4 pr-4 text-etc-on-surface">{{ $registration->applicant_name }}</td>
                            <td class="py-4 pr-4 text-etc-on-muted">{{ $registration->program?->name ?? '-' }}</td>
                            <td class="py-4 pr-4">
                                <span class="inline-flex rounded-full bg-etc-surface-container px-3 py-1 font-heading text-xs font-bold text-etc-magenta">
                                    {{ str($registration->status)->replace('_', ' ')->headline() }}
                                </span>
                            </td>
                            <td class="py-4 text-etc-on-muted">{{ $registration->created_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-etc-on-muted">Belum ada pendaftaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.dashboard>

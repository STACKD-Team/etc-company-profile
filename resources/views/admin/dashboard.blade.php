<x-layouts.dashboard title="Dashboard Admin" area="admin" active="dashboard">
    <x-slot:eyebrow>Operasional ETC Planet</x-slot:eyebrow>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Siswa', 'value' => number_format($summary['students_count']), 'icon' => 'groups', 'tone' => 'text-etc-magenta'],
            ['label' => 'Pendaftaran Hari Ini', 'value' => number_format($summary['new_registrations_count']), 'icon' => 'how_to_reg', 'tone' => 'text-etc-magenta'],
            ['label' => 'Pendapatan Terverifikasi', 'value' => 'Rp '.number_format($summary['paid_revenue'], 0, ',', '.'), 'icon' => 'payments', 'tone' => 'text-emerald-700'],
            ['label' => 'Kelas Aktif', 'value' => number_format($summary['active_classes_count']), 'icon' => 'meeting_room', 'tone' => 'text-etc-magenta'],
        ] as $stat)
            <x-ui.panel compact class="h-full transition hover:-translate-y-0.5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $stat['label'] }}</p>
                        <p class="mt-3 font-heading text-3xl font-black text-etc-on-surface">{{ $stat['value'] }}</p>
                    </div>
                    <span class="material-symbols-outlined rounded-selector bg-etc-surface-container p-2 text-2xl {{ $stat['tone'] }}">{{ $stat['icon'] }}</span>
                </div>
            </x-ui.panel>
        @endforeach
    </div>

    <x-ui.panel heading="Pendaftaran Terbaru" description="Data diambil dari tabel registrations." class="mt-6">
        <x-slot:actions>
            <x-ui.button :href="\Illuminate\Support\Facades\Route::has('admin.registrations.index') ? route('admin.registrations.index') : '#'" outlined size="sm">
                Lihat Semua
            </x-ui.button>
        </x-slot:actions>

        <div class="etc-data-table-scroll overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead>
                    <tr class="border-b-2 border-etc-outline-variant/60 text-xs uppercase text-etc-on-muted">
                        <th class="pb-3 pr-4 font-heading font-bold">Kode</th>
                        <th class="pb-3 pr-4 font-heading font-bold">Nama</th>
                        <th class="pb-3 pr-4 font-heading font-bold">Program</th>
                        <th class="pb-3 pr-4 font-heading font-bold">Status</th>
                        <th class="pb-3 font-heading font-bold">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-etc-outline-variant/60">
                    @forelse ($latestRegistrations as $registration)
                        <tr>
                            <td class="py-4 pr-4 font-heading font-bold text-etc-on-surface">{{ $registration->registration_code }}</td>
                            <td class="py-4 pr-4 text-etc-on-surface">{{ $registration->applicant_name }}</td>
                            <td class="py-4 pr-4 text-etc-on-muted">{{ $registration->program?->name ?? '-' }}</td>
                            <td class="py-4 pr-4">
                                <x-ui.badge :status="$registration->status" />
                            </td>
                            <td class="py-4 text-etc-on-muted">{{ $registration->created_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8">
                                <x-ui.empty-state
                                    heading="Belum ada pendaftaran"
                                    description="Pendaftaran terbaru akan muncul setelah calon siswa mengirim formulir."
                                    icon="heroicon-o-clipboard-document-list"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.dashboard>

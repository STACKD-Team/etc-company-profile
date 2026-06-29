<x-layouts.dashboard title="Dashboard Admin" area="admin" active="dashboard">
    <x-slot:eyebrow>Operasional ETC Planet</x-slot:eyebrow>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Siswa', 'value' => number_format($summary['students_count']), 'icon' => 'heroicon-m-users'],
            ['label' => 'Pendaftaran Hari Ini', 'value' => number_format($summary['new_registrations_count']), 'icon' => 'heroicon-m-clipboard-document-list'],
            ['label' => 'Pendapatan Terverifikasi', 'value' => 'Rp '.number_format($summary['paid_revenue'], 0, ',', '.'), 'icon' => 'heroicon-m-banknotes'],
            ['label' => 'Kelas Aktif', 'value' => number_format($summary['active_classes_count']), 'icon' => 'heroicon-m-building-office-2'],
        ] as $stat)
            <x-ui.stat-card :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" />
        @endforeach
    </div>

    <x-ui.detail-card heading="Pendaftaran Terbaru" description="Data diambil dari tabel registrations." class="mt-6">
        <x-slot:actions>
            <x-ui.button :href="\Illuminate\Support\Facades\Route::has('admin.registration.index') ? route('admin.registration.index') : '#'" outlined size="sm">
                Lihat Semua
            </x-ui.button>
        </x-slot:actions>

        <x-ui.data-table
            :items="$latestRegistrations"
            :columns="[
                'registration_code' => 'Kode',
                'applicant_name' => 'Nama',
                'program' => 'Program',
                'status' => 'Status',
                'created_at' => 'Tanggal',
            ]"
            row-view="pages.admin.dashboard.partials.latest-registration-row"
            empty="Belum ada pendaftaran"
            empty-description="Pendaftaran terbaru akan muncul setelah calon siswa mengirim formulir."
            :show-search="false"
        />
    </x-ui.detail-card>
</x-layouts.dashboard>

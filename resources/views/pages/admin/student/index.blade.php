<x-layouts.dashboard title="Data Siswa" area="admin" active="students">
    <x-ui.data-table
        :items="$students"
        :columns="[
            'full_name' => ['label' => 'Nama', 'sortable' => true],
            'email' => ['label' => 'Email', 'sortable' => true],
            'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'text', 'name' => 'status', 'placeholder' => 'Status siswa']],
            'enrollments_count' => ['label' => 'Kelas', 'sortable' => true],
            'created_at' => ['label' => 'Terdaftar', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="pages.admin.student.partials.row"
        empty="Belum ada siswa"
        empty-description="Siswa akan tampil setelah pendaftaran diproses menjadi akun siswa."
        search-placeholder="Cari nama, email, atau no induk"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.student.create')" icon="heroicon-m-plus">Tambah Siswa</x-ui.button>
            <x-ui.button type="button" outlined icon="heroicon-m-arrow-down-tray" data-open-modal="student-export-modal">
                Export
            </x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>

    <x-ui.modal id="student-export-modal" heading="Export Siswa" description="Filter dan unduh rekap siswa dari halaman Data Siswa." icon="heroicon-o-table-cells">
        <form method="POST" action="{{ route('admin.exports.students.download') }}" class="space-y-4">
            @csrf
            <x-ui.field name="year" label="Tahun" type="number" :value="now()->year" />
            <x-ui.button type="submit" icon="heroicon-m-arrow-down-tray">Download</x-ui.button>
        </form>
    </x-ui.modal>
</x-layouts.dashboard>

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
    />
</x-layouts.dashboard>

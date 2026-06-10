<x-layouts.dashboard title="Data Instruktur" area="admin" active="instructors">
    <x-ui.data-table
        :items="$instructors"
        :columns="[
            'full_name' => ['label' => 'Instruktur', 'sortable' => true],
            'email' => ['label' => 'Email', 'sortable' => true],
            'specialization' => ['label' => 'Spesialisasi', 'filter' => ['type' => 'text', 'name' => 'specialization', 'placeholder' => 'Spesialisasi']],
            'classes_taught_count' => ['label' => 'Kelas', 'sortable' => true],
            'created_at' => ['label' => 'Dibuat', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="admin.instructors.partials.row"
        empty="Belum ada instruktur"
        empty-description="Instructor akan tampil setelah akun role instructor dibuat."
        search-placeholder="Cari nama atau email instruktur"
    />
</x-layouts.dashboard>

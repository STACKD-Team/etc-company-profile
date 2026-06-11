<x-layouts.dashboard title="Kelas Saya" area="student" active="classes" :user="$student">
    <x-ui.data-table
        :items="$enrollments"
        :columns="[
            'class' => [
                'label' => 'Kelas',
                'filter' => ['type' => 'autocomplete', 'name' => 'class_id', 'options' => $classOptions],
            ],
            'program' => [
                'label' => 'Program',
                'filter' => ['type' => 'autocomplete', 'name' => 'program_id', 'options' => $programOptions],
            ],
            'instructor' => [
                'label' => 'Instruktur',
                'filter' => ['type' => 'autocomplete', 'name' => 'instructor_id', 'options' => $instructorOptions],
            ],
            'enrolled_at' => [
                'label' => 'Mulai',
                'key' => 'enrolled_at',
                'sortable' => true,
            ],
            'status' => [
                'label' => 'Status',
                'key' => 'status',
                'sortable' => true,
                'filter' => [
                    'type' => 'select',
                    'name' => 'status',
                    'options' => [
                        'active' => 'Sedang Berjalan',
                        'completed' => 'Selesai',
                        'dropped' => 'Berhenti',
                    ],
                ],
            ],
            'action' => 'Aksi',
        ]"
        row-view="student.classes.partials.row"
        empty="Belum ada kelas"
        empty-description="Kelas akan tampil setelah admin memperbarui enrollment siswa."
        search-placeholder="Cari kelas, program, instructor, jadwal, atau ruangan"
        data-student-classes-table
    />
</x-layouts.dashboard>

<x-layouts.dashboard title="Riwayat Pembelajaran" area="student" active="classes" :user="$student">
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
            'period' => [
                'label' => 'Periode',
                'key' => 'enrolled_at',
                'sortable' => true,
            ],
            'instructor' => [
                'label' => 'Instruktur',
                'filter' => ['type' => 'autocomplete', 'name' => 'instructor_id', 'options' => $instructorOptions],
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
        row-view="student.partials.learning-history-row"
        empty="Belum ada riwayat pembelajaran"
        empty-description="Riwayat akan tampil setelah siswa masuk kelas."
        search-placeholder="Cari kelas, program, instructor, jadwal, atau ruangan"
        data-student-learning-history-table
    />
</x-layouts.dashboard>

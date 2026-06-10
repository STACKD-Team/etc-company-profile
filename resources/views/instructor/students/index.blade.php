<x-layouts.dashboard title="Siswa Instructor" area="instructor" active="students">
    <x-ui.data-table
        :items="$students"
        :columns="[
            'student' => [
                'label' => 'Siswa',
                'filter' => ['type' => 'autocomplete', 'name' => 'student_id', 'options' => $studentOptions],
            ],
            'class' => [
                'label' => 'Kelas',
                'filter' => ['type' => 'autocomplete', 'name' => 'class_id', 'options' => $classOptions],
            ],
            'enrolled_at' => [
                'label' => 'Mulai',
                'key' => 'enrolled_at',
                'sortable' => true,
                'filter' => ['type' => 'date', 'name' => 'enrolled_at'],
            ],
            'status' => [
                'label' => 'Status',
                'key' => 'status',
                'sortable' => true,
                'filter' => [
                    'type' => 'select',
                    'name' => 'status',
                    'options' => ['active' => 'Active', 'completed' => 'Completed', 'dropped' => 'Dropped'],
                ],
            ],
            'assessment' => [
                'label' => 'Assessment',
                'filter' => [
                    'type' => 'select',
                    'name' => 'assessment_status',
                    'options' => [
                        'not_started' => 'Belum mulai',
                        'incomplete' => 'Belum lengkap',
                        'complete' => 'Lengkap',
                        'draft' => 'Semua draft',
                        'published' => 'Published',
                    ],
                ],
            ],
            'action' => 'Aksi',
        ]"
        row-view="instructor.partials.student-row"
        empty="Belum ada siswa"
        empty-description="Siswa dari kelas yang kamu ajar akan tampil di sini."
        search-placeholder="Cari nama, email, atau kelas"
    />
</x-layouts.dashboard>

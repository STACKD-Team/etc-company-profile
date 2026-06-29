<x-layouts.dashboard
    title="Siswa Instructor"
    description="Daftar ini hanya memuat siswa dari kelas yang ditugaskan kepadamu."
    area="instructor"
    active="students"
>
    <x-slot:eyebrow>Instructor Workspace</x-slot:eyebrow>

    <x-ui.data-table
        :items="$students"
        :columns="[
            'student' => [
                'label' => 'Siswa',
                'key' => 'student',
                'sortable' => true,
                'filter' => ['type' => 'autocomplete', 'name' => 'student_id', 'options' => $studentOptions],
            ],
            'class' => [
                'label' => 'Kelas',
                'key' => 'class',
                'sortable' => true,
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
                'key' => 'assessment',
                'sortable' => true,
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
        row-view="pages.instructor.student.partials.row"
        empty="Belum ada siswa"
        empty-description="Siswa dari kelas yang kamu ajar akan tampil di sini."
        search-placeholder="Cari nama, email, atau kelas"
        data-instructor-students-table
    />
</x-layouts.dashboard>

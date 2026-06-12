<x-layouts.dashboard
    title="Assessment & Rapor"
    description="Mulai, lanjutkan, atau tinjau assessment siswa pada kelas yang kamu ajar."
    area="instructor"
    active="reports"
>
    <x-slot:eyebrow>Instructor Workspace</x-slot:eyebrow>

    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$assessments"
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
            'score' => [
                'label' => 'Total',
                'key' => 'score',
                'sortable' => true,
                'filter' => ['type' => 'number', 'name' => 'total_score', 'min' => 0, 'max' => 100, 'placeholder' => 'Nilai tepat'],
            ],
            'status' => [
                'label' => 'Status',
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
        row-view="pages.instructor.report-card.partials.row"
        empty="Belum ada siswa untuk dinilai"
        empty-description="Assessment tersedia setelah siswa masuk ke kelas yang kamu ajar."
        search-placeholder="Cari siswa atau kelas"
        data-instructor-assessments-table
    />
</x-layouts.dashboard>

<x-layouts.dashboard title="Rapor" area="student" active="reports" :user="$student">
    <x-ui.data-table
        :items="$reportCards"
        :columns="[
            'report' => [
                'label' => 'Rapor',
            ],
            'program' => [
                'label' => 'Program',
                'filter' => ['type' => 'autocomplete', 'name' => 'program_id', 'options' => $programOptions],
            ],
            'class' => [
                'label' => 'Kelas',
                'filter' => ['type' => 'autocomplete', 'name' => 'class_id', 'options' => $classOptions],
            ],
            'issued_at' => [
                'label' => 'Terbit',
                'key' => 'issued_at',
                'sortable' => true,
                'filter' => ['type' => 'date', 'name' => 'issued_at'],
            ],
            'grade' => [
                'label' => 'Nilai',
                'key' => 'final_grade',
                'sortable' => true,
                'filter' => ['type' => 'select', 'name' => 'final_grade', 'options' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']],
            ],
            'file' => [
                'label' => 'File',
                'filter' => ['type' => 'select', 'name' => 'report_status', 'options' => ['with_file' => 'File tersedia', 'without_file' => 'Belum ada file']],
            ],
            'action' => 'Aksi',
        ]"
        row-view="pages.student.report-card.partials.row"
        empty="Belum ada rapor yang dipublikasikan"
        empty-description="Rapor akan tampil setelah admin mempublish hasil pembelajaran."
        search-placeholder="Cari program, kelas, next class, atau komentar"
        data-student-report-cards-table
    />
</x-layouts.dashboard>

<x-layouts.dashboard
    title="Kelas Mengajar"
    description="Temukan kelas berdasarkan program, jadwal, jumlah siswa, atau status pembelajaran."
    area="instructor"
    active="classes"
>
    <x-slot:eyebrow>Instructor Workspace</x-slot:eyebrow>

    <x-ui.data-table
        :items="$classes"
        :columns="[
            'name' => [
                'label' => 'Kelas',
                'key' => 'name',
                'sortable' => true,
                'filter' => ['type' => 'text', 'name' => 'name', 'placeholder' => 'Nama kelas'],
            ],
            'program' => [
                'label' => 'Program',
                'key' => 'program',
                'sortable' => true,
                'filter' => ['type' => 'autocomplete', 'name' => 'program_id', 'options' => $programOptions],
            ],
            'schedule' => [
                'label' => 'Jadwal / Ruang',
                'key' => 'schedule',
                'sortable' => true,
                'filter' => ['type' => 'text', 'name' => 'schedule', 'placeholder' => 'Hari, jam, atau ruangan'],
            ],
            'students' => [
                'label' => 'Siswa',
                'key' => 'students',
                'sortable' => true,
                'filter' => ['type' => 'number', 'name' => 'students_count', 'min' => 0, 'placeholder' => 'Jumlah tepat'],
            ],
            'status' => [
                'label' => 'Status',
                'key' => 'status',
                'sortable' => true,
                'filter' => [
                    'type' => 'select',
                    'name' => 'status',
                    'options' => [
                        'upcoming' => 'Upcoming',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ],
                ],
            ],
            'action' => 'Aksi',
        ]"
        row-view="instructor.partials.class-row"
        empty="Belum ada kelas yang ditugaskan"
        empty-description="Kelas akan tampil setelah admin menugaskan instructor."
        search-placeholder="Cari nama kelas, program, atau ruangan"
        data-instructor-classes-table
    />
</x-layouts.dashboard>

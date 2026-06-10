<x-layouts.dashboard title="Kelas Mengajar" area="instructor" active="classes">
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
                'filter' => ['type' => 'autocomplete', 'name' => 'program_id', 'options' => $programOptions],
            ],
            'schedule' => [
                'label' => 'Jadwal / Ruang',
                'filter' => ['type' => 'text', 'name' => 'schedule', 'placeholder' => 'Hari, jam, atau ruangan'],
            ],
            'students' => [
                'label' => 'Siswa',
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
    />
</x-layouts.dashboard>

<x-layouts.dashboard :title="$class->name" area="instructor" active="classes">
    <x-slot:headerActions>
        <x-ui.button :href="route('instructor.classes.index')" outlined icon="heroicon-m-arrow-left">
            Kembali
        </x-ui.button>
    </x-slot:headerActions>

    <div class="space-y-6">
        <x-ui.panel :heading="$class->program?->name ?? 'Detail Kelas'" description="Informasi operasional kelas yang kamu ajar." icon="heroicon-o-academic-cap">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Jadwal', 'value' => trim(($class->schedule_days ?? '-').' '.($class->schedule_time ?? ''))],
                    ['label' => 'Ruangan', 'value' => $class->room ?? '-'],
                    ['label' => 'Periode', 'value' => ($class->start_date?->format('d M Y') ?? '-').' - '.($class->end_date?->format('d M Y') ?? '-')],
                    ['label' => 'Jumlah Siswa', 'value' => $class->enrollments_count],
                ] as $detail)
                    <div class="rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4">
                        <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $detail['label'] }}</p>
                        <p class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $detail['value'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-5">
                <x-ui.badge :status="$class->status" />
            </div>
        </x-ui.panel>

        <x-ui.data-table
            :items="$students"
            :columns="[
                'student' => [
                    'label' => 'Siswa',
                    'filter' => ['type' => 'autocomplete', 'name' => 'student_id', 'options' => $studentOptions],
                ],
                'status' => [
                    'label' => 'Enrollment',
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
            row-view="instructor.partials.class-student-row"
            empty="Belum ada siswa di kelas ini"
            empty-description="Enrollment siswa akan tampil setelah diproses admin."
            search-placeholder="Cari nama atau email siswa"
        />
    </div>
</x-layouts.dashboard>

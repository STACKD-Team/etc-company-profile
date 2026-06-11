<x-layouts.dashboard
    :title="$class->name"
    :description="($class->program?->name ?? 'Program belum ditentukan').' - ringkasan jadwal dan siswa kelas.'"
    area="instructor"
    active="classes"
>
    <x-slot:eyebrow>Detail Kelas</x-slot:eyebrow>
    <x-slot:headerActions>
        <x-ui.button :href="route('instructor.classes.index')" outlined icon="heroicon-m-arrow-left">
            Kembali
        </x-ui.button>
    </x-slot:headerActions>

    <div class="space-y-6">
        <x-ui.panel
            heading="Ringkasan Kelas"
            description="Informasi operasional untuk kelas yang kamu ajar."
            icon="heroicon-o-academic-cap"
            data-instructor-class-summary
        >
            <x-slot:actions>
                <x-ui.badge :status="$class->status" />
            </x-slot:actions>

            <dl class="grid gap-x-6 gap-y-5 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Jadwal', 'value' => trim(($class->schedule_days ?? '-').' '.($class->schedule_time ?? ''))],
                    ['label' => 'Ruangan', 'value' => $class->room ?? '-'],
                    ['label' => 'Periode', 'value' => ($class->start_date?->format('d M Y') ?? '-').' - '.($class->end_date?->format('d M Y') ?? '-')],
                    ['label' => 'Jumlah Siswa', 'value' => $class->enrollments_count],
                ] as $detail)
                    <div class="border-l-2 border-etc-outline-variant pl-4 first:border-etc-magenta">
                        <dt class="font-heading text-xs font-bold uppercase tracking-wide text-etc-on-muted">{{ $detail['label'] }}</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $detail['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
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
            data-instructor-class-students-table
        />
    </div>
</x-layouts.dashboard>

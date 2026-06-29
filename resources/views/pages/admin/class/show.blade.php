<x-layouts.dashboard :title="$class->name" area="admin" active="classes">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$class->name"
        subtitle="Detail kelas, jadwal, room, instructor, enrollment, dan link rapor."
        :back-url="route('admin.class.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$class->status" />
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.class.edit', $class)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route('admin.class.destroy', $class)" heading="Hapus kelas?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Enrollment" :value="$class->enrollments->count()" icon="heroicon-m-users" />
        <x-ui.stat-card label="Rapor" :value="$class->enrollments->filter(fn ($enrollment) => $enrollment->reportCard)->count()" icon="heroicon-m-document-text" />
        <x-ui.stat-card label="Room" :value="$class->room_label ?? '-'" icon="heroicon-m-building-office-2" />
    </div>

    <x-ui.detail-card heading="Detail Kelas" class="mt-6">
        <x-ui.description-list columns="3">
            <x-ui.description-item label="Program">
                @if ($class->program)
                    <a href="{{ route('admin.program.show', $class->program) }}" class="font-bold text-etc-magenta hover:text-etc-primary">{{ $class->program->name }}</a>
                @else
                    -
                @endif
            </x-ui.description-item>
            <x-ui.description-item label="Instructor">
                @if ($class->instructor)
                    <a href="{{ route('admin.instructor.show', $class->instructor) }}" class="font-bold text-etc-magenta hover:text-etc-primary">{{ $class->instructor->full_name ?? $class->instructor->name }}</a>
                @else
                    -
                @endif
            </x-ui.description-item>
            <x-ui.description-item label="Room">
                @if ($class->room)
                    <a href="{{ route('admin.room.show', $class->room) }}" class="font-bold text-etc-magenta hover:text-etc-primary">{{ $class->room->name }}</a>
                @else
                    {{ $class->room_label ?? '-' }}
                @endif
            </x-ui.description-item>
            <x-ui.description-item label="Hari" :value="$class->schedule_days ?: '-'" />
            <x-ui.description-item label="Jam" :value="$class->schedule_time ?: '-'" />
            <x-ui.description-item label="Status"><x-ui.badge :status="$class->status" /></x-ui.description-item>
            <x-ui.description-item label="Mulai" :value="$class->start_date?->format('d M Y') ?: '-'" />
            <x-ui.description-item label="Selesai" :value="$class->end_date?->format('d M Y') ?: '-'" />
        </x-ui.description-list>
    </x-ui.detail-card>

    <x-ui.detail-card heading="Enrollment Kelas" class="mt-6">
        <x-ui.data-table
            :items="$class->enrollments"
            :columns="[
                'student' => 'Siswa',
                'enrolled_at' => 'Mulai',
                'completed_at' => 'Selesai',
                'status' => 'Status',
                'report' => 'Rapor',
                'actions' => 'Aksi',
            ]"
            row-view="pages.admin.class.partials.enrollment-row"
            empty="Belum ada enrollment"
            empty-description="Siswa yang masuk kelas ini akan tampil di sini."
            :show-search="false"
        />
    </x-ui.detail-card>
</x-layouts.dashboard>

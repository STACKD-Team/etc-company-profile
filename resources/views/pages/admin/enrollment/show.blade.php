<x-layouts.dashboard title="Detail Enrollment" area="admin" active="enrollments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        title="Detail Enrollment"
        subtitle="Relasi siswa, kelas, program, dan rapor untuk enrollment ini."
        :back-url="route('admin.enrollment.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$enrollment->status" />
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.enrollment.edit', $enrollment)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route('admin.enrollment.destroy', $enrollment)" heading="Hapus enrollment?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <x-ui.detail-card heading="Siswa dan Kelas">
            <x-ui.description-list>
                <x-ui.description-item label="Siswa">
                    @if ($enrollment->user)
                        <a href="{{ route('admin.student.show', $enrollment->user) }}" class="font-bold text-etc-magenta hover:text-etc-primary">
                            {{ $enrollment->user->full_name ?? $enrollment->user->name }}
                        </a>
                    @else
                        -
                    @endif
                </x-ui.description-item>
                <x-ui.description-item label="Email" :value="$enrollment->user?->email ?: '-'" />
                <x-ui.description-item label="Kelas">
                    @if ($enrollment->courseClass)
                        <a href="{{ route('admin.class.show', $enrollment->courseClass) }}" class="font-bold text-etc-magenta hover:text-etc-primary">
                            {{ $enrollment->courseClass->name }}
                        </a>
                    @else
                        -
                    @endif
                </x-ui.description-item>
                <x-ui.description-item label="Program">
                    @if ($enrollment->courseClass?->program)
                        <a href="{{ route('admin.program.show', $enrollment->courseClass->program) }}" class="font-bold text-etc-magenta hover:text-etc-primary">
                            {{ $enrollment->courseClass->program->name }}
                        </a>
                    @else
                        -
                    @endif
                </x-ui.description-item>
                <x-ui.description-item label="Instructor" :value="$enrollment->courseClass?->instructor?->full_name ?? $enrollment->courseClass?->instructor?->name ?? '-'" />
                <x-ui.description-item label="Room" :value="$enrollment->courseClass?->room_label ?: '-'" />
            </x-ui.description-list>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Status Enrollment">
            <x-ui.description-list columns="1">
                <x-ui.description-item label="Tanggal Masuk" :value="$enrollment->enrolled_at?->format('d M Y') ?: '-'" />
                <x-ui.description-item label="Tanggal Selesai" :value="$enrollment->completed_at?->format('d M Y') ?: '-'" />
                <x-ui.description-item label="Status"><x-ui.badge :status="$enrollment->status" /></x-ui.description-item>
                <x-ui.description-item label="Rapor">
                    @if ($enrollment->reportCard)
                        <x-ui.button :href="route('admin.report-card.show', $enrollment->reportCard)" size="sm" outlined>Detail Rapor</x-ui.button>
                    @else
                        <x-ui.badge status="draft">Belum ada</x-ui.badge>
                    @endif
                </x-ui.description-item>
            </x-ui.description-list>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>

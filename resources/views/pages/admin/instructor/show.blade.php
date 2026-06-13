<x-layouts.dashboard title="Detail Instruktur" area="admin" active="instructors">
    <x-ui.resource-header
        :title="$instructor->full_name ?? $instructor->name"
        :subtitle="$instructor->email.' - '.($instructor->instructor_specialization ?? 'Spesialisasi belum diisi')"
        :back-url="route('admin.instructor.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$instructor->show_on_team_page ? 'published' : 'draft'">{{ $instructor->show_on_team_page ? 'Tampil di Team' : 'Internal' }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.instructor.edit', $instructor)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route('admin.instructor.destroy', $instructor)" heading="Hapus instructor?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Kelas Diampu" :value="$instructor->classesTaught->count()" icon="heroicon-m-academic-cap" />
        <x-ui.stat-card label="Publik" :value="$instructor->show_on_team_page ? 'Ya' : 'Tidak'" icon="heroicon-m-user-circle" />
        <x-ui.stat-card label="Status" :value="$instructor->is_active ? 'Aktif' : 'Nonaktif'" icon="heroicon-m-check-circle" />
    </div>

    <x-ui.detail-card heading="Profil Instruktur" class="mt-6">
        <x-ui.description-list>
            <x-ui.description-item label="Posisi" :value="$instructor->instructor_position ?: '-'" />
            <x-ui.description-item label="Spesialisasi" :value="$instructor->instructor_specialization ?: '-'" />
            <x-ui.description-item label="Email" :value="$instructor->email" />
            <x-ui.description-item label="Status Publik">
                <x-ui.badge :status="$instructor->show_on_team_page ? 'published' : 'draft'">{{ $instructor->show_on_team_page ? 'Tampil di Team' : 'Internal' }}</x-ui.badge>
            </x-ui.description-item>
        </x-ui.description-list>

        <div class="mt-5">
            <h2 class="font-heading text-sm font-bold text-etc-on-surface">Bio</h2>
            <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $instructor->instructor_bio ?: 'Belum ada bio.' }}</p>
        </div>
    </x-ui.detail-card>

    <x-ui.detail-card heading="Kelas Diampu" class="mt-6">
        <x-ui.data-table
            :items="$instructor->classesTaught"
            :columns="[
                'name' => 'Kelas',
                'program' => 'Program',
                'schedule' => 'Jadwal',
                'status' => 'Status',
                'actions' => 'Aksi',
            ]"
            row-view="pages.admin.instructor.partials.class-row"
            empty="Belum mengajar kelas"
            empty-description="Kelas akan tampil setelah instructor di-assign ke kelas."
            :show-search="false"
        />
    </x-ui.detail-card>
</x-layouts.dashboard>

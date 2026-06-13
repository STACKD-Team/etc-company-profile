<x-layouts.dashboard :title="$room->name" area="admin" active="rooms">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$room->name"
        subtitle="Detail room dan kelas yang memakai lokasi ini."
        :back-url="route('admin.room.index')"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.room.edit', $room)" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            <x-ui.delete-action :action="route('admin.room.destroy', $room)" heading="Hapus room?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <x-ui.detail-card heading="Detail Room">
            <x-ui.description-list>
                <x-ui.description-item label="Nama" :value="$room->name" />
                <x-ui.description-item label="Kapasitas" :value="$room->capacity ?: '-'" />
                <x-ui.description-item label="Status" :value="$room->is_active ? 'Aktif' : 'Nonaktif'" />
                <x-ui.description-item label="Urutan" :value="(string) $room->display_order" />
            </x-ui.description-list>

            <div class="mt-5">
                <h2 class="font-heading text-sm font-bold text-etc-on-surface">Deskripsi</h2>
                <p class="mt-2 text-sm leading-6 text-etc-on-muted">{{ $room->description ?: '-' }}</p>
            </div>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Fasilitas">
            @if (($room->facilities ?? []) !== [])
                <div class="flex flex-wrap gap-2">
                    @foreach ($room->facilities as $facility)
                        <x-ui.badge status="primary">{{ $facility }}</x-ui.badge>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state heading="Belum ada fasilitas" description="Fasilitas bisa ditambahkan dari form edit room." compact />
            @endif
        </x-ui.detail-card>
    </div>

    <x-ui.detail-card heading="Kelas Terkait" class="mt-6">
        <x-ui.data-table
            :items="$room->classes"
            :columns="[
                'name' => 'Kelas',
                'program' => 'Program',
                'instructor' => 'Instruktur',
                'status' => 'Status',
                'actions' => 'Aksi',
            ]"
            row-view="pages.admin.room.partials.class-row"
            :show-search="false"
            empty="Belum ada kelas"
            empty-description="Kelas yang memakai room ini akan tampil di sini."
        />
    </x-ui.detail-card>
</x-layouts.dashboard>

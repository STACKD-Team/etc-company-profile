<x-layouts.dashboard title="Room" area="admin" active="rooms">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$rooms"
        :columns="[
            'name' => ['label' => 'Room', 'sortable' => true],
            'capacity' => ['label' => 'Kapasitas', 'sortable' => true],
            'is_active' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_active', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
            'display_order' => ['label' => 'Urutan', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="pages.admin.room.partials.row"
        empty="Room belum tersedia"
        empty-description="Tambahkan room agar kelas bisa memiliki lokasi belajar yang konsisten."
        search-placeholder="Cari nama atau deskripsi room"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.room.create')" icon="heroicon-m-plus">Tambah Room</x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

<x-layouts.dashboard :title="$pageTitle" area="admin" :active="$contentType">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$contents"
        :columns="[
            'title' => ['label' => 'Konten', 'sortable' => true],
            'display_order' => ['label' => 'Urutan', 'sortable' => true],
            'is_published' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_published', 'options' => ['1' => 'Published', '0' => 'Draft']]],
            'updated_at' => ['label' => 'Update', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        :row-view="$rowView"
        :empty="$pageTitle.' belum tersedia'"
        :empty-description="$pageTitle.' akan tampil setelah admin menambahkan data.'"
        search-placeholder="Cari judul atau deskripsi"
    >
        <x-slot:actions>
            <x-ui.button :href="route($routeBase.'.create')" icon="heroicon-m-plus">Tambah {{ $pageTitle }}</x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

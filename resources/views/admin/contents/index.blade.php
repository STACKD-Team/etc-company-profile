<x-layouts.dashboard title="CMS Konten" area="admin" active="contents">
    @if (session('status'))
        <div class="mb-5 rounded-card border border-green-200 bg-green-50 px-5 py-4 font-heading text-sm font-bold text-green-700">{{ session('status') }}</div>
    @endif

    <x-ui.data-table
        :items="$contents"
        :columns="[
            'title' => ['label' => 'Konten', 'sortable' => true],
            'type' => ['label' => 'Tipe', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'type', 'options' => collect($types)->mapWithKeys(fn ($type) => [$type => str($type)->replace('_', ' ')->headline()->toString()])->all()]],
            'display_order' => ['label' => 'Urutan', 'sortable' => true],
            'is_published' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_published', 'options' => ['1' => 'Published', '0' => 'Draft']]],
            'updated_at' => ['label' => 'Update', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="admin.contents.partials.row"
        empty="Konten belum tersedia"
        empty-description="Konten CMS akan tampil setelah admin menambahkan item page, gallery, partner, room, atau setting."
        search-placeholder="Cari judul atau slug"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.contents.create')" icon="heroicon-m-plus">Tambah Konten</x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

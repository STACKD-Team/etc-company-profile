<x-layouts.dashboard title="Admin Reels" area="admin" active="reels">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$reels"
        :columns="[
            'title' => ['label' => 'Reel', 'sortable' => true],
            'category' => ['label' => 'Kategori', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'category', 'options' => collect($categories)->mapWithKeys(fn ($category) => [$category => ucfirst($category)])->all()]],
            'views_count' => ['label' => 'Views', 'sortable' => true],
            'likes_count' => ['label' => 'Likes', 'sortable' => true],
            'is_published' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_published', 'options' => ['1' => 'Published', '0' => 'Draft']]],
            'created_at' => ['label' => 'Dibuat', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="pages.admin.reel.partials.row"
        empty="Belum ada reels"
        empty-description="Upload reels agar admin bisa mengelola video promosi dan dokumentasi."
        search-placeholder="Cari judul reel"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.reel.create')" icon="heroicon-m-plus">Tambah Reel</x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

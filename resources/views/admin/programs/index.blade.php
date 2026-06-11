@php
    $categories = ['english' => 'English', 'mandarin' => 'Mandarin', 'japanese' => 'Japanese', 'test_prep' => 'Test Prep', 'soft_skills' => 'Soft Skills', 'other' => 'Other'];
    $types = ['regular' => 'Regular', 'private' => 'Private', 'one_on_one' => 'One on One'];
    $targetAges = ['all' => 'All', 'kids' => 'Kids', 'teen' => 'Teen', 'adult' => 'Adult', 'university' => 'University'];
@endphp

<x-layouts.dashboard title="Master Program" area="admin" active="programs">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$programs"
        :columns="[
            'name' => ['label' => 'Program', 'sortable' => true],
            'category' => ['label' => 'Kategori', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'category', 'options' => $categories]],
            'type' => ['label' => 'Tipe', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'type', 'options' => $types]],
            'target_age' => ['label' => 'Target', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'target_age', 'options' => $targetAges]],
            'price' => ['label' => 'Biaya', 'sortable' => true],
            'is_active' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_active', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']]],
            'actions' => 'Aksi',
        ]"
        row-view="admin.programs.partials.row"
        empty="Belum ada program"
        empty-description="Tambahkan program agar bisa dipilih calon siswa dan dikelola admin."
        search-placeholder="Cari program atau slug"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.programs.create')" icon="heroicon-m-plus">Tambah Program</x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

@php($statuses = ['upcoming' => 'Upcoming', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])

<x-layouts.dashboard title="Master Kelas" area="admin" active="classes">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$classes"
        :columns="[
            'name' => ['label' => 'Kelas', 'sortable' => true],
            'program_id' => ['label' => 'Program', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'program_id', 'options' => $programs->pluck('name', 'id')->all()]],
            'instructor_id' => ['label' => 'Instruktur', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'instructor_id', 'options' => $instructors->mapWithKeys(fn ($instructor) => [$instructor->id => $instructor->full_name ?? $instructor->name])->all()]],
            'schedule' => 'Jadwal',
            'start_date' => ['label' => 'Mulai', 'sortable' => true],
            'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statuses]],
            'actions' => 'Aksi',
        ]"
        row-view="admin.classes.partials.row"
        empty="Belum ada kelas"
        empty-description="Kelas akan tampil setelah master kelas dibuat."
        search-placeholder="Cari kelas"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.classes.create')" icon="heroicon-m-plus">Tambah Kelas</x-ui.button>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

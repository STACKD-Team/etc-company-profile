@php($statuses = ['active' => 'Aktif', 'completed' => 'Selesai', 'dropped' => 'Berhenti'])

<x-layouts.dashboard title="Data Enrollment" area="admin" active="enrollments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$enrollments"
        :columns="[
            'user_id' => ['label' => 'Siswa', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'user_id', 'options' => $students->mapWithKeys(fn ($student) => [$student->id => $student->full_name ?? $student->name])->all()]],
            'class_id' => ['label' => 'Kelas', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'class_id', 'options' => $classes->mapWithKeys(fn ($class) => [$class->id => trim(($class->program?->name ?? '-').' - '.$class->name)])->all()]],
            'enrolled_at' => ['label' => 'Mulai', 'sortable' => true],
            'completed_at' => ['label' => 'Selesai', 'sortable' => true],
            'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statuses]],
            'actions' => 'Aksi',
        ]"
        row-view="pages.admin.enrollment.partials.row"
        empty="Belum ada enrollment"
        empty-description="Enrollment akan tampil setelah siswa dimasukkan ke kelas."
        search-placeholder="Cari siswa, email, kelas, atau program"
    >
        <x-slot:actions>
            <x-ui.modal id="create-enrollment-modal" heading="Assign Siswa ke Kelas" description="Buat enrollment dari daftar siswa dan kelas aktif." icon="heroicon-o-user-plus" width="2xl">
                <x-slot:trigger>
                    <x-ui.button type="button" icon="heroicon-m-plus">Assign Siswa</x-ui.button>
                </x-slot:trigger>
                <form method="POST" action="{{ route('admin.enrollment.store') }}" class="space-y-6">
                    @php($enrollment = new \App\Models\Enrollment(['status' => 'active']))
                    @include('pages.admin.enrollment._form')
                    <x-ui.button type="submit" icon="heroicon-m-check">Simpan Enrollment</x-ui.button>
                </form>
            </x-ui.modal>
        </x-slot:actions>
    </x-ui.data-table>
</x-layouts.dashboard>

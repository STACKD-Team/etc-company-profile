@php($statuses = ['active' => 'Aktif', 'completed' => 'Selesai', 'dropped' => 'Berhenti'])

<x-layouts.dashboard title="Data Enrollment" area="admin" active="enrollments">
    @if (session('status'))
        <div class="mb-5 rounded-card border border-green-200 bg-green-50 px-5 py-4 font-heading text-sm font-bold text-green-700">{{ session('status') }}</div>
    @endif

    <x-ui.panel heading="Assign Siswa ke Kelas" description="Workflow manual tetap dipertahankan untuk Sprint 1.">
        <form method="POST" action="{{ route('admin.enrollments.store') }}" class="grid gap-4 lg:grid-cols-[1fr_1fr_180px_160px_auto] lg:items-end">
            @csrf
            <x-ui.select name="user_id" label="Siswa" placeholder="Pilih siswa" :options="$students->mapWithKeys(fn ($student) => [$student->id => $student->full_name ?? $student->name])->all()" required />
            <x-ui.select name="class_id" label="Kelas" placeholder="Pilih kelas" :options="$classes->mapWithKeys(fn ($class) => [$class->id => trim(($class->program?->name ?? '-').' - '.$class->name)])->all()" required />
            <x-ui.field name="enrolled_at" label="Tanggal masuk" type="date" :value="old('enrolled_at', now()->toDateString())" required />
            <x-ui.select name="status" label="Status" :options="$statuses" :value="old('status', 'active')" required />
            <x-ui.button type="submit" icon="heroicon-m-plus">Assign</x-ui.button>
        </form>
    </x-ui.panel>

    <div class="mt-6">
        <x-ui.data-table
            :items="$enrollments"
            :columns="[
                'user_id' => ['label' => 'Siswa', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'user_id', 'options' => $students->mapWithKeys(fn ($student) => [$student->id => $student->full_name ?? $student->name])->all()]],
                'class_id' => ['label' => 'Kelas', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'class_id', 'options' => $classes->mapWithKeys(fn ($class) => [$class->id => trim(($class->program?->name ?? '-').' - '.$class->name)])->all()]],
                'enrolled_at' => ['label' => 'Mulai', 'sortable' => true],
                'completed_at' => ['label' => 'Selesai', 'sortable' => true],
                'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statuses]],
            ]"
            row-view="admin.enrollments.partials.row"
            empty="Belum ada enrollment"
            empty-description="Enrollment akan tampil setelah siswa dimasukkan ke kelas."
            search-placeholder="Cari siswa, email, kelas, atau program"
        />
    </div>
</x-layouts.dashboard>

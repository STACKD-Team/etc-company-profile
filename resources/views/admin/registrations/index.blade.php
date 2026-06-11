@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
@endphp

<x-layouts.dashboard title="Data Pendaftaran" area="admin" active="registrations">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$registrations"
        :columns="[
            'registration_code' => ['label' => 'Kode', 'sortable' => true],
            'applicant_name' => ['label' => 'Pendaftar', 'sortable' => true],
            'program_id' => ['label' => 'Program', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'program_id', 'options' => $programs->pluck('name', 'id')->all()]],
            'preferred_days' => 'Jadwal',
            'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statusLabels]],
            'created_at' => ['label' => 'Tanggal', 'sortable' => true, 'filter' => ['type' => 'date', 'name' => 'created_from']],
            'actions' => 'Aksi',
        ]"
        row-view="admin.registrations.partials.row"
        empty="Belum ada pendaftaran"
        empty-description="Pendaftaran baru akan tampil setelah calon siswa mengirim formulir."
        search-placeholder="Cari nama atau email"
    />
</x-layouts.dashboard>

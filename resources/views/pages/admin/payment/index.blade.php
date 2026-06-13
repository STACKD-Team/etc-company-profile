@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Verifikasi',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
@endphp

<x-layouts.dashboard title="Verifikasi Pembayaran" area="admin" active="payments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$payments"
        :columns="[
            'applicant_name' => ['label' => 'Pendaftar', 'sortable' => true],
            'program_id' => ['label' => 'Program', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'program_id', 'options' => $programs->pluck('name', 'id')->all()]],
            'payment_method' => ['label' => 'Metode', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'payment_method', 'options' => $methods]],
            'payment_amount' => ['label' => 'Nominal', 'sortable' => true],
            'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statusLabels]],
            'paid_at' => ['label' => 'Paid At', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="pages.admin.payment.partials.row"
        empty="Belum ada pembayaran"
        empty-description="Pembayaran yang sudah dikonfirmasi calon siswa akan tampil di sini."
        search-placeholder="Cari nama atau email"
    />
</x-layouts.dashboard>

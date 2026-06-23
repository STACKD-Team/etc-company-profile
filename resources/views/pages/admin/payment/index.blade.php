@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Paid',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];
    $paymentStatusLabels = [
        'waiting_payment' => 'Waiting Payment',
        'paid' => 'Paid',
        'expired' => 'Expired',
        'failed' => 'Failed',
    ];
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet'];
@endphp

<x-layouts.dashboard title="Monitoring Pembayaran" area="admin" active="payments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.data-table
        :items="$payments"
        :columns="[
            'applicant_name' => ['label' => 'Pendaftar', 'sortable' => true],
            'program_id' => ['label' => 'Program', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'program_id', 'options' => $programs->pluck('name', 'id')->all()]],
            'payment_method' => ['label' => 'Metode', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'payment_method', 'options' => $methods]],
            'final_amount' => ['label' => 'Nominal Final', 'sortable' => true],
            'payment_status' => ['label' => 'Status Gateway', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'payment_status', 'options' => $paymentStatusLabels]],
            'status' => ['label' => 'Status Pendaftaran', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statusLabels]],
            'paid_at' => ['label' => 'Paid At', 'sortable' => true],
            'actions' => 'Aksi',
        ]"
        row-view="pages.admin.payment.partials.row"
        empty="Belum ada pembayaran"
        empty-description="Transaksi Midtrans akan tampil setelah pendaftaran dibuat."
        search-placeholder="Cari nama, email, kode, atau order gateway"
    />
</x-layouts.dashboard>

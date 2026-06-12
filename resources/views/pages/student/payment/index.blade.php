<x-layouts.dashboard title="Riwayat Pembayaran" area="student" active="payments" :user="$student">
    <x-ui.data-table
        :items="$payments"
        :columns="[
            'registration' => [
                'label' => 'Pembayaran',
            ],
            'program' => [
                'label' => 'Program',
                'filter' => ['type' => 'autocomplete', 'name' => 'program_id', 'options' => $programOptions],
            ],
            'method' => [
                'label' => 'Metode',
                'filter' => ['type' => 'select', 'name' => 'payment_method', 'options' => $methods],
            ],
            'amount' => [
                'label' => 'Nominal',
                'key' => 'payment_amount',
                'sortable' => true,
            ],
            'status' => [
                'label' => 'Status',
                'key' => 'payment_status',
                'sortable' => true,
                'filter' => ['type' => 'select', 'name' => 'payment_status', 'options' => $paymentStatusOptions],
            ],
            'paid_at' => [
                'label' => 'Dibayar',
                'key' => 'paid_at',
                'sortable' => true,
                'filter' => ['type' => 'date', 'name' => 'paid_at'],
            ],
            'action' => 'Aksi',
        ]"
        row-view="pages.student.payment.partials.row"
        empty="Belum ada riwayat pembayaran"
        empty-description="Pembayaran akan tampil setelah pendaftaran dibuat atau dikonfirmasi."
        search-placeholder="Cari kode, program, pendaftar, gateway, atau promo"
        data-student-payments-table
    />
</x-layouts.dashboard>

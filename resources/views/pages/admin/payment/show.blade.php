@php
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
    $proofUrl = $payment->payment_proof ? app(\App\Services\MediaStorageService::class)->url($payment->payment_proof) : null;
@endphp

<x-layouts.dashboard title="Detail Pembayaran" area="admin" active="payments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$payment->registration_code"
        :subtitle="$payment->applicant_name.' - '.$payment->applicant_email"
        :back-url="route('admin.payment.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$payment->status" />
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.registration.show', ['registration' => $payment])" outlined icon="heroicon-m-clipboard-document-list">Pendaftaran</x-ui.button>
            <x-ui.modal id="verify-payment-modal" heading="Verifikasi Pembayaran" description="Tandai pembayaran sebagai paid setelah bukti/gateway valid." icon="heroicon-o-check-circle">
                <x-slot:trigger>
                    <x-ui.button type="button" icon="heroicon-m-check">Verifikasi</x-ui.button>
                </x-slot:trigger>
                <form method="POST" action="{{ route('admin.payment.verify', ['payment' => $payment]) }}" class="space-y-4">
                    @csrf
                    <x-ui.field name="payment_amount" label="Nominal Koreksi" type="number" min="0" :value="$payment->payment_amount" />
                    <x-ui.select name="payment_method" label="Metode" :value="$payment->payment_method" placeholder="Tidak diubah" :options="$methods" />
                    <x-ui.button type="submit" icon="heroicon-m-check">Verifikasi Paid</x-ui.button>
                </form>
            </x-ui.modal>
            <x-ui.modal id="reject-payment-modal" heading="Tolak Pembayaran" description="Simpan alasan penolakan agar admin berikutnya memahami statusnya." icon="heroicon-o-x-circle" icon-color="danger">
                <x-slot:trigger>
                    <x-ui.button type="button" color="danger" outlined icon="heroicon-m-x-mark">Tolak</x-ui.button>
                </x-slot:trigger>
                <form method="POST" action="{{ route('admin.payment.reject', ['payment' => $payment]) }}" class="space-y-4">
                    @csrf
                    <x-ui.textarea name="notes" label="Alasan" rows="4" required />
                    <x-ui.button type="submit" color="danger" icon="heroicon-m-x-mark">Tolak Pembayaran</x-ui.button>
                </form>
            </x-ui.modal>
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <x-ui.detail-card heading="Ringkasan Pembayaran">
            <x-ui.description-list>
                <x-ui.description-item label="Pendaftar" :value="$payment->applicant_name" />
                <x-ui.description-item label="Program" :value="$payment->program?->name ?? '-'" />
                <x-ui.description-item label="Metode" :value="$methods[$payment->payment_method] ?? ($payment->payment_method ?? '-')" />
                <x-ui.description-item label="Nominal" :value="$payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-'" />
                <x-ui.description-item label="Gateway ID" :value="$payment->payment_gateway_id ?? '-'" />
                <x-ui.description-item label="Paid At" :value="$payment->paid_at?->format('d M Y H:i') ?? '-'" />
            </x-ui.description-list>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Bukti Pembayaran">
            @if ($proofUrl)
                <x-ui.button :href="$proofUrl" target="_blank" outlined icon="heroicon-m-photo">Buka Bukti</x-ui.button>
            @else
                <x-ui.empty-state heading="Belum ada bukti pembayaran" description="Calon siswa belum mengupload bukti atau belum menyelesaikan konfirmasi manual." icon="heroicon-o-photo" />
            @endif
        </x-ui.detail-card>
    </div>

    <x-ui.detail-card heading="Catatan" class="mt-6">
        <p class="whitespace-pre-line text-sm leading-6 text-etc-on-muted">{{ $payment->notes ?: 'Tidak ada catatan.' }}</p>
    </x-ui.detail-card>
</x-layouts.dashboard>

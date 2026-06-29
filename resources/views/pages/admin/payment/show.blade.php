@php
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet'];
    $paymentStatus = $payment->payment_status ?: match ($payment->status) {
        'paid', 'placement_test', 'enrolled' => 'paid',
        'cancelled' => 'expired',
        'rejected' => 'failed',
        default => 'waiting_payment',
    };
    $formatMoney = fn (float|int|string|null $amount): string => $amount !== null && $amount !== '' ? 'Rp '.number_format((float) $amount, 0, ',', '.') : '-';
@endphp

<x-layouts.dashboard title="Monitoring Pembayaran" area="admin" active="payments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        title="Detail Pembayaran"
        :subtitle="$payment->registration_code.' - '.$payment->applicant_name.' - '.$payment->applicant_email"
        :back-url="route('admin.payment.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$paymentStatus">{{ str($paymentStatus)->replace('_', ' ')->headline() }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.registration.show', ['registration' => $payment])" outlined icon="heroicon-m-clipboard-document-list">Pendaftaran</x-ui.button>
            @if ($payment->midtrans_redirect_url && $paymentStatus === 'waiting_payment')
                <x-ui.button :href="$payment->midtrans_redirect_url" target="_blank" outlined icon="heroicon-m-arrow-top-right-on-square">Buka Midtrans</x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.resource-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <x-ui.detail-card heading="Gateway Midtrans" description="Monitoring otomatis dari order dan notification Midtrans.">
                <x-ui.description-list columns="2">
                    <x-ui.description-item label="Order ID" :value="$payment->midtrans_order_id ?: '-'" />
                    <x-ui.description-item label="Transaction/Gateway ID" :value="$payment->payment_gateway_id ?: '-'" />
                    <x-ui.description-item label="Metode" :value="$methods[$payment->payment_method] ?? ($payment->payment_method ?? 'Midtrans')" />
                    <x-ui.description-item label="Status Gateway" :value="str($paymentStatus)->replace('_', ' ')->headline()" />
                    <x-ui.description-item label="Snap Token" :value="$payment->midtrans_snap_token ?: '-'" />
                    <x-ui.description-item label="Batas Pembayaran" :value="$payment->payment_expires_at?->format('d M Y H:i') ?? '-'" />
                    <x-ui.description-item label="Paid At" :value="$payment->paid_at?->format('d M Y H:i') ?? '-'" />
                    <x-ui.description-item label="Status Pendaftaran" :value="str($payment->status)->replace('_', ' ')->headline()" />
                    <x-ui.description-item label="Pesan Gateway" :value="$payment->payment_status_message ?: '-'" class="md:col-span-2" />
                </x-ui.description-list>
            </x-ui.detail-card>

            <x-ui.detail-card heading="Riwayat Notification">
                @if ($payment->midtransNotifications->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="font-heading text-xs uppercase text-etc-on-muted">
                                <tr>
                                    <th class="py-3 pr-4">Diterima</th>
                                    <th class="py-3 pr-4">Status</th>
                                    <th class="py-3 pr-4">Transaksi</th>
                                    <th class="py-3 pr-4">Nominal</th>
                                    <th class="py-3 pr-4">Proses</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-etc-outline-variant">
                                @foreach ($payment->midtransNotifications as $notification)
                                    <tr>
                                        <td class="py-3 pr-4 text-etc-on-muted">{{ $notification->received_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="py-3 pr-4">
                                            <x-ui.badge :status="$notification->transaction_status">{{ str($notification->transaction_status)->replace('_', ' ')->headline() }}</x-ui.badge>
                                            @if ($notification->fraud_status)
                                                <p class="mt-1 text-xs text-etc-on-muted">Fraud: {{ $notification->fraud_status }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-etc-on-muted">{{ $notification->transaction_id ?: '-' }}</td>
                                        <td class="py-3 pr-4 font-semibold text-etc-on-surface">{{ $formatMoney($notification->gross_amount) }}</td>
                                        <td class="py-3 pr-4">
                                            <x-ui.badge :status="$notification->processing_status">{{ str($notification->processing_status)->headline() }}</x-ui.badge>
                                            @if ($notification->error_message)
                                                <p class="mt-1 max-w-md text-xs text-red-600">{{ $notification->error_message }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-ui.empty-state heading="Belum ada notification" description="Midtrans belum mengirim webhook untuk order ini." icon="heroicon-o-bell-alert" />
                @endif
            </x-ui.detail-card>
        </div>

        <aside class="space-y-6">
            <x-ui.detail-card heading="Snapshot Nominal">
                <x-ui.description-list columns="1">
                    <x-ui.description-item label="Nominal Asli" :value="$formatMoney($payment->original_amount ?: $payment->payment_amount)" />
                    <x-ui.description-item label="Promo" :value="$payment->program_promotion_title ?: ($payment->programPromotion?->title ?? '-')" />
                    <x-ui.description-item label="Potongan" :value="$formatMoney($payment->discount_amount ?: 0)" />
                    <x-ui.description-item label="Nominal Final" :value="$formatMoney($payment->final_amount ?: $payment->payment_amount)" />
                </x-ui.description-list>
            </x-ui.detail-card>

            <x-ui.detail-card heading="Aksi Midtrans" description="Pembayaran diproses otomatis melalui Midtrans. Gunakan retry hanya jika transaksi belum memiliki checkout aktif.">
                @if ($payment->midtrans_redirect_url && $paymentStatus === 'waiting_payment')
                    <x-ui.button :href="$payment->midtrans_redirect_url" target="_blank" icon="heroicon-m-arrow-top-right-on-square">Buka Checkout</x-ui.button>
                @else
                    <p class="text-sm leading-6 text-etc-on-muted">Tidak ada aksi manual untuk status ini.</p>
                @endif
            </x-ui.detail-card>
        </aside>
    </div>

    <x-ui.detail-card heading="Catatan" class="mt-6">
        <p class="whitespace-pre-line text-sm leading-6 text-etc-on-muted">{{ $payment->notes ?: 'Tidak ada catatan.' }}</p>
    </x-ui.detail-card>
</x-layouts.dashboard>

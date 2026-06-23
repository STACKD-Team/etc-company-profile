@php
    $statusLabel = $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline();
    $formatMoney = fn (float|int|null $amount): string => 'Rp '.number_format((float) $amount, 0, ',', '.');
    $isWaiting = in_array($summary['status'], ['pending_payment', 'waiting_payment'], true);
@endphp

<x-layouts.dashboard title="Detail Pembayaran" area="student" active="payments" :user="$student">
    <x-ui.resource-header
        title="Detail Pembayaran"
        :subtitle="$payment->registration_code.' - '.($payment->program?->name ?? 'Program ETC Planet')"
        :back-url="route('student.payments.index')"
        back-label="Kembali ke Riwayat"
    >
        <x-slot name="status">
            <x-ui.badge :status="$summary['status']" :color="$summary['color']">{{ $summary['label'] }}</x-ui.badge>
        </x-slot>

        @if ($summary['can_continue'])
            <x-slot name="actions">
                <x-ui.button :href="$summary['redirect_url']" target="_blank" icon="heroicon-m-arrow-top-right-on-square">
                    Lanjutkan Pembayaran
                </x-ui.button>
            </x-slot>
        @endif
    </x-ui.resource-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <x-ui.detail-card heading="Ringkasan Pembayaran" description="Ringkasan status dan data gateway pembayaran siswa.">
                @if ($isWaiting)
                    <x-ui.alert status="warning" title="Menunggu pembayaran" class="mb-6">
                        Pembayaran masih menunggu penyelesaian melalui gateway. Gunakan tombol lanjutkan pembayaran jika masih tersedia.
                    </x-ui.alert>
                @endif

                <x-ui.description-list columns="2">
                    <x-ui.description-item label="Kode Registrasi" :value="$payment->registration_code" />
                    <x-ui.description-item label="Pendaftar" :value="$payment->applicant_name" />
                    <x-ui.description-item label="Program" :value="$payment->program?->name ?? 'Program ETC Planet'" />
                    <x-ui.description-item label="Metode" :value="$summary['method']" />
                    <x-ui.description-item label="Status Gateway" :value="$summary['label']" />
                    <x-ui.description-item label="Gateway ID" :value="$payment->payment_gateway_id" />
                    <x-ui.description-item label="Snap Token" :value="$summary['snap_token']" />
                    <x-ui.description-item label="Tanggal Daftar" :value="$payment->created_at?->format('d M Y H:i')" />
                    <x-ui.description-item label="Dibayar Pada" :value="$payment->paid_at?->format('d M Y H:i')" />
                    <x-ui.description-item label="Batas Pembayaran" :value="$summary['expires_at']?->format('d M Y H:i')" />
                    <x-ui.description-item label="Status Pendaftaran" :value="$statusLabel" />
                    <x-ui.description-item label="Catatan" :value="$payment->notes" class="md:col-span-2" />
                </x-ui.description-list>
            </x-ui.detail-card>
        </div>

        <aside class="space-y-6">
            <x-ui.detail-card heading="Rincian Nominal" description="Snapshot nominal disimpan saat transaksi dibuat.">
                <x-ui.description-list columns="1">
                    <x-ui.description-item label="Nominal asli">
                        <span class="font-semibold text-etc-on-surface">{{ $formatMoney($summary['original_amount']) }}</span>
                    </x-ui.description-item>
                    <x-ui.description-item label="Nama promo" :value="$summary['promotion_title'] ?? '-'" />
                    <x-ui.description-item label="Potongan promo">
                        <span class="font-semibold text-etc-on-surface">{{ $formatMoney($summary['discount_amount']) }}</span>
                    </x-ui.description-item>
                    <x-ui.description-item label="Nominal akhir">
                        <span class="font-heading text-xl font-bold text-etc-magenta">{{ $formatMoney($summary['final_amount']) }}</span>
                    </x-ui.description-item>
                </x-ui.description-list>
            </x-ui.detail-card>
        </aside>
    </div>
</x-layouts.dashboard>

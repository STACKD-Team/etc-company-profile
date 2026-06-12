@php
    $statusLabel = $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline();
    $statusColor = $statusColors[$payment->status] ?? 'primary';
    $originalAmount = (float) ($payment->payment_amount ?? 0);
    $discountAmount = 0;
    $finalAmount = max($originalAmount - $discountAmount, 0);
    $proofUrl = $payment->payment_proof ? app(\App\Services\MediaStorageService::class)->url($payment->payment_proof) : null;
    $isWaiting = in_array($payment->status, ['pending_payment', 'waiting_payment'], true);
    $formatMoney = fn (float|int|null $amount): string => 'Rp '.number_format((float) $amount, 0, ',', '.');
@endphp

<x-layouts.dashboard title="Detail Pembayaran" area="student" active="payments" :user="$student">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <x-ui.panel heading="Detail Pembayaran" description="Ringkasan status dan nominal pembayaran siswa.">
                <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase text-etc-magenta">{{ $payment->registration_code }}</p>
                        <h2 class="mt-2 font-heading text-2xl font-bold text-etc-on-surface">{{ $payment->program?->name ?? 'Program ETC Planet' }}</h2>
                        <p class="mt-1 text-sm text-etc-on-muted">Pendaftar: {{ $payment->applicant_name }}</p>
                    </div>
                    <x-ui.badge :status="$summary['status']" :color="$summary['color']">{{ $summary['label'] }}</x-ui.badge>
                </div>

                @if ($isWaiting)
                    <x-ui.alert status="warning" title="Menunggu pembayaran" class="mb-6">
                        Pembayaran masih menunggu penyelesaian melalui gateway. Gunakan tombol lanjutkan pembayaran jika masih tersedia.
                    </x-ui.alert>
                @endif

                <dl class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Metode</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $summary['method'] }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Status Gateway</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $summary['label'] }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Gateway ID</dt>
                        <dd class="mt-2 break-words text-sm font-semibold text-etc-on-surface">{{ $payment->payment_gateway_id ?? '-' }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Snap Token</dt>
                        <dd class="mt-2 break-words text-sm font-semibold text-etc-on-surface">{{ $payment->payment_snap_token ?? '-' }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Tanggal Daftar</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Dibayar Pada</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Batas Pembayaran</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $summary['expires_at']?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Status Pendaftaran</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline() }}</dd>
                    </div>
                    <div class="rounded-box bg-etc-surface-container p-4 md:col-span-2">
                        <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Catatan</dt>
                        <dd class="mt-2 whitespace-pre-line text-sm text-etc-on-surface">{{ $payment->notes ?? '-' }}</dd>
                    </div>
                </dl>

                @if ($summary['can_continue'])
                    <x-ui.button :href="$payment->payment_redirect_url" target="_blank" class="mt-6" icon="heroicon-m-arrow-top-right-on-square">
                        Lanjutkan Pembayaran
                    </x-ui.button>
                @endif
            </x-ui.panel>

            <x-ui.panel heading="Informasi Legacy" description="Bukti upload lama hanya ditampilkan sebagai arsip, bukan alur pembayaran utama.">
                @if ($proofUrl)
                    <p class="text-sm text-etc-on-muted">File bukti pembayaran lama masih tersedia sebagai arsip.</p>
                    <x-ui.button :href="$proofUrl" target="_blank" outlined class="mt-4" icon="heroicon-m-arrow-top-right-on-square">
                        Buka Arsip Bukti
                    </x-ui.button>
                @else
                    <p class="text-sm text-etc-on-muted">Tidak ada arsip bukti pembayaran manual.</p>
                @endif
            </x-ui.panel>
        </div>

        <aside class="space-y-6">
            <x-ui.panel heading="Rincian Nominal" description="Snapshot nominal disimpan saat transaksi dibuat.">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-etc-on-muted">Nominal asli</dt>
                        <dd class="font-semibold text-etc-on-surface">{{ $formatMoney($summary['original_amount']) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-etc-on-muted">Nama promo</dt>
                        <dd class="text-right font-semibold text-etc-on-surface">{{ $summary['promotion_title'] ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-etc-on-muted">Potongan promo</dt>
                        <dd class="font-semibold text-etc-on-surface">{{ $formatMoney($summary['discount_amount']) }}</dd>
                    </div>
                    <div class="border-t border-etc-outline-variant/60 pt-3">
                        <div class="flex justify-between gap-3">
                            <dt class="font-heading font-bold text-etc-on-surface">Nominal akhir</dt>
                            <dd class="font-heading text-xl font-bold text-etc-magenta">{{ $formatMoney($summary['final_amount']) }}</dd>
                        </div>
                    </div>
                </dl>
            </x-ui.panel>

            <x-ui.button :href="route('student.payments.index')" outlined class="w-full" icon="heroicon-m-arrow-left">
                Kembali ke Riwayat
            </x-ui.button>
        </aside>
    </div>
</x-layouts.dashboard>

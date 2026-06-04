@php
    $methods = [
        'qris' => 'QRIS',
        'bank_transfer' => 'Transfer Bank',
        'virtual_account' => 'Virtual Account',
        'ewallet' => 'E-Wallet',
        'manual' => 'Manual',
    ];
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Terverifikasi',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
    $proofUrl = $payment->payment_proof ? \Illuminate\Support\Facades\Storage::url($payment->payment_proof) : null;
@endphp

<x-layouts.dashboard title="Detail Pembayaran" area="student" active="payments" :user="$student">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <section class="rounded-card bg-white p-6 shadow-panel">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Detail Pembayaran</p>
                    <h2 class="mt-2 font-heading text-2xl font-bold text-etc-on-surface">{{ $payment->registration_code }}</h2>
                    <p class="mt-1 text-sm text-etc-on-muted">{{ $payment->program?->name ?? 'Program ETC Planet' }}</p>
                </div>
                <span class="w-fit rounded-pill bg-etc-surface-container px-4 py-2 font-heading text-xs font-bold text-etc-on-surface">
                    {{ $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline() }}
                </span>
            </div>

            <dl class="grid gap-4 md:grid-cols-2">
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Nama Pendaftar</dt>
                    <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->applicant_name }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Program</dt>
                    <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->program?->name ?? '-' }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Metode</dt>
                    <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? '-') }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Nominal</dt>
                    <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-' }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Tanggal Daftar</dt>
                    <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Dibayar Pada</dt>
                    <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4 md:col-span-2">
                    <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">Catatan</dt>
                    <dd class="mt-2 whitespace-pre-line text-sm text-etc-on-surface">{{ $payment->notes ?? '-' }}</dd>
                </div>
            </dl>
        </section>

        <aside class="space-y-6">
            <section class="rounded-card bg-white p-6 shadow-panel">
                <h3 class="font-heading text-lg font-bold text-etc-on-surface">Bukti Pembayaran</h3>
                @if ($proofUrl)
                    <p class="mt-2 text-sm text-etc-on-muted">File bukti pembayaran yang sudah diupload saat pendaftaran.</p>
                    <a href="{{ $proofUrl }}" target="_blank" class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white">
                        Buka Bukti Upload
                    </a>
                @else
                    <p class="mt-2 text-sm text-etc-on-muted">Belum ada bukti pembayaran yang diupload.</p>
                @endif
            </section>

            <a href="{{ route('student.payments.index') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-pill border border-etc-outline-variant px-5 py-3 font-heading text-sm font-bold text-etc-on-surface">
                Kembali ke Riwayat
            </a>
        </aside>
    </div>
</x-layouts.dashboard>

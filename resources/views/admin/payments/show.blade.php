@php
    $methods = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'manual' => 'Manual'];
    $proofUrl = $payment->payment_proof ? \Illuminate\Support\Facades\Storage::url($payment->payment_proof) : null;
    $summary = [
        'Pendaftar' => $payment->applicant_name,
        'Program' => $payment->program?->name ?? '-',
        'Metode' => $methods[$payment->payment_method] ?? ($payment->payment_method ?? '-'),
        'Nominal' => $payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-',
        'Gateway ID' => $payment->payment_gateway_id ?? '-',
        'Paid At' => $payment->paid_at?->format('d M Y H:i') ?? '-',
    ];
@endphp

<x-layouts.dashboard title="Detail Pembayaran" area="admin" active="payments">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <x-ui.panel>
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                    <div>
                        <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Pembayaran</p>
                        <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $payment->registration_code }}</h2>
                        <p class="mt-2 text-sm text-etc-on-muted">{{ $payment->applicant_email }} - {{ $payment->applicant_phone }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-ui.badge :status="$payment->status" />
                        <x-ui.button :href="route('admin.registrations.show', ['registration' => $payment])" outlined>Detail Pendaftaran</x-ui.button>
                    </div>
                </div>
            </x-ui.panel>

            <x-ui.panel heading="Ringkasan Pembayaran">
                <dl class="divide-y-2 divide-etc-outline-variant/60">
                    @foreach ($summary as $label => $value)
                        <div class="grid gap-1 py-3 md:grid-cols-[150px_1fr] md:gap-4">
                            <dt class="font-heading text-xs font-bold uppercase text-etc-on-muted">{{ $label }}</dt>
                            <dd class="text-sm text-etc-on-surface">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.panel>

            <x-ui.panel heading="Bukti Pembayaran" description="Flow manual tetap dipertahankan sampai Midtrans Sprint 2 aktif.">
                @if ($proofUrl)
                    <div class="flex flex-col gap-4 rounded-lg border border-etc-outline-variant/70 p-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-heading font-bold text-etc-on-surface">Bukti upload tersedia</p>
                            <p class="mt-1 text-sm text-etc-on-muted">Buka file untuk memeriksa transfer sebelum verifikasi.</p>
                        </div>
                        <x-ui.button :href="$proofUrl" target="_blank" outlined>Buka Bukti</x-ui.button>
                    </div>
                @else
                    <x-ui.empty-state heading="Belum ada bukti pembayaran" description="Calon siswa belum mengupload bukti atau belum menyelesaikan konfirmasi manual." icon="heroicon-o-photo" />
                @endif
            </x-ui.panel>

            <x-ui.panel heading="Catatan">
                <p class="whitespace-pre-line text-sm leading-6 text-etc-on-muted">{{ $payment->notes ?: 'Tidak ada catatan.' }}</p>
            </x-ui.panel>
        </div>

        <aside class="space-y-6">
            <x-ui.panel heading="Verifikasi">
                <form method="POST" action="{{ route('admin.payments.verify', ['payment' => $payment]) }}" class="space-y-4">
                    @csrf
                    <x-ui.field name="payment_amount" label="Nominal Koreksi" type="number" min="0" :value="$payment->payment_amount" />
                    <x-ui.select name="payment_method" label="Metode" :value="$payment->payment_method" placeholder="Tidak diubah" :options="$methods" />
                    <x-ui.button type="submit" class="w-full">Verifikasi Paid</x-ui.button>
                </form>
            </x-ui.panel>

            <x-ui.panel heading="Tolak Pembayaran">
                <form method="POST" action="{{ route('admin.payments.reject', ['payment' => $payment]) }}" class="space-y-4">
                    @csrf
                    <x-ui.textarea name="notes" label="Alasan" rows="4" />
                    <x-ui.button type="submit" color="danger" outlined class="w-full">Tolak</x-ui.button>
                </form>
            </x-ui.panel>
        </aside>
    </div>
</x-layouts.dashboard>

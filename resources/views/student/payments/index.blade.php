@php
    $statusLabels = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Terverifikasi',
        'placement_test' => 'Placement Test',
        'enrolled' => 'Enrolled',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
    $methods = [
        'qris' => 'QRIS',
        'bank_transfer' => 'Transfer Bank',
        'virtual_account' => 'Virtual Account',
        'ewallet' => 'E-Wallet',
        'manual' => 'Manual',
    ];
@endphp

<x-layouts.dashboard title="Riwayat Pembayaran" area="student" active="payments" :user="$student">
    <section class="rounded-card bg-white p-6 shadow-panel">
        <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Pembayaran</p>
                <h2 class="mt-2 font-heading text-2xl font-bold text-etc-on-surface">Riwayat Pembayaran</h2>
                <p class="mt-1 text-sm text-etc-on-muted">Pantau status pembayaran pendaftaran dan program ETC Planet.</p>
            </div>
        </div>

        <div class="grid gap-4">
            @forelse ($payments as $payment)
                <article class="rounded-card border border-etc-outline-variant bg-white p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="min-w-0">
                            <p class="font-heading text-xs font-bold uppercase text-etc-magenta">{{ $payment->registration_code }}</p>
                            <h3 class="mt-2 truncate font-heading text-xl font-bold text-etc-on-surface">{{ $payment->program?->name ?? 'Program ETC Planet' }}</h3>
                            <p class="mt-2 text-sm text-etc-on-muted">
                                {{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? 'Metode belum dipilih') }}
                                <span class="mx-2">-</span>
                                {{ $payment->created_at?->format('d M Y') ?? '-' }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 md:items-end">
                            <strong class="font-heading text-xl font-bold text-etc-on-surface">
                                {{ $payment->payment_amount ? 'Rp '.number_format((float) $payment->payment_amount, 0, ',', '.') : '-' }}
                            </strong>
                            <span class="w-fit rounded-pill bg-etc-surface-container px-3 py-1 font-heading text-xs font-bold text-etc-on-surface">
                                {{ $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline() }}
                            </span>
                            <a href="{{ route('student.payments.show', ['payment' => $payment]) }}" class="inline-flex min-h-10 items-center justify-center rounded-pill bg-etc-magenta px-5 py-2 font-heading text-sm font-bold text-white">
                                Detail
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-card bg-etc-surface-low p-8 text-center">
                    <h3 class="font-heading text-xl font-bold text-etc-on-surface">Belum ada riwayat pembayaran.</h3>
                    <p class="mt-2 text-sm text-etc-on-muted">Pembayaran akan tampil setelah pendaftaran dibuat atau dikonfirmasi.</p>
                </div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$payments" class="mt-6" />
    </section>
</x-layouts.dashboard>

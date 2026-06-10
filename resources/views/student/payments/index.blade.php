<x-layouts.dashboard title="Riwayat Pembayaran" area="student" active="payments" :user="$student">
    <x-ui.panel heading="Riwayat Pembayaran" description="Pantau status pembayaran pendaftaran dan program ETC Planet.">
        <div class="grid gap-4">
            @forelse ($payments as $payment)
                @php
                    $originalAmount = (float) ($payment->payment_amount ?? 0);
                    $discountAmount = 0;
                    $finalAmount = max($originalAmount - $discountAmount, 0);
                    $statusLabel = $statusLabels[$payment->status] ?? str($payment->status)->replace('_', ' ')->headline();
                    $statusColor = $statusColors[$payment->status] ?? 'primary';
                @endphp
                <article class="rounded-card border border-etc-outline-variant/70 bg-white p-5">
                    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_280px] xl:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.badge :status="$payment->status" :color="$statusColor">{{ $statusLabel }}</x-ui.badge>
                                <x-ui.badge status="info">{{ $methods[$payment->payment_method] ?? ($payment->payment_method ?? 'Metode belum dipilih') }}</x-ui.badge>
                            </div>
                            <p class="mt-3 font-heading text-xs font-bold uppercase text-etc-magenta">{{ $payment->registration_code }}</p>
                            <h3 class="mt-2 truncate font-heading text-xl font-bold text-etc-on-surface">{{ $payment->program?->name ?? 'Program ETC Planet' }}</h3>
                            <p class="mt-1 text-sm text-etc-on-muted">Pendaftar: {{ $payment->applicant_name }} - {{ $payment->created_at?->format('d M Y') ?? '-' }}</p>
                        </div>

                        <div class="rounded-card bg-etc-surface-low p-4">
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-etc-on-muted">Nominal asli</dt>
                                    <dd class="font-semibold text-etc-on-surface">{{ $originalAmount > 0 ? 'Rp '.number_format($originalAmount, 0, ',', '.') : '-' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-etc-on-muted">Promo</dt>
                                    <dd class="font-semibold text-etc-on-surface">-</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-etc-on-muted">Potongan</dt>
                                    <dd class="font-semibold text-etc-on-surface">Rp 0</dd>
                                </div>
                                <div class="border-t border-etc-outline-variant/60 pt-2">
                                    <div class="flex justify-between gap-3">
                                        <dt class="font-heading font-bold text-etc-on-surface">Nominal akhir</dt>
                                        <dd class="font-heading font-black text-etc-magenta">{{ $finalAmount > 0 ? 'Rp '.number_format($finalAmount, 0, ',', '.') : '-' }}</dd>
                                    </div>
                                </div>
                            </dl>
                            <x-ui.button :href="route('student.payments.show', ['payment' => $payment])" class="mt-4 w-full" icon="heroicon-m-eye">
                                Detail Pembayaran
                            </x-ui.button>
                        </div>
                    </div>
                </article>
            @empty
                <x-ui.empty-state
                    heading="Belum ada riwayat pembayaran"
                    description="Pembayaran akan tampil setelah pendaftaran dibuat atau dikonfirmasi."
                    icon="heroicon-o-credit-card"
                />
            @endforelse
        </div>

        <div class="mt-6">{{ $payments->withQueryString()->links() }}</div>
    </x-ui.panel>
</x-layouts.dashboard>

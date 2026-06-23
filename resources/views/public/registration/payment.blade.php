<x-layouts.public title="Pembayaran" :show-navbar="false" :show-footer="false" :show-chatbot="false">
<x-public-discovery.navbar active="program" />
<link rel="stylesheet" href="{{ asset('css/pembayaran.css') }}">

@php
    $isWaiting = in_array($paymentSummary['status'], ['waiting_payment', 'pending_payment'], true);
    $canPay = $paymentSummary['redirectUrl'] && $isWaiting && (! $paymentSummary['expiresAt'] || $paymentSummary['expiresAt']->isFuture());
@endphp

<header class="page-header">
    <p class="page-kicker">Pembayaran Midtrans</p>
    <h1>Pendaftaran ETC Planet</h1>
    <p>Selesaikan transaksi melalui Midtrans. Status pembayaran akan berubah otomatis setelah gateway mengirim notifikasi.</p>

    <div class="stepper" aria-label="Progress pendaftaran">
        <div class="step done">
            <div class="step-circle">&#10003;</div>
            <div class="step-label">Pilih Program</div>
        </div>
        <div class="step-line done"></div>
        <div class="step done">
            <div class="step-circle">&#10003;</div>
            <div class="step-label">Data Pribadi</div>
        </div>
        <div class="step-line done"></div>
        <div class="step active">
            <div class="step-circle">3</div>
            <div class="step-label">Pembayaran</div>
        </div>
        <div class="step-line"></div>
        <div class="step pending">
            <div class="step-circle">4</div>
            <div class="step-label">Konfirmasi</div>
        </div>
    </div>
</header>

@if (session('status'))
    <div class="payment-alert">
        <x-ui.alert status="success">{{ session('status') }}</x-ui.alert>
    </div>
@endif

<main class="payment-layout payment-layout--midtrans">
    <aside class="summary-card">
        <h2>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2h8v4H8z"/><path d="M6 4H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1"/><path d="M8 11h8M8 15h5"/></svg>
            Ringkasan Pesanan
        </h2>

        <div class="program-box">
            <span>Kode Pendaftaran</span>
            <strong>{{ $registration->registration_code }}</strong>
        </div>

        <div class="program-box">
            <span>Program</span>
            <strong>{{ $paymentSummary['program'] }}</strong>
        </div>

        <div class="summary-row"><span>Biaya Pendaftaran</span><strong>{{ $paymentSummary['registrationFee'] }}</strong></div>
        <div class="summary-row"><span>Biaya Program</span><strong>{{ $paymentSummary['programFee'] }}</strong></div>
        @if ((float) $registration->discount_amount > 0)
            <div class="summary-row"><span>Promo</span><strong>{{ $paymentSummary['promotionTitle'] ?? 'Promo aktif' }}</strong></div>
            <div class="summary-row"><span>Potongan</span><strong>- {{ $paymentSummary['discountAmount'] }}</strong></div>
        @endif

        <div class="summary-total">
            <strong>Total Midtrans</strong>
            <span>{{ $paymentSummary['finalAmount'] }}</span>
        </div>

        <div class="summary-note">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            <p>Status saat ini: {{ str($paymentSummary['status'])->replace('_', ' ')->headline() }}.</p>
        </div>
    </aside>

    <section class="payment-content">
        <article class="midtrans-card">
            <div class="method-head">
                <div class="method-icon qris-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4z"/><path d="M14 14h2v2h-2zM18 14h2v2h-2zM14 18h2v2h-2zM18 18h2v2h-2z"/></svg>
                </div>
                <div class="method-copy">
                    <p class="section-kicker">Gateway otomatis</p>
                    <h2 class="section-title">Midtrans Checkout</h2>
                    <p>Bayar dengan QRIS, virtual account, e-wallet, atau metode lain yang tersedia di Midtrans.</p>
                </div>
            </div>

            <div class="midtrans-status">
                <span class="timer">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2M9 2h6"/></svg>
                    {{ $paymentSummary['expiresAt'] ? 'Batas: '.$paymentSummary['expiresAt']->format('d M Y H:i') : 'Menunggu pembayaran' }}
                </span>
                <span class="gateway-code">{{ $registration->midtrans_order_id ?: 'Order sedang disiapkan' }}</span>
            </div>

            <div class="payment-actions">
                @if ($canPay)
                    <x-ui.button :href="$paymentSummary['redirectUrl']" target="_blank" rel="noopener" size="xl" class="w-full max-w-[432px]" icon="heroicon-m-arrow-right" icon-position="after">
                        Lanjutkan ke Midtrans
                    </x-ui.button>
                @elseif ($paymentSummary['redirectUrl'] && $paymentSummary['status'] === 'paid')
                    <x-ui.alert status="success" class="payment-alert--inline">Pembayaran sudah diterima. Kamu bisa melihat status pendaftaran di halaman konfirmasi.</x-ui.alert>
                @else
                    <x-ui.alert status="warning" class="payment-alert--inline">Transaksi Midtrans belum tersedia atau sudah tidak aktif. Hubungi admin ETC Planet untuk bantuan.</x-ui.alert>
                @endif
                <x-ui.button :href="route('registrations.confirmation.show', ['registration' => $registration])" color="gray" outlined size="xl" class="w-full max-w-[432px]">
                    Lihat Status Pendaftaran
                </x-ui.button>
            </div>
        </article>
    </section>
</main>

<x-public-discovery.page-end />
</x-layouts.public>

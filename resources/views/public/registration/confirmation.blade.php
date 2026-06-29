<x-layouts.public title="Konfirmasi Pendaftaran" :show-navbar="false" :show-footer="false" :show-chatbot="false">
    <x-public-discovery.navbar active="program" />
    <link rel="stylesheet" href="{{ asset('css/konfirmasi.css') }}">

    @php
        $paymentStatus = $registrationDetail['paymentStatus'];
        $canContinue = $registrationDetail['redirectUrl']
            && in_array($paymentStatus, ['waiting_payment', 'pending_payment'], true)
            && (! $registrationDetail['expiresAt'] || $registrationDetail['expiresAt']->isFuture());
    @endphp

    <div class="page-shell">
        <main class="confirmation-main">
            <div class="decor decor-left"></div>
            <div class="decor decor-right"></div>

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
                <div class="step done">
                    <div class="step-circle">&#10003;</div>
                    <div class="step-label">Pembayaran</div>
                </div>
                <div class="step-line done"></div>
                <div class="step active">
                    <div class="step-circle">4</div>
                    <div class="step-label">Konfirmasi</div>
                </div>
            </div>

            <section class="success-hero success-hero--{{ $confirmationCopy['tone'] }}">
                <div class="success-icon">
                    <span class="material-symbols-outlined">{{ $confirmationCopy['icon'] }}</span>
                </div>
                <x-ui.badge :status="$paymentStatus" size="lg" class="status-pill">{{ str($paymentStatus)->replace('_', ' ')->headline() }}</x-ui.badge>
                <h1>{{ $confirmationCopy['title'] }}</h1>
                <p>{{ $confirmationCopy['body'] }}</p>
            </section>

            <section class="detail-card">
                <div class="detail-card__head">
                    <div>
                        <p>Detail transaksi</p>
                        <h2>Ringkasan Pendaftaran</h2>
                    </div>
                    <strong>{{ $registrationDetail['amount'] }}</strong>
                </div>

                <div class="detail-grid">
                    <div class="detail-row">
                        <span>Nama Siswa</span>
                        <strong>{{ $registrationDetail['studentName'] }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>Program Terpilih</span>
                        <strong>{{ $registrationDetail['programName'] }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>ID Pendaftaran</span>
                        <strong><code>{{ $registrationDetail['registrationCode'] }}</code></strong>
                    </div>
                    <div class="detail-row">
                        <span>Status Pendaftaran</span>
                        <strong>{{ str($registrationDetail['status'])->replace('_', ' ')->title() }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>Status Gateway</span>
                        <strong>{{ str($paymentStatus)->replace('_', ' ')->title() }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>Dibayar Pada</span>
                        <strong>{{ $registrationDetail['paidAt']?->format('d M Y H:i') ?? '-' }}</strong>
                    </div>
                </div>
            </section>

            <section class="info-box info-box--{{ $confirmationCopy['tone'] }}">
                <div class="info-icon">
                    <span class="material-symbols-outlined">info</span>
                </div>
                <div>
                    <h3>Langkah berikutnya</h3>
                    <p>{{ $confirmationCopy['next'] }}</p>
                </div>
            </section>

            <div class="action-row">
                <x-ui.button :href="$receiptUrl" color="gray" outlined size="xl" class="w-full" icon="heroicon-m-arrow-down-tray">
                    Unduh Bukti Pendaftaran
                </x-ui.button>
                @if ($canContinue)
                    <x-ui.button :href="$registrationDetail['redirectUrl']" target="_blank" rel="noopener" size="xl" class="w-full" icon="heroicon-m-arrow-right" icon-position="after">
                        Lanjutkan Pembayaran
                    </x-ui.button>
                @else
                    <x-ui.button :href="route('public.home')" size="xl" class="w-full" icon="heroicon-m-arrow-right" icon-position="after">
                        Kembali ke Beranda
                    </x-ui.button>
                @endif
            </div>
        </main>
    </div>
    <x-public-discovery.page-end />
</x-layouts.public>

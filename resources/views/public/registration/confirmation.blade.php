<x-layouts.public title="Konfirmasi Pendaftaran">
    <link rel="stylesheet" href="{{ asset('css/konfirmasi.css') }}">
    <link rel="stylesheet" href="{{ asset('css/interactive.css') }}">

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

            <section class="success-hero">
                <div class="success-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h1>Pendaftaran Berhasil!</h1>
                <p>Terima kasih telah mendaftar di ETC Planet. Langkah pertamamu menuju kelancaran berbahasa dimulai di sini.</p>
            </section>

            <section class="detail-card">
                <h2>Detail Pendaftaran</h2>
                <div class="detail-row">
                    <span>Nama Siswa</span>
                    <strong>{{ $registrationDetail['studentName'] }}</strong>
                </div>
                <div class="detail-row">
                    <span>Program Terpilih</span>
                    <strong><em>{{ $registrationDetail['programName'] }}</em></strong>
                </div>
                <div class="detail-row">
                    <span>ID Pendaftaran</span>
                    <strong><code>{{ $registrationDetail['registrationCode'] }}</code></strong>
                </div>
            </section>

            <section class="info-box">
                <div class="info-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                </div>
                <div>
                    <h3>Informasi Penting:</h3>
                    <p>Placement Test akan dijadwalkan oleh admin ETC Planet. Pantau email kamu secara berkala untuk instruksi selanjutnya.</p>
                </div>
            </section>

            <div class="action-row">
                <button class="btn-unduh" type="button">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg>
                    Unduh Bukti Pendaftaran
                </button>
                <button class="btn-dashboard" type="button">
                    Ke Dashboard
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </button>
            </div>
        </main>
    </div>
</x-layouts.public>

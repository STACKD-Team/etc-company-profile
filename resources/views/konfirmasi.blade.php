<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Konfirmasi Pendaftaran - ETC Planet</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/konfirmasi.css') }}">
    <link rel="stylesheet" href="{{ asset('css/interactive.css') }}">
</head>
<body>
<div class="page-shell">
    <nav>
        <div class="nav-brand">
            <strong>ETC Planet</strong>
        </div>
        <div class="nav-links">
            <a href="#">Program</a>
            <a href="#">Tentang Kami</a>
            <a href="#">Testimoni</a>
            <a href="#">Kontak</a>
        </div>
        <div class="nav-actions">
            <button class="btn-masuk" type="button">Masuk</button>
            <button class="btn-daftar" type="button">Daftar Sekarang</button>
        </div>
    </nav>

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
            <h1>Pendaftaran Berhasil! <span>🎉</span></h1>
            <p>Terima kasih telah mendaftar di ETC Planet. Langkah pertamamu menuju kelancaran berbahasa dimulai di sini.</p>
        </section>

        <section class="detail-card">
            <h2>Detail Pendaftaran</h2>
            <div class="detail-row">
                <span>Nama Siswa</span>
                <strong>Budi Santoso</strong>
            </div>
            <div class="detail-row">
                <span>Program Terpilih</span>
                <strong><em>General English</em></strong>
            </div>
            <div class="detail-row">
                <span>ID Pendaftaran</span>
                <strong><code>ETC-2024-89012A</code></strong>
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

    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <strong>ETC Planet</strong>
                </div>
                <p>Lembaga kursus bahasa Inggris terpercaya dengan metode pembelajaran modern dan interaktif untuk semua usia.</p>
            </div>
            <div class="footer-col">
                <h4>Program</h4>
                <a href="#">General English</a>
                <a href="#">TOEFL Preparation</a>
                <a href="#">IELTS Preparation</a>
                <a href="#">English for Kids</a>
            </div>
            <div class="footer-col">
                <h4>Kontak</h4>
                <p class="contact-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Jl. S. Parman No. 202B, Padang, 25132</span>
                </p>
                <p class="contact-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.91.32 1.8.59 2.65a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.25-1.25a2 2 0 0 1 2.11-.45c.85.27 1.74.47 2.65.59A2 2 0 0 1 22 16.92Z"/></svg>
                    <span>+62 0811-6036-969</span>
                </p>
                <p class="contact-item">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <span>halo@etcplanet.com</span>
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2024 ETC Planet. All rights reserved.</span>
            <div class="footer-social">
                <span>◎</span>
                <span>⌯</span>
            </div>
        </div>
    </footer>
</div>
</body>
</html>

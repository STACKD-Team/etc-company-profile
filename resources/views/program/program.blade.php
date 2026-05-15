
@extends('program.appProgram')

@section('content')
<main class="program-page">
    <section class="program-hero">
        <div class="container program-container">
            <nav class="mb-4">
        <ol class="breadcrumb mb-0 program-breadcrumb">
            <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Program</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">English Conversation</li>
        </ol>
            </nav>

            <div class="hero-copy">
                <div class="program-badges">
                    <span class="program-badge primary">English</span>
                    <span class="program-badge">Reguler</span>
                    <span class="program-badge">Remaja & Dewasa</span>
                </div>

                <h1 class="program-title">English Conversation</h1>
                <p class="program-lead">
                    Tingkatkan kepercayaan diri Anda dalam berkomunikasi dengan bahasa Inggris yang natural dan lancar melalui metode pembelajaran interaktif.
                </p>
            </div>
        </div>
    </section>

    <section class="program-content">
        <div class="container program-container">
            <div class="program-layout">
                <div class="program-main">
                    <article class="program-card about-card">
                        <h2>Tentang Program</h2>
                        <p>
                            Program English Conversation didesain khusus untuk Anda yang ingin fokus pada kemampuan berbicara dan mendengar. Dengan pendekatan komunikatif, kelas ini akan memaksa Anda untuk lebih banyak berlatih speaking dalam berbagai konteks situasi sehari-hari maupun profesional.
                        </p>
                        <p>
                            Materi yang diajarkan selalu relevan dan up-to-date, dipandu oleh instruktur berpengalaman yang akan memberikan feedback konstruktif untuk memperbaiki pelafalan (pronunciation) dan tata bahasa (grammar) Anda secara natural.
                        </p>
                    </article>

                    <article class="program-card learn-card">
                        <h2>Yang Akan Kamu Pelajari</h2>
                        <div class="learn-grid">
                            <div class="learn-item">
                                <i class="fa-solid fa-check"></i>
                                <span>Membangun kosakata untuk percakapan sehari-hari.</span>
                            </div>
                            <div class="learn-item">
                                <i class="fa-solid fa-check"></i>
                                <span>Teknik pelafalan (pronunciation) yang akurat dan natural.</span>
                            </div>
                            <div class="learn-item">
                                <i class="fa-solid fa-check"></i>
                                <span>Merespon dengan cepat dan tepat dalam dialog interaktif.</span>
                            </div>
                            <div class="learn-item">
                                <i class="fa-solid fa-check"></i>
                                <span>Simulasi diskusi kelompok dan presentasi ringan.</span>
                            </div>
                        </div>
                    </article>

                    <div class="info-grid">
                        <article class="program-card info-card schedule-card">
                            <div class="icon-circle">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <h2>Jadwal Kelas</h2>
                            <p class="schedule-day">Senin - Kamis</p>
                            <p class="schedule-time">16:00 - 17:30 WIB</p>
                        </article>

                        <article class="program-card info-card instructor-card">
                            <div class="instructor-photo">
                                <span>SJ</span>
                            </div>
                            <div>
                                <p class="instructor-label">Instruktur Utama</p>
                                <h2>Sarah Johnson, M.Ed.</h2>
                                <p class="rating"><i class="fa-solid fa-star"></i> 4.9/5</p>
                            </div>
                        </article>
                    </div>
                </div>

                <aside class="price-card">
                    <div class="price-body">
                        <p class="price-label">Investasi Pembelajaran</p>
                        <h2 class="price-value"><span>Rp</span> 200.000</h2>
                        <p class="price-subtitle">Biaya Pendaftaran / Siswa</p>
                        <hr>
                        <button class="program-cta" type="button">
                            Daftar Program Ini <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <p class="price-note">Termasuk modul pembelajaran dasar & akses ke komunitas eksklusif.</p>
                    </div>

                    <div class="benefit-panel">
                        <ul>
                            <li><i class="fa-regular fa-circle-check"></i> Placement Test Gratis</li>
                            <li><i class="fa-solid fa-award"></i> Sertifikat Penyelesaian</li>
                            <li><i class="fa-solid fa-headset"></i> Dukungan Akademik 1-on-1</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>
@endsection

@extends('dashboardAdmin.appDashboardA')

@section('title', 'Dashboard Admin')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Dashboard Admin</h1>
            <p class="page-subtitle">Ringkasan aktivitas dan pendaftaran hari ini.</p>
        </div>
        <div class="avatar" aria-label="Admin ETC">A</div>
    </header>

    <section class="stat-grid" aria-label="Ringkasan data admin">
        <article class="stat-card">
            <div>
                <p class="stat-label">Total Siswa</p>
                <p class="stat-value">1,248</p>
                <p class="stat-note"><i class="fa-solid fa-arrow-trend-up"></i> +12% bulan ini</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-users"></i>
            </div>
        </article>

        <article class="stat-card">
            <div>
                <p class="stat-label">Pendaftaran Baru</p>
                <p class="stat-value">84</p>
                <p class="stat-note"><i class="fa-solid fa-arrow-trend-up"></i> +5% minggu ini</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </article>

        <article class="stat-card">
            <div>
                <p class="stat-label">Pendapatan</p>
                <p class="stat-value">Rp 45M</p>
                <p class="stat-note"><i class="fa-solid fa-arrow-trend-up"></i> +22% dari target</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </article>

        <article class="stat-card">
            <div>
                <p class="stat-label">Kelas Aktif</p>
                <p class="stat-value">32</p>
                <p class="stat-note neutral">&ndash; Stabil</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </article>
    </section>

    <section class="action-row" aria-label="Aksi cepat">
        <button class="action-btn primary" type="button">
            <i class="fa-solid fa-user-plus me-1"></i>
            Tambah Siswa
        </button>
        <button class="action-btn" type="button">
            <i class="fa-solid fa-video me-1"></i>
            Upload Reels
        </button>
        <button class="action-btn" type="button">
            <i class="fa-regular fa-file-lines me-1"></i>
            Generate Rapor
        </button>
    </section>

    <section class="table-card">
        <div class="section-header">
            <h2>Pendaftaran Terbaru</h2>
            <a class="section-link" href="#">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td><strong>Budi Santoso</strong></td>
                    <td><span class="program-pill">Intensive English</span></td>
                    <td>
                        <span class="status-pill paid">
                            <span class="status-dot"></span>
                            Lunas
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="icon-btn" type="button" aria-label="Lihat Budi Santoso"><i class="fa-regular fa-eye"></i></button>
                            <button class="icon-btn" type="button" aria-label="Edit Budi Santoso"><i class="fa-solid fa-pen"></i></button>
                            <button class="icon-btn" type="button" aria-label="Hapus Budi Santoso"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>002</td>
                    <td><strong>Siti Aminah</strong></td>
                    <td><span class="program-pill">TOEFL Prep</span></td>
                    <td>
                        <span class="status-pill pending">
                            <span class="status-dot"></span>
                            Pending
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="icon-btn" type="button" aria-label="Lihat Siti Aminah"><i class="fa-regular fa-eye"></i></button>
                            <button class="icon-btn" type="button" aria-label="Edit Siti Aminah"><i class="fa-solid fa-pen"></i></button>
                            <button class="icon-btn" type="button" aria-label="Hapus Siti Aminah"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>003</td>
                    <td><strong>Andi Wijaya</strong></td>
                    <td><span class="program-pill">Kids English</span></td>
                    <td>
                        <span class="status-pill cancelled">
                            <span class="status-dot"></span>
                            Dibatalkan
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="icon-btn" type="button" aria-label="Lihat Andi Wijaya"><i class="fa-regular fa-eye"></i></button>
                            <button class="icon-btn" type="button" aria-label="Edit Andi Wijaya"><i class="fa-solid fa-pen"></i></button>
                            <button class="icon-btn" type="button" aria-label="Hapus Andi Wijaya"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
@endsection

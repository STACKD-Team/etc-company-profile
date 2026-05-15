<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin') - ETC Planet Learner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div>
                <p class="brand-title">Student Portal</p>
                <p class="brand-subtitle">ETC Planet Learner</p>
            </div>

            <nav class="sidebar-nav" aria-label="Navigasi admin">
                <a class="nav-item active" href="{{ url('/dashboard-admin') }}">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-item" href="#">
                    <i class="fa-regular fa-user"></i>
                    <span>Profil Saya</span>
                </a>
                <a class="nav-item" href="#">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Kelas Saya</span>
                </a>
                <a class="nav-item" href="#">
                    <i class="fa-regular fa-star"></i>
                    <span>Rapor</span>
                </a>
                <a class="nav-item" href="#">
                    <i class="fa-regular fa-credit-card"></i>
                    <span>Riwayat Pembayaran</span>
                </a>
            </nav>

            <button class="sidebar-help" type="button">Bantuan</button>
        </aside>

        <main class="main-panel">
            <div class="content-wrap">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>

# Tahap Implementasi Dashboard Admin dari Nol

Dokumen ini menjelaskan langkah yang sudah kita lakukan untuk membuat halaman dashboard admin Laravel dari awal. Bahasanya dibuat sederhana supaya bisa dipakai sebagai catatan belajar.

## 1. Mengenal Struktur Project Laravel

Laravel punya beberapa folder penting:

```text
routes/
resources/views/
public/
database/
app/
```

Untuk tahap awal ini, kita fokus ke dua bagian:

```text
routes/web.php
resources/views/
```

Penjelasannya:

- `routes/web.php` adalah tempat mendaftarkan alamat halaman website.
- `resources/views/` adalah tempat menyimpan tampilan HTML yang memakai Blade.

Blade adalah template engine Laravel. File Blade biasanya berakhiran:

```text
.blade.php
```

Contoh:

```text
dashboardA.blade.php
appDashboardA.blade.php
program.blade.php
```

## 2. Membuat Route Dashboard

Route adalah alamat URL yang bisa dibuka di browser.

Kita menambahkan route baru di:

```text
routes/web.php
```

Kode yang ditambahkan:

```php
Route::get('/dashboard-admin', function () {
    return view('dashboardAdmin.dashboardA');
});
```

Artinya:

- Kalau user membuka `/dashboard-admin`
- Laravel akan menampilkan view `dashboardAdmin.dashboardA`

Nama view ini mengikuti struktur folder:

```text
resources/views/dashboardAdmin/dashboardA.blade.php
```

Di Laravel, tanda titik dipakai untuk masuk ke folder.

Jadi:

```php
view('dashboardAdmin.dashboardA')
```

sama dengan:

```text
resources/views/dashboardAdmin/dashboardA.blade.php
```

## 3. Membuat Layout Utama Dashboard

File layout dashboard ada di:

```text
resources/views/dashboardAdmin/appDashboardA.blade.php
```

File ini berfungsi sebagai kerangka utama halaman admin.

Isi utamanya:

- Struktur HTML lengkap
- Link Bootstrap
- Link Font Awesome
- CSS dashboard
- Sidebar
- Area konten utama

Contoh bagian penting:

```blade
@yield('content')
```

Artinya:

> Di bagian ini, Laravel akan memasukkan isi halaman lain.

Jadi `appDashboardA.blade.php` bukan isi dashboardnya langsung. File ini adalah template atau wadah utama.

Bayangkan seperti ini:

```text
appDashboardA.blade.php
= kerangka halaman

dashboardA.blade.php
= isi halaman
```

## 4. Membuat Isi Halaman Dashboard

File isi dashboard ada di:

```text
resources/views/dashboardAdmin/dashboardA.blade.php
```

Di bagian atas file ini ada kode:

```blade
@extends('dashboardAdmin.appDashboardA')
```

Artinya:

> File ini memakai layout dari `appDashboardA.blade.php`.

Lalu isi halaman ditulis di dalam:

```blade
@section('content')
    ...
@endsection
```

Artinya:

> Semua yang ada di dalam `@section('content')` akan dimasukkan ke `@yield('content')` milik layout.

Contoh hubungan sederhananya:

```blade
{{-- appDashboardA.blade.php --}}
<main>
    @yield('content')
</main>
```

```blade
{{-- dashboardA.blade.php --}}
@extends('dashboardAdmin.appDashboardA')

@section('content')
    <h1>Dashboard Admin</h1>
@endsection
```

Hasil akhirnya di browser:

```html
<main>
    <h1>Dashboard Admin</h1>
</main>
```

## 5. Membuat Sidebar

Sidebar dibuat di file layout:

```text
appDashboardA.blade.php
```

Kenapa sidebar diletakkan di layout?

Karena sidebar akan dipakai di banyak halaman admin, misalnya:

- Dashboard
- Profil Saya
- Kelas Saya
- Rapor
- Riwayat Pembayaran

Kalau sidebar ditulis di setiap halaman, kodenya akan berulang. Jadi lebih rapi kalau sidebar cukup ditulis sekali di layout.

Contoh bagian sidebar:

```blade
<aside class="sidebar">
    <div>
        <p class="brand-title">Student Portal</p>
        <p class="brand-subtitle">ETC Planet Learner</p>
    </div>

    <nav class="sidebar-nav">
        <a class="nav-item active" href="{{ url('/dashboard-admin') }}">
            <i class="fa-solid fa-table-cells-large"></i>
            <span>Dashboard</span>
        </a>
    </nav>
</aside>
```

Penjelasan:

- `<aside>` dipakai untuk bagian samping halaman.
- `<nav>` dipakai untuk menu navigasi.
- `<a>` dipakai untuk link.
- `<i>` dipakai untuk icon dari Font Awesome.
- `{{ url('/dashboard-admin') }}` adalah helper Laravel untuk membuat URL.

## 6. Membuat Header Dashboard

Header dashboard dibuat di:

```text
dashboardA.blade.php
```

Contoh:

```blade
<header class="page-header">
    <div>
        <h1 class="page-title">Dashboard Admin</h1>
        <p class="page-subtitle">Ringkasan aktivitas dan pendaftaran hari ini.</p>
    </div>
    <div class="avatar" aria-label="Admin ETC">A</div>
</header>
```

Penjelasan:

- `<header>` adalah bagian kepala halaman.
- `<h1>` adalah judul utama halaman.
- `<p>` adalah teks deskripsi.
- `.avatar` adalah lingkaran kecil di kanan atas.

## 7. Membuat Card Statistik

Card statistik adalah kotak putih yang berisi data seperti:

- Total siswa
- Pendaftaran baru
- Pendapatan
- Kelas aktif

Kode pembungkusnya:

```blade
<section class="stat-grid">
    ...
</section>
```

Setiap card memakai:

```blade
<article class="stat-card">
    ...
</article>
```

Kenapa memakai `<section>` dan `<article>`?

- `<section>` untuk mengelompokkan satu bagian halaman.
- `<article>` cocok untuk item yang berdiri sendiri, seperti satu card statistik.

Contoh satu card:

```blade
<article class="stat-card">
    <div>
        <p class="stat-label">Total Siswa</p>
        <p class="stat-value">1,248</p>
        <p class="stat-note">+12% bulan ini</p>
    </div>
    <div class="stat-icon">
        <i class="fa-solid fa-users"></i>
    </div>
</article>
```

## 8. Membuat Tombol Aksi

Tombol aksi berada di bawah card statistik.

Contohnya:

```blade
<section class="action-row">
    <button class="action-btn primary" type="button">
        <i class="fa-solid fa-user-plus me-1"></i>
        Tambah Siswa
    </button>
</section>
```

Penjelasan:

- `<button>` dipakai untuk tombol.
- `type="button"` berarti tombol biasa, bukan tombol submit form.
- Class `primary` dipakai untuk membedakan tombol utama dengan tombol biasa.

## 9. Membuat Tabel Pendaftaran

Tabel pendaftaran terbaru dibuat di:

```text
dashboardA.blade.php
```

Strukturnya:

```blade
<section class="table-card">
    <div class="section-header">
        <h2>Pendaftaran Terbaru</h2>
        <a class="section-link" href="#">Lihat Semua</a>
    </div>

    <table class="admin-table">
        ...
    </table>
</section>
```

Bagian tabel:

```blade
<thead>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Program</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
</thead>
```

Penjelasan:

- `<table>` adalah tabel.
- `<thead>` adalah kepala tabel.
- `<tbody>` adalah isi tabel.
- `<tr>` adalah baris tabel.
- `<th>` adalah judul kolom.
- `<td>` adalah isi kolom.

## 10. Memisahkan CSS Dashboard

CSS dashboard dipisahkan ke file sendiri:

```text
public/css/dashboard-admin.css
```

File layout `appDashboardA.blade.php` cukup memanggil CSS tersebut dengan:

```blade
<link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
```

Kenapa CSS dipisahkan?

- File Blade jadi lebih rapi.
- HTML dan CSS tidak bercampur terlalu banyak.
- CSS lebih mudah dicari dan diedit.
- Kalau nanti ada banyak halaman admin, CSS bisa dipakai ulang.

Contoh:

```css
.admin-shell {
    display: grid;
    grid-template-columns: 255px 1fr;
    min-height: 100vh;
}
```

Artinya:

- `.admin-shell` adalah pembungkus utama halaman admin.
- `display: grid` membuat layout memakai CSS Grid.
- `grid-template-columns: 255px 1fr` artinya:
  - kolom kiri lebarnya 255px untuk sidebar
  - kolom kanan memakai sisa ruang untuk konten utama
- `min-height: 100vh` artinya tinggi minimal sama dengan tinggi layar.

## 11. Mengenal CSS Variable

Di bagian atas CSS ada:

```css
:root {
    --pink: #ec008c;
    --ink: #2c2228;
    --charcoal: #2f2f2f;
}
```

Ini disebut CSS variable.

Tujuannya agar warna bisa dipakai ulang.

Contoh penggunaan:

```css
.sidebar-help {
    background: var(--pink);
}
```

Artinya:

```css
background: #ec008c;
```

Keuntungannya:

Kalau nanti ingin mengganti warna utama website, cukup ubah nilai `--pink` di satu tempat.

## 12. Membuat Tampilan Responsif

Responsif artinya tampilan tetap rapi di layar besar dan layar kecil.

Kita memakai media query:

```css
@media (max-width: 992px) {
    .admin-shell {
        grid-template-columns: 1fr;
    }
}
```

Artinya:

> Kalau lebar layar maksimal 992px, layout berubah menjadi satu kolom.

Ini penting untuk tablet dan HP.

## 13. Merapikan View Program

Sebelumnya file program berada di folder:

```text
resources/views/program/
```

Maka pemanggilan view harus memakai nama folder.

Route yang benar:

```php
Route::get('/program', function () {
    return view('program.program');
});
```

Layout program yang benar:

```blade
@extends('program.appProgram')
```

Include yang benar:

```blade
@include('program.headerProgram')
@include('program.footerProgram')
```

Aturan sederhananya:

```text
Folder di resources/views ditulis memakai titik.
```

Contoh:

```text
resources/views/program/headerProgram.blade.php
```

dipanggil dengan:

```blade
@include('program.headerProgram')
```

CSS halaman program juga dipisahkan ke file:

```text
public/css/program.css
```

Lalu layout program memanggilnya dengan:

```blade
<link rel="stylesheet" href="{{ asset('css/program.css') }}">
```

Jadi semua style halaman program tidak lagi ditulis langsung di Blade memakai `style="..."` atau tag `<style>`.

## 14. Mengecek Hasil

Setelah membuat atau mengubah view, kita bisa mengecek route dengan:

```bash
php artisan route:list
```

Kalau route berhasil, akan muncul daftar URL, termasuk:

```text
dashboard-admin
program
```

Untuk mengecek Blade, gunakan:

```bash
php artisan view:cache
```

Kalau berhasil, berarti template Blade tidak punya error sintaks.

Kalau ingin membersihkan cache view:

```bash
php artisan view:clear
```

## 15. Menjalankan Website

Untuk menjalankan website Laravel:

```bash
php artisan serve
```

Setelah itu buka di browser:

```text
http://127.0.0.1:8000/dashboard-admin
```

Untuk halaman program:

```text
http://127.0.0.1:8000/program
```

## 16. Urutan Belajar yang Disarankan

Belajar coding website ini sebaiknya bertahap:

1. Pahami HTML dasar
2. Pahami CSS dasar
3. Pahami layout dengan Flexbox dan Grid
4. Pahami route Laravel
5. Pahami Blade layout
6. Pahami `@extends`, `@section`, `@yield`, dan `@include`
7. Buat halaman statis dulu
8. Baru masukkan data dari PHP
9. Baru lanjut ke database
10. Baru lanjut ke fitur login dan admin sungguhan

## 17. Tahap Berikutnya

Tahap dashboard kita saat ini masih memakai data statis.

Contoh data statis:

```blade
<p class="stat-value">1,248</p>
```

Nanti data seperti ini bisa dipindahkan ke route:

```php
$totalSiswa = 1248;
```

Lalu dikirim ke view:

```php
return view('dashboardAdmin.dashboardA', [
    'totalSiswa' => $totalSiswa,
]);
```

Kemudian dipakai di Blade:

```blade
<p class="stat-value">{{ $totalSiswa }}</p>
```

Itulah langkah awal menuju dashboard yang datanya dinamis.

## Ringkasan

Yang sudah dibuat:

- Route `/dashboard-admin`
- Layout utama `appDashboardA.blade.php`
- Isi dashboard `dashboardA.blade.php`
- Sidebar admin
- Header dashboard
- Card statistik
- Tombol aksi
- Tabel pendaftaran terbaru
- Perbaikan pemanggilan view program

Konsep utama yang harus diingat:

```text
Route menentukan URL.
View menentukan tampilan.
Layout menghindari kode berulang.
Blade menghubungkan layout dan isi halaman.
CSS mengatur bentuk, warna, posisi, dan responsif.
```

# Laporan Praktikum Dependency Laravel Excel

Tanggal: 8 Juni 2026
Branch: `fitur-dependency`
Project: ETC Planet Company Profile
Dependency utama: `maatwebsite/excel`

## Ringkasan Implementasi

Bagian 2 praktikum dikerjakan dengan memasang package `maatwebsite/excel` untuk kebutuhan import/export Excel pada project Laravel. Scope implementasi pada tahap ini adalah install dependency dan dokumentasi perubahan, belum refactor fitur export siswa agar memakai Laravel Excel.

Command akhir yang berhasil dipakai:

```bash
composer require maatwebsite/excel --ignore-platform-req=php
```

Catatan kendala: command awal `composer require maatwebsite/excel` gagal karena mesin lokal memakai PHP `8.5.3`, sedangkan dependency transitif `phpoffice/phpspreadsheet` versi yang dipilih Composer masih mensyaratkan PHP `>=7.4.0 <8.5.0`. Karena project Laravel ini menargetkan PHP `^8.3`, dependency tetap relevan untuk environment PHP 8.3/8.4. Pada mesin lokal PHP 8.5.3, Composer perlu dijalankan dengan `--ignore-platform-req=php`.

## Analisis Perubahan

Perubahan pada `composer.json`:

- Menambahkan dependency runtime `maatwebsite/excel` dengan constraint `^3.1`.
- Tidak ada perubahan route, controller, model, migration, schema database, atau UI.

Alasan `composer.lock` ikut berubah:

- `composer.lock` menyimpan versi exact hasil resolusi Composer agar semua anggota kelompok mendapat dependency yang sama.
- Setelah `maatwebsite/excel` dipasang, Composer mengunci versi `maatwebsite/excel` ke `3.1.69`.
- Lock file juga mencatat dependency transitif yang dibutuhkan oleh Laravel Excel.

Dependency tambahan yang masuk melalui `composer.lock`:

- `maatwebsite/excel`
- `phpoffice/phpspreadsheet`
- `ezyang/htmlpurifier`
- `maennchen/zipstream-php`
- `markbaker/complex`
- `markbaker/matrix`
- `composer/semver`

Composer juga menampilkan peringatan audit security advisory. Risiko ini perlu dipantau dengan `composer audit` dan update package berkala.

## Hasil Pengecekan Project

Validasi yang sudah dijalankan:

- `composer show maatwebsite/excel`: berhasil, package terpasang versi `3.1.69`.
- `php artisan package:discover`: berhasil, `maatwebsite/excel` terdeteksi oleh Laravel package discovery.
- `php artisan route:list`: berhasil, route tetap terbaca normal dengan 106 route.
- `php artisan test`: berhasil, 83 test lulus dengan 731 assertion.
- `php artisan serve`: berhasil, halaman utama merespons `HTTP 200` pada localhost.

Kesimpulan pengecekan: project tetap berjalan normal setelah dependency dipasang.

## Refleksi Bagian 3

Kendala saat install dependency:

- Terjadi kendala kompatibilitas platform karena PHP lokal `8.5.3` lebih baru dari batas versi `phpoffice/phpspreadsheet`.
- Solusi sementara untuk praktikum adalah menjalankan Composer dengan `--ignore-platform-req=php`.

Apakah terjadi error:

- Ya, command awal gagal pada tahap dependency resolution.
- Setelah memakai opsi platform ignore, dependency berhasil dipasang dan project tetap lolos verifikasi.

Risiko dependency terhadap project:

- Project menjadi bergantung pada package pihak ketiga dan dependency transitifnya.
- Versi package harus dipantau agar tetap kompatibel dengan Laravel, PHP, dan security advisory.
- Jika environment production memakai PHP 8.5+, Composer install normal dapat gagal sampai `phpoffice/phpspreadsheet` mendukung PHP tersebut.

Apakah dependency diperlukan:

- Diperlukan untuk kebutuhan evolusi fitur export laporan siswa ke Excel.
- Saat ini package baru dipasang dan didokumentasikan; implementasi fitur export dengan Laravel Excel dapat dikerjakan pada tahap berikutnya.

Dampak terhadap maintenance software:

- Dependency mempercepat pengembangan fitur Excel karena tidak perlu membuat file spreadsheet secara manual.
- Maintenance perlu mencakup update dependency, audit keamanan, dan pengecekan compatibility sebelum upgrade PHP/Laravel.

## Checklist Luaran Praktikum

- Link repository GitHub: `https://github.com/STACKD-Team/etc-company-profile`
- Branch praktikum: `fitur-dependency`
- Screenshot install dependency: ambil dari output `composer require maatwebsite/excel --ignore-platform-req=php`.
- Screenshot perubahan `composer.json`: ambil dari diff yang menunjukkan `maatwebsite/excel`.
- Commit history: ambil setelah commit `Menambahkan dependency`.
- Dokumentasi dependency: dokumen ini dan `context/DEPENDENCY_PACKAGE_5W1H.md`.
- Hasil refleksi kelompok: bagian "Refleksi Bagian 3" pada dokumen ini.

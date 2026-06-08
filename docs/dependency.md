# Dependency Documentation

Dokumen ini merapikan dokumentasi dependency proyek ETC Planet dari `context/DEPENDENCY_PACKAGE_5W1H.md` dan `docs/laporan-praktikum-dependency.md`.

## Ringkasan Dependency

| Package | Fungsi | Alasan | Versi | Risiko |
| --- | --- | --- | --- | --- |
| `laravel/framework` | Framework utama aplikasi web fullstack. | Menyediakan routing, controller, model, migration, Blade, validation, middleware, queue, storage, dan service container. | `^13.7` | Upgrade major dapat membawa breaking changes. |
| `laravel/tinker` | Console interaktif Laravel. | Membantu cek model, query, relasi, dan data seed saat development. | `^3.0` | Jangan dipakai sembarang di production shell tanpa kontrol akses. |
| `maatwebsite/excel` | Import/export Excel. | Dibutuhkan untuk export laporan rekap siswa sesuai template Excel ETC. | `^3.1` | Bergantung pada dependency transitif seperti `phpoffice/phpspreadsheet`; perlu audit keamanan dan kompatibilitas PHP. |
| `fakerphp/faker` | Generator data dummy. | Membantu factory, seeder, dan test. | `^1.23` | Data palsu tidak boleh masuk data production. |
| `laravel/pail` | Melihat log Laravel real-time. | Mempercepat debugging saat development. | `^1.2.5` | Dev-only; tidak menjadi fitur aplikasi. |
| `laravel/pao` | Tool pendukung development Laravel. | Membantu workflow development sesuai skeleton Laravel. | `^1.0.6` | Dev-only; pantau kompatibilitas saat upgrade Laravel. |
| `laravel/pint` | Code style fixer PHP. | Menjaga format kode konsisten antar anggota tim. | `^1.27` | Formatter dapat mengubah banyak file jika dijalankan tanpa review. |
| `pestphp/pest` | Framework testing PHP. | Menulis unit dan feature test dengan sintaks ringkas. | `^4.7` | Test perlu dirawat agar tetap relevan dengan behavior aktual. |
| `pestphp/pest-plugin-laravel` | Integrasi Pest dengan Laravel. | Menyediakan helper testing Laravel. | `^4.1` | Harus kompatibel dengan versi Pest dan Laravel. |
| `mockery/mockery` | Mocking library untuk test. | Membantu isolasi service atau dependency saat unit test. | `^1.6` | Mock berlebihan dapat membuat test tidak merepresentasikan alur nyata. |
| `nunomaduro/collision` | Error reporting console. | Membuat error test/CLI lebih mudah dibaca. | `^8.6` | Dev-only. |
| `vite` | Build tool frontend. | Development server dan bundling asset. | `^8.0.0` | Upgrade dapat memengaruhi konfigurasi build. |
| `laravel-vite-plugin` | Integrasi Vite dengan Laravel. | Menghubungkan asset Vite ke Blade melalui konfigurasi Laravel. | `^3.1` | Harus kompatibel dengan Laravel dan Vite. |
| `tailwindcss` | Utility-first CSS framework. | Mendukung UI responsive dan konsisten dengan desain ETC Planet. | `^4.0.0` | Perubahan versi major dapat mengubah behavior styling. |
| `@tailwindcss/vite` | Plugin Tailwind untuk Vite. | Memproses Tailwind CSS 4 dalam pipeline Vite. | `^4.0.0` | Harus kompatibel dengan Vite. |
| `concurrently` | Menjalankan beberapa proses development. | Dipakai script `composer dev` untuk server, queue, dan Vite. | `^9.0.1` | Dev-only; proses paralel perlu dimatikan dengan benar. |

## Cara Install

Install semua dependency dari lock file:

```bash
composer install
npm install
```

Install Laravel Excel jika perlu dilakukan ulang:

```bash
composer require maatwebsite/excel
```

Jika memakai PHP lokal 8.5 dan Composer gagal karena dependency transitif belum mendukung PHP tersebut:

```bash
composer require maatwebsite/excel --ignore-platform-req=php
```

Catatan: workaround ini hanya untuk environment lokal. Target project tetap PHP `^8.3`.

## Dampak pada Proyek

- Laravel menjadi fondasi seluruh fitur public website, registration, payment, dashboard, CMS, reels, chatbot, report card, dan export.
- Tailwind, Vite, dan plugin terkait mendukung tampilan responsive dan pipeline asset.
- Pest dan dependency test membantu menjaga route, service, dan workflow tetap terverifikasi.
- Laravel Excel menambah kemampuan export Excel, tetapi juga menambah dependency transitif yang harus dipantau.
- Composer lock dan package lock wajib ikut dicommit agar semua anggota tim memakai versi dependency yang sama.

## Dependency yang Direkomendasikan untuk Tahap Berikutnya

| Package | Fungsi | Alasan | Status |
| --- | --- | --- | --- |
| `phpoffice/phpword` | Generate dokumen Word dari template. | Dibutuhkan jika rapor akhir pembelajaran dibuat dari template `.doc`/Word. | Direkomendasikan |
| `barryvdh/laravel-dompdf` | Generate PDF dari HTML. | Berguna untuk bukti pendaftaran, receipt, atau turunan PDF rapor. | Direkomendasikan |
| `spatie/laravel-permission` | Role dan permission berbasis database. | Dipakai jika role `admin`, `student`, dan `instructor` membutuhkan permission lebih granular. | Direkomendasikan |

## Maintenance Dependency

- Jalankan audit berkala:

```bash
composer audit
```

- Jalankan test setelah install atau update dependency:

```bash
php artisan test
npm run build
```

- Catat perubahan dependency di `CHANGELOG.md` bagian `Dependency`.
- Hindari update major menjelang demo/final tanpa waktu testing yang cukup.

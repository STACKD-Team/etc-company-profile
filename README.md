# ETC Planet Company Profile

Website company profile dan sistem operasional awal untuk LKP ETC / ETC Planet Padang. Aplikasi ini dibangun untuk memperkuat profil lembaga, memudahkan calon siswa melihat program, melakukan pendaftaran online, mengirim bukti pembayaran, serta membantu admin mengelola pendaftaran, kelas, siswa, rapor, reels, CMS, dan laporan.

Repository GitHub: https://github.com/STACKD-Team/etc-company-profile

## Deskripsi Proyek

ETC Planet masih banyak menjalankan promosi, konsultasi, pendaftaran, dan rekap data melalui kanal manual seperti WhatsApp, Instagram, formulir fisik, dan Excel. Proyek ini mendigitalisasi alur utama tersebut agar informasi program lebih mudah ditemukan, proses pendaftaran lebih tertata, data siswa lebih mudah dipelihara, dan laporan akademik dapat disiapkan dari satu sistem.

Target pengguna:

- Calon siswa dan orang tua yang ingin melihat informasi program dan mendaftar.
- Siswa yang ingin melihat profil, kelas, riwayat pembayaran, dan rapor.
- Admin ETC yang mengelola pendaftaran, pembayaran, kelas, siswa, CMS, reels, dan laporan.
- Instructor yang membutuhkan akses ringkas ke kelas, siswa, dan rapor terkait.

## Features

- Public website: beranda, tentang ETC, program, detail program, team, fasilitas, galeri, FAQ, kontak, reels, dan chatbot.
- Registration flow: pilih program, form pendaftaran, pembayaran, upload bukti, konfirmasi, dan bukti pendaftaran.
- Student dashboard: overview, profil, kelas, riwayat pembelajaran, pembayaran, bantuan, dan rapor yang sudah dipublish.
- Admin dashboard: statistik, pendaftaran, verifikasi pembayaran, placement test, siswa, instructor, program, kelas, enrollment, rapor, export, CMS, reels, pesan kontak, chatbot log, dan setting.
- Instructor dashboard: kelas yang diajar, siswa, dan report card terkait.
- Document output: rapor akhir pembelajaran dan export rekap siswa berbasis template dokumen ETC.

## Tech Stack

- Laravel 13
- PHP 8.3
- MySQL
- Blade
- Vite
- Tailwind CSS 4
- Composer
- NPM
- Pest
- Laravel Excel (`maatwebsite/excel`)
- Git dan GitHub

## Instalasi Singkat

```bash
git clone https://github.com/STACKD-Team/etc-company-profile.git
cd etc-company-profile
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Buka aplikasi di:

```text
http://127.0.0.1:8000
```

Panduan lengkap tersedia di [docs/installation.md](docs/installation.md).

## Screenshot Proyek

Screenshot sementara memakai referensi desain dari folder `context/`. Screenshot ini perlu diganti dengan hasil aplikasi aktual saat final.

| Halaman | Screenshot Referensi |
| --- | --- |
| Beranda | `context/stitch_etc_planet_digital_hub/beranda_lengkap_etc_planet/screen.png` |
| Pilih Program | `context/stitch_etc_planet_digital_hub/pilih_program_etc_planet/screen.png` |
| Form Pendaftaran | `context/stitch_etc_planet_digital_hub/pendaftaran_online_lengkap_etc_planet/screen.png` |
| Pembayaran | `context/stitch_etc_planet_digital_hub/pembayaran_etc_planet/screen.png` |
| Konfirmasi | `context/stitch_etc_planet_digital_hub/konfirmasi_pendaftaran_etc_planet/screen.png` |
| Dashboard Siswa | `context/stitch_etc_planet_digital_hub/dashboard_siswa_etc_planet/screen.png` |
| Dashboard Admin | `context/stitch_etc_planet_digital_hub/dashboard_admin_etc_planet/screen.png` |

## Dokumentasi

- [Installation Documentation](docs/installation.md)
- [Feature Documentation](docs/features.md)
- [Dependency Documentation](docs/dependency.md)
- [Refactoring Documentation](docs/refactoring.md)
- [GitHub Actions Documentation](docs/github-actions.md)
- [Changelog](CHANGELOG.md)

## Tim Pengembang

| Anggota | Fokus Modul |
| --- | --- |
| Miftah | Public website, contact, chatbot public, reels public, CMS/content |
| Mecca | Program discovery, student dashboard, academic master data |
| Mia | Registration flow, payment flow, admin registration/payment verification |
| Rasky | Auth, program detail, admin dashboard/workflow, report cards, exports, instructor dashboard |

## Status Dokumentasi

| Dokumen | Target |
| --- | --- |
| README | Minggu depan, 15-21 Juni 2026 |
| Installation Documentation | Minggu depan, 15-21 Juni 2026 |
| Feature Documentation | Minggu ke-14 |
| CHANGELOG.md | Diupdate mingguan mulai 15 Juni 2026 |
| Dependency Documentation | Merapikan dokumentasi tugas 2 minggu terakhir |
| Refactoring Documentation | Final |
| GitHub Actions Documentation | Final |

## Referensi Proyek

- `PROJECT_BLUEPRINT_ETC.md`
- `context/WEB_ROUTES_ETC.md`
- `context/SKEMA_DATABASE_LENGKAP.md`
- `context/DEPENDENCY_PACKAGE_5W1H.md`
- `docs/laporan-praktikum-dependency.md`
- `context/stitch_etc_planet_digital_hub/`

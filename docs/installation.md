# Installation Documentation

Dokumen ini menjelaskan cara memasang dan menjalankan proyek ETC Planet Company Profile di environment lokal developer.

## Persyaratan Sistem

| Kebutuhan | Versi / Catatan |
| --- | --- |
| PHP | `^8.3` sesuai `composer.json` |
| Composer | Versi terbaru yang kompatibel dengan PHP lokal |
| Node.js dan NPM | Dibutuhkan untuk Vite dan Tailwind CSS |
| MySQL | Direkomendasikan MySQL 8 atau kompatibel dengan `utf8mb4` |
| Git | Untuk clone repository |

Stack utama proyek:

- Laravel 13
- Blade
- Vite
- Tailwind CSS 4
- Pest
- Laravel Excel

## Langkah Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/STACKD-Team/etc-company-profile.git
cd etc-company-profile
```

### 2. Install Dependency Backend

```bash
composer install
```

Jika environment lokal memakai PHP 8.5 dan Composer gagal karena batas versi dependency transitif Laravel Excel, gunakan workaround lokal berikut:

```bash
composer install --ignore-platform-req=php
```

Catatan: project tetap menargetkan PHP `^8.3`. Workaround ini hanya untuk mesin lokal yang PHP-nya lebih baru dari dukungan package transitif.

### 3. Install Dependency Frontend

```bash
npm install
```

### 4. Setup Environment

Salin file environment:

```bash
cp .env.example .env
```

Pada Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Sesuaikan konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=etc_company_profile
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Setup Database

Buat database sesuai nama di `.env`, lalu jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Jika hanya ingin menjalankan migration:

```bash
php artisan migrate
```

### 6. Build Asset

Untuk development:

```bash
npm run dev
```

Untuk production build:

```bash
npm run build
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

### 8. Jalankan Test

```bash
php artisan test
```

Atau memakai script Composer:

```bash
composer test
```

## Troubleshooting

### Composer Gagal Karena PHP 8.5

Masalah:

- Mesin lokal memakai PHP 8.5.
- Dependency transitif `phpoffice/phpspreadsheet` yang dibawa `maatwebsite/excel` belum menerima PHP 8.5 pada versi yang dipilih Composer.

Solusi lokal:

```bash
composer install --ignore-platform-req=php
```

Solusi ideal:

- Gunakan PHP 8.3 atau 8.4 untuk development.
- Pantau update dependency dengan `composer update` dan `composer audit`.

### Storage atau Cache Tidak Bisa Ditulis

Pada Linux/macOS, pastikan folder berikut bisa ditulis web server:

```bash
chmod -R 775 storage bootstrap/cache
```

Pada Windows, pastikan folder `storage` dan `bootstrap/cache` tidak read-only.

### Config Lama Masih Terbaca

Bersihkan cache Laravel:

```bash
php artisan optimize:clear
```

### Asset Tidak Muncul

Jalankan ulang build frontend:

```bash
npm run build
```

Saat development, jalankan:

```bash
npm run dev
```

## Checklist Instalasi

- Repository berhasil di-clone.
- Dependency Composer dan NPM berhasil terpasang.
- File `.env` sudah dibuat dan disesuaikan.
- `APP_KEY` sudah dibuat.
- Database sudah dibuat.
- Migration dan seeder berhasil dijalankan.
- Asset berhasil dibuild.
- Aplikasi bisa dibuka di browser.
- Test berhasil dijalankan.

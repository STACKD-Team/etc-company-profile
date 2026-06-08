# GitHub Actions Documentation

Dokumen ini menjelaskan rencana workflow GitHub Actions untuk proyek ETC Planet. Saat dokumen ini dibuat, folder `.github/workflows` belum tersedia di repository lokal, sehingga workflow di bawah ini adalah rencana implementasi CI untuk tahap final.

## Workflow yang Digunakan

CI workflow untuk:

- Checkout source code.
- Setup PHP.
- Install dependency Composer.
- Setup Node.js.
- Install dependency NPM.
- Build asset frontend.
- Menjalankan test Laravel.

## Lokasi File

Rencana lokasi workflow:

```text
.github/workflows/ci.yml
```

## Trigger

Workflow dijalankan saat:

- `push` ke branch utama atau branch fitur.
- `pull_request` ke branch utama.

Contoh trigger:

```yaml
on:
  push:
  pull_request:
```

## Tahapan Workflow

1. Checkout code dari repository.
2. Setup PHP 8.3 dan ekstensi yang dibutuhkan Laravel.
3. Install dependency Composer dengan `composer install`.
4. Setup Node.js.
5. Install dependency frontend dengan `npm ci`.
6. Build asset dengan `npm run build`.
7. Setup environment testing.
8. Jalankan migration untuk database test.
9. Jalankan test dengan `php artisan test`.

## Contoh Rencana `ci.yml`

```yaml
name: CI

on:
  push:
  pull_request:

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, intl, pdo_mysql, zip
          coverage: none

      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-interaction --no-progress

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '22'
          cache: npm

      - name: Install NPM dependencies
        run: npm ci

      - name: Build frontend assets
        run: npm run build

      - name: Prepare environment
        run: |
          cp .env.example .env
          php artisan key:generate

      - name: Run tests
        run: php artisan test
```

## Hasil Workflow

Saat workflow sudah dibuat, dokumentasi ini perlu dilengkapi dengan:

- Screenshot workflow berhasil di tab GitHub Actions.
- Status badge di `README.md`.
- Catatan jika ada test yang perlu database service khusus.

Contoh badge setelah workflow tersedia:

```markdown
![CI](https://github.com/STACKD-Team/etc-company-profile/actions/workflows/ci.yml/badge.svg)
```

## Catatan Implementasi Final

- Jika test memakai MySQL, tambahkan service MySQL di workflow.
- Jika test dapat memakai SQLite memory, atur environment testing agar lebih cepat.
- Jangan menyimpan secret di file workflow.
- Semua secret harus masuk ke GitHub Repository Secrets.

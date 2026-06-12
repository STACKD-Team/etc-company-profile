# Sprint 2 Miftah Public Discovery Audit

Dokumen ini mencatat hasil audit tanggung jawab Miftah pada Sprint 2 berdasarkan
`context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.

## Kesimpulan

Status Sprint 2 Miftah adalah **Complete** untuk seluruh aturan Sprint 2 yang
relevan terhadap Public Discovery.

Sprint 2 berfokus pada standardisasi Filament dan CRUD dengan pembagian:

- Mia sebagai owner utama admin panel dan Filament Resource.
- Mecca untuk migrasi komponen Student Panel bila tetap memakai Blade.
- Rasky untuk migrasi komponen Instructor Panel bila tetap memakai Blade.

Miftah tidak memiliki CRUD atau Filament Resource pada Sprint 2. Implementasi
yang relevan adalah memastikan control generik Public Discovery memakai shared
component `x-ui.*`, wrapper tersebut berbasis Filament, seluruh halaman memakai
layout public bersama, dan kontrak tersebut dilindungi regression test.

Kesimpulan ini tidak menyatakan bahwa keseluruhan Sprint 2 project sudah
selesai; status area Mia, Mecca, dan Rasky harus diaudit terpisah.

## Batas Ownership

Ownership terbaru Miftah adalah Public Discovery:

- Public home, about, team, facilities, dan gallery.
- Contact dan FAQ.
- Public chatbot UI.
- Public reels.
- Public program discovery UI.
- Gallery dan partner published yang ditampilkan dari CMS.

`context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md` menyatakan bahwa
`context/WEB_ROUTES_ETC.md` merupakan dokumen deprecated untuk ownership/task
terbaru. Oleh karena itu, ownership CMS/admin Miftah pada dokumen route lama,
README, atau test sprint lama tidak dipakai untuk menentukan scope Sprint 2.

Flow registration dan payment juga bukan ownership Miftah. Raw field atau
button yang masih ada pada halaman pembayaran tidak dihitung sebagai gap
Sprint 2 Miftah.

## Verifikasi

Audit dilakukan pada 11 Juni 2026 dengan hasil:

- `php artisan route:list --name=public` berhasil.
- Seluruh 15 route Public Discovery Miftah terdaftar.
- Public page utama memakai shared layout `<x-layouts.public>`.
- Control pada area Public Discovery memakai shared component `x-ui.*` untuk
  field, action, badge, dan empty state yang relevan.
- `tests/Feature/MiftahSprint2FilamentStandardizationTest.php` mengunci:
  - seluruh 15 route Public Discovery Miftah;
  - pemakaian shared public layout;
  - larangan raw control pada halaman yang memiliki control interaktif;
  - basis Filament pada wrapper `x-ui.*` yang dipakai Miftah;
  - render seluruh halaman Public Discovery.
- Test berikut berhasil dijalankan:
  - `tests/Feature/MiftahSprint0FoundationTest.php`
  - `tests/Feature/MiftahSprint1PublicDiscoveryTest.php`
  - `tests/Feature/PublicDiscoveryTest.php`
- Seluruh test Miftah/Public Discovery setelah implementasi: 28 passed,
  492 assertions.
- Full project suite: 127 dari 128 test lulus. Satu kegagalan berada pada
  `FilamentUiAssetsTest` terkait class collapsed sidebar Instructor Panel,
  sehingga berada di luar ownership dan perubahan Sprint 2 Miftah.

## Keputusan

Tidak diperlukan route, controller, view, Filament Resource, atau migration
baru karena implementasi Public Discovery sudah memenuhi standardisasi.
Regression test Sprint 2 ditambahkan agar kepatuhan tersebut tidak mengalami
regresi saat shared component atau halaman public diubah.

## Penyempurnaan Reels

- Feed reels memakai vertical scroll snap satu video per viewport.
- Video aktif mencoba autoplay dengan suara dan video di luar viewport berhenti.
- Jika autoplay bersuara diblokir browser, suara diaktifkan pada interaksi
  pertama pengguna sesuai kebijakan autoplay browser.
- Klik atau tap video melakukan pause/resume dengan indikator playback tanpa
  menampilkan teks petunjuk tambahan.
- Desktop menampilkan caption di samping video, sedangkan mobile menampilkan
  video full-screen dengan caption overlay seperti pengalaman Instagram Reels.
- Wheel, keyboard, swipe, dan klik area kosong memindahkan tepat satu reel
  dengan transisi halus; klik pada video tetap melakukan pause/resume.
- Reels hanya memiliki satu tampilan feed di `/reels`; URL detail lama seperti
  `/reels/{reel}` mengalihkan pengunjung ke feed yang sama.
- Ikon dan angka view/like tidak ditampilkan pada feed, detail, maupun preview
  reels di landing page.
- View tetap dicatat melalui endpoint terkontrol tanpa menampilkan counternya.
- Endpoint like tetap dipertahankan untuk kompatibilitas route, tetapi tidak
  dipanggil dari UI Public Discovery.

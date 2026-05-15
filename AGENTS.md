# AGENTS.md

Panduan singkat untuk agen/developer yang bekerja di project ETC Planet.

## Sumber Wajib Dibaca

Sebelum mengubah kode, baca:

- `PROJECT_BLUEPRINT_ETC.md`
- `context/Project_Charter_ETC_Updated.pdf`
- `context/SKEMA_DATABASE_LENGKAP.md`
- `context/FORMULIR PENDAFTARAN.jpeg`
- `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`
- `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`
- `context/WEB_ROUTES_ETC.md`
- `context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md`
- Referensi HTML/screenshot di `context/stitch_etc_planet_digital_hub/`

Folder `context/` adalah sumber kebenaran project.

## Stack

- Laravel 13
- PHP 8.3
- MySQL
- Blade
- Vite
- Tailwind CSS 4

## Aturan Kerja

- Ikuti pola Laravel standar: route, controller, request validation, model, policy/middleware, Blade.
- Saat membuat route, controller, view, sidebar/menu, breadcrumb, atau link antar halaman, ikuti blueprint `context/WEB_ROUTES_ETC.md`.
- Jangan mengubah file yang tidak terkait dengan tugas.
- Jangan menghapus atau mengganti konteks/template contoh.
- Semua form registrasi harus mengikuti field dan pilihan pada `context/FORMULIR PENDAFTARAN.jpeg`.
- Output rapor harus mengikuti template `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`.
- Output rekap siswa harus mengikuti template `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`.
- Untuk document generation, gunakan pendekatan template cloning agar layout, heading, kolom, style, spacing, dan border tetap sama.
- Desain harus mengikuti gaya Playful Professional di Stitch: magenta `#e6007f`, warm plum charcoal `#3A2C33`, Plus Jakarta Sans, Work Sans, pill button, rounded cards, dan layout responsive.
- Validasi dan amankan semua input user, upload file, pembayaran, login, dan data siswa.

## Aturan Route Web

- `context/WEB_ROUTES_ETC.md` adalah acuan utama untuk route web: owner, method, URI, route name, controller action, middleware, layout, dan referensi halaman.
- Semua URI/path dan route name harus memakai Bahasa Inggris sesuai dokumen tersebut, misalnya `/programs`, `/registration/payment/{registration}`, `public.programs.show`, dan `admin.registrations.index`.
- Jangan membuat nama route atau URI baru jika route yang dibutuhkan sudah ada di `WEB_ROUTES_ETC.md`. Jika butuh route tambahan, tambahkan dulu ke dokumen tersebut dengan owner yang jelas.
- Ikuti pembagian owner pada `WEB_ROUTES_ETC.md`: Rasky, Miftah, Mecca, dan Mia bertanggung jawab pada area route masing-masing.
- Route public memakai name prefix `public.` kecuali flow pendaftaran yang memakai `registrations.`.
- Route dashboard wajib memakai prefix URI dan name sesuai role: `/admin` + `admin.`, `/student` + `student.`, dan `/instructor` + `instructor.`.
- Route dashboard wajib dilindungi middleware `auth` dan role yang sesuai. Student hanya boleh mengakses data miliknya sendiri.
- Gunakan route model binding untuk resource utama seperti `{program}`, `{registration}`, `{reel}`, `{reportCard}`, dan `{class}`.
- Untuk action workflow seperti upload bukti pembayaran, verify/reject payment, schedule placement test, publish report card, reels view/like, dan chatbot message, ikuti method dan route name di `WEB_ROUTES_ETC.md`.
- Saat menambahkan link di Blade/component, gunakan route name dari `WEB_ROUTES_ETC.md`; sediakan fallback hanya untuk komponen navigasi reusable yang memang harus aman ketika route belum tersedia.

## Aturan Controller

- Controller harus tipis: terima request, panggil FormRequest/service, lalu return response/redirect/view.
- Jangan menaruh business logic, query kompleks, upload file, generate dokumen, atau perubahan status workflow langsung di controller; gunakan service yang sesuai.
- Gunakan FormRequest untuk validasi create/update yang punya banyak field, terutama registrasi, pembayaran, upload file, rapor, reels, dan CMS.
- Gunakan route model binding untuk resource utama, misalnya `Program $program` atau `Reel $reel`.
- Gunakan authorization middleware/policy sebelum aksi admin/student yang sensitif.
- Gunakan redirect dengan flash message untuk web CRUD; gunakan response JSON hanya untuk endpoint API/AJAX yang memang dirancang begitu.
- Jangan menerima mass assignment mentah dari `$request->all()`; gunakan `$request->validated()` atau array eksplisit.
- Untuk upload, controller hanya menerima file dan menyerahkannya ke service; storage lifecycle ditangani service.
- Untuk resource admin, gunakan pola nama controller yang jelas seperti `Admin\ProgramController`, `Admin\RegistrationController`, dan `Admin\ReelController`.

## Aturan View Blade

- View hanya untuk presentasi; jangan menaruh query database atau business logic di Blade.
- Data yang dibutuhkan view harus disiapkan controller/service terlebih dahulu.
- Gunakan layout dan component Blade reusable untuk navbar, sidebar, footer, form field, button, card, table, badge status, alert, modal, dan pagination.
- Untuk halaman public, gunakan layout `resources/views/components/layouts/public.blade.php` dengan sintaks `<x-layouts.public title="Judul Halaman">...</x-layouts.public>`. Layout ini otomatis memuat navbar public dan footer ETC Planet.
- Untuk halaman dashboard admin/student/instructor, gunakan layout `resources/views/components/layouts/dashboard.blade.php` dengan sintaks `<x-layouts.dashboard title="Dashboard Admin" area="admin">...</x-layouts.dashboard>`. Nilai `area` yang didukung: `admin`, `student`, dan `instructor`.
- Gunakan komponen navigasi siap pakai:
  - `resources/views/components/site/navbar.blade.php` untuk navbar public desktop dan bottom nav mobile.
  - `resources/views/components/site/footer.blade.php` untuk footer public.
  - `resources/views/components/dashboard/sidebar.blade.php` untuk sidebar dashboard desktop dan bottom nav dashboard mobile.
- Jangan menduplikasi markup navbar, sidebar, bottom navigation, atau footer di halaman baru. Jika butuh menu khusus, kirim prop `items`, `active`, `loginUrl`, `registerUrl`, `linkGroups`, `socialLinks`, `contact`, atau `sidebarItems` ke komponen/layout yang sesuai.
- Route pada komponen navigasi harus tetap defensif: gunakan route name jika tersedia dan sediakan fallback URL agar halaman tidak error saat route belum dibuat.
- Gunakan `@csrf`, `@method`, `@error`, `old()`, dan flash message untuk form web.
- Escape output default dengan `{{ }}`. Gunakan `{!! !!}` hanya untuk konten CMS yang sudah disanitasi dan memang perlu HTML.
- Pisahkan view public, admin, dan student, misalnya `resources/views/public`, `resources/views/admin`, dan `resources/views/student`.
- Ikuti desain Stitch dan `DESIGN.md`: warna `#e6007f` dan `#3A2C33`, tipografi Plus Jakarta Sans/Work Sans, spacing konsisten, card rounded, button pill, dan mobile responsive.
- Hindari duplikasi markup panjang; pindahkan pola berulang ke Blade component atau partial.
- Pastikan state kosong, error, loading, dan success terlihat jelas untuk tabel/dashboard/form.
- Jangan hardcode data bisnis jika sudah tersedia dari database, service, atau config.

## Command Referensi

```bash
composer test
php artisan test
npm run build
npm run dev
php artisan serve
```

## Catatan Implementasi

- Role utama: `admin`, `student`, `instructor`.
- Placement test tetap offline; sistem hanya menyimpan jadwal, hasil, dan assignment kelas.
- Admin memverifikasi pembayaran sebelum siswa masuk proses placement test.
- Student hanya boleh melihat data dan rapor miliknya sendiri.
- Admin bertanggung jawab publish rapor sebelum siswa bisa download.

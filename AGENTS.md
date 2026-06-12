# AGENTS.md

Panduan ringkas untuk agen/developer yang bekerja di project ETC Planet.

## Sumber Wajib

Sebelum mengubah kode, baca sumber yang relevan:

- `PROJECT_BLUEPRINT_ETC.md`
- `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`
- `context/WEB_ROUTES_ETC.md`
- `context/SKEMA_DATABASE_LENGKAP.md`
- `context/FORMULIR PENDAFTARAN.jpeg`
- `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`
- `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`
- `context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md`
- Referensi HTML/screenshot di `context/stitch_etc_planet_digital_hub/`

Folder `context/` adalah sumber kebenaran project.

## Stack

- Laravel 13, PHP 8.3, MySQL
- Blade, Vite, Tailwind CSS 4

## Prinsip Kerja

- Ikuti pola Laravel standar: route, controller, FormRequest, service, model, policy/middleware, Blade.
- Jangan mengubah file yang tidak terkait tugas.
- Jangan menghapus atau mengganti file konteks/template contoh.
- Validasi dan amankan semua input user, upload, pembayaran, login, dan data siswa.
- Controller harus tipis; business logic, query kompleks, upload, status workflow, dan document generation taruh di service.
- Jangan pakai `$request->all()` untuk mass assignment; gunakan `$request->validated()` atau array eksplisit.
- Gunakan route model binding dan authorization untuk resource sensitif.

## Route Web

- `context/WEB_ROUTES_ETC.md` adalah inventaris route project: method, URI, route name, controller action, middleware, layout, dan notes.
- Ownership, sprint, prioritas, dan pembagian kerja developer mengikuti `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.
- URI/path dan route name wajib Bahasa Inggris, contoh: `/programs`, `/registration/payment/{registration}`, `public.programs.show`, `admin.registrations.index`.
- Jangan membuat URI/route name baru jika sudah ada di `WEB_ROUTES_ETC.md`. Jika perlu tambahan atau perubahan route, update dokumen route itu tanpa menambah kolom owner.
- Gunakan controller action, bukan closure, kecuali route sementara yang sangat kecil.
- Gunakan HTTP method sesuai fungsi: `GET` read/page, `POST` create/action, `PUT/PATCH` update, `DELETE` delete/cancel.
- Route statis harus ditulis sebelum route dinamis, misalnya `/programs` sebelum `/programs/{program}`.
- Gunakan parameter singular konsisten: `{program}`, `{registration}`, `{payment}`, `{reel}`, `{content}`, `{reportCard}`, `{student}`, `{instructor}`, `{class}`.
- Workflow non-CRUD seperti `verify`, `reject`, `publish`, `schedule`, `result`, `proof`, `confirm`, `views`, dan `likes` harus route eksplisit.
- Setelah mengubah route, cek `php artisan route:list`; jalankan `php artisan test` jika menyentuh flow yang punya test.

## Struktur File Route

`routes/web.php` hanya loader. Route bisnis dipisah agar tidak konflik saat dikerjakan paralel.

```text
routes/web.php
routes/web/public.php
routes/web/auth.php
routes/web/registration.php
routes/web/student.php
routes/web/instructor.php
routes/web/admin/dashboard.php
routes/web/admin/registrations.php
routes/web/admin/payments.php
routes/web/admin/academic.php
routes/web/admin/content.php
routes/web/admin/reports.php
```

Aturan:

- File route baru wajib di-`require` dari `routes/web.php` dengan urutan: public, auth, registration, student, admin, instructor.
- Jika `routes/web.php` sudah membungkus `Route::middleware('web')->group(...)`, file turunannya tidak perlu mengulang middleware `web`.
- Gunakan group `prefix`, `name`, dan `middleware` agar tidak mengulang string.
- Prefix/name role:
  - Public: `public.`
  - Registration: `registrations.`
  - Admin: `/admin` + `admin.`
  - Student: `/student` + `student.`
  - Instructor: `/instructor` + `instructor.`
- Dashboard wajib middleware `auth` + role sesuai area.
- Pembagian owner mengikuti `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.

## Blade Dan UI

- View hanya untuk presentasi; jangan taruh query database atau business logic di Blade.
- Gunakan data dari controller/service, bukan hardcode data bisnis yang sudah tersedia dari DB/config.
- Gunakan layout/component reusable:
  - Public: `<x-layouts.public title="Judul Halaman">...</x-layouts.public>`
  - Dashboard: `<x-layouts.dashboard title="Dashboard Admin" area="admin">...</x-layouts.dashboard>`
  - Navbar: `resources/views/components/site/navbar.blade.php`
  - Footer: `resources/views/components/site/footer.blade.php`
  - Sidebar: `resources/views/components/dashboard/sidebar.blade.php`
- Jangan duplikasi markup navbar, sidebar, bottom navigation, atau footer.
- Gunakan route name dari `WEB_ROUTES_ETC.md` untuk link. Fallback URL hanya untuk komponen navigasi reusable yang harus aman saat route belum tersedia.
- Gunakan `@csrf`, `@method`, `@error`, `old()`, flash message, dan escape output dengan `{{ }}`.
- Page utama mengikuti struktur target `resources/views/pages/public`, `resources/views/pages/admin`, `resources/views/pages/student`, dan `resources/views/pages/instructor`. View flow lama yang belum dimigrasikan tetap dipisah per area.

## Desain

- Ikuti Stitch dan `DESIGN.md`.
- Brand: magenta `#e6007f`, warm plum charcoal `#3A2C33`, Plus Jakarta Sans, Work Sans.
- Gunakan pill button, rounded card, spacing konsisten, layout responsive, dan state kosong/error/loading/success yang jelas.

## Dokumen Dan Form Penting

- Form registrasi wajib mengikuti field/pilihan di `context/FORMULIR PENDAFTARAN.jpeg`.
- Rapor wajib mengikuti template `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`.
- Rekap siswa wajib mengikuti template `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`.
- Document generation harus template cloning agar layout, heading, kolom, style, spacing, dan border tetap sama.

## Catatan Domain

- Role utama: `admin`, `student`, `instructor`.
- Placement test tetap offline; sistem hanya menyimpan jadwal, hasil, dan assignment kelas.
- Admin memverifikasi pembayaran sebelum siswa masuk proses placement test.
- Student hanya boleh melihat data dan rapor miliknya sendiri.
- Admin publish rapor sebelum siswa bisa download.

## Command Referensi

```bash
composer test
php artisan test
php artisan route:list
npm run build
npm run dev
php artisan serve
```

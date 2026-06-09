# Dokumentasi Keseluruhan Project ETC Planet

Terakhir diperbarui: 9 Juni 2026

Dokumen ini adalah dokumentasi induk untuk project `etc-company-profile`. Isinya merangkum tujuan project, stack, struktur folder, arsitektur, modul fitur, database, alur kerja, validasi, testing, deployment awal, dan catatan pengembangan berikutnya.

## 1. Identitas Project

| Item | Keterangan |
| --- | --- |
| Nama project | ETC Planet Company Profile |
| Mitra | LKP ETC / ETC Planet Padang |
| Repository | `https://github.com/STACKD-Team/etc-company-profile` |
| Lokasi aplikasi di workspace | `etc-company-profile/` |
| Jenis aplikasi | Website company profile dan sistem operasional awal lembaga kursus |
| Stack utama | Laravel 13, PHP 8.3, MySQL, Blade, Vite, Tailwind CSS 4 |
| Testing | Pest dan Laravel feature test |
| Output dokumen | Export rekap siswa XLSX dan export rapor DOC/HTML |

Project ini dibuat untuk mendigitalisasi proses promosi, discovery program, pendaftaran online, pembayaran awal, pengelolaan data siswa, kelas, rapor, reels, CMS, kontak, dan chatbot sederhana.

## 2. Latar Belakang

Sebelum sistem ini dibuat, sebagian besar proses ETC Planet masih dilakukan secara manual melalui WhatsApp, Instagram, formulir fisik, dan Excel. Kondisi tersebut membuat informasi program sulit ditemukan secara mandiri, data pendaftaran tersebar, verifikasi pembayaran bergantung pada komunikasi manual, dan rekap akademik perlu disusun ulang.

Project ini menyatukan alur tersebut ke dalam satu aplikasi web:

- Pengunjung bisa melihat profil lembaga, program, galeri, reels, FAQ, dan kontak.
- Calon siswa bisa memilih program, mengisi formulir pendaftaran, memilih metode pembayaran, mengunggah bukti, dan melihat konfirmasi.
- Siswa bisa masuk ke dashboard untuk melihat profil, kelas, riwayat belajar, pembayaran, bantuan, dan rapor yang sudah dipublish.
- Admin bisa mengelola pendaftaran, pembayaran, placement test, siswa, instructor, program, kelas, enrollment, rapor, export, reels, CMS, pesan kontak, chatbot log, dan setting.
- Instructor bisa melihat dashboard ringkas, kelas, siswa, dan rapor terkait kelas yang diajar.

## 3. Target Pengguna

| Role | Kebutuhan Utama |
| --- | --- |
| Pengunjung / calon siswa | Menemukan informasi program, melihat kredibilitas lembaga, menghubungi ETC, dan mendaftar. |
| Orang tua | Melihat informasi program, biaya, fasilitas, jadwal, dan alur pendaftaran. |
| Student | Mengakses data kelas, riwayat pembelajaran, pembayaran, bantuan, dan rapor. |
| Admin | Mengelola operasional akademik, pembayaran, konten website, dan laporan. |
| Instructor | Melihat kelas yang diajar, siswa terkait, dan rapor yang relevan. |

## 4. Ringkasan Stack

### Backend

- `laravel/framework ^13.7` sebagai framework utama.
- PHP `^8.3`.
- MySQL untuk database utama.
- Laravel session, cache, queue, dan migration bawaan.
- FormRequest untuk validasi input.
- Service layer untuk business logic.
- Middleware role sederhana untuk `admin`, `student`, dan `instructor`.

### Frontend

- Blade untuk server-rendered views.
- Vite sebagai build tool.
- Tailwind CSS 4 melalui `@tailwindcss/vite`.
- CSS tambahan di `public/css/` untuk halaman lama atau halaman khusus.
- JavaScript utama di `resources/js/app.js`, termasuk interaksi chatbot public.

### Package Penting

| Package | Fungsi |
| --- | --- |
| `laravel/tinker` | Console interaktif untuk development. |
| `maatwebsite/excel` | Dicatat sebagai dependency export Excel, walaupun export saat ini juga memiliki generator XLSX manual di `DocumentExportService`. |
| `pestphp/pest` | Framework test. |
| `pestphp/pest-plugin-laravel` | Integrasi Pest dengan Laravel. |
| `laravel/pint` | Formatter kode PHP. |
| `vite` | Build asset frontend. |
| `tailwindcss` | Utility CSS untuk UI responsive. |

Dokumentasi dependency lebih detail ada di `docs/dependency.md`.

## 5. Struktur Folder Project

```text
etc-company-profile/
|-- app/
|   |-- Http/
|   |   |-- Controllers/
|   |   |-- Middleware/
|   |   `-- Requests/
|   |-- Models/
|   |-- Providers/
|   `-- Services/
|-- bootstrap/
|-- config/
|-- context/
|-- database/
|   |-- factories/
|   |-- migrations/
|   `-- seeders/
|-- docs/
|-- public/
|   |-- css/
|   |-- images/
|   `-- videos/
|-- resources/
|   |-- css/
|   |-- js/
|   `-- views/
|-- routes/
|   `-- web/
|-- storage/
|-- tests/
|-- composer.json
|-- package.json
|-- phpunit.xml
`-- vite.config.js
```

| Folder | Fungsi |
| --- | --- |
| `app/Http/Controllers` | Controller untuk public, auth, registration, student, admin, dan instructor. |
| `app/Http/Requests` | Validasi request untuk form public, admin, dan student. |
| `app/Http/Middleware` | Middleware `role` dan `signed.optional`. |
| `app/Models` | Model Eloquent untuk tabel utama. |
| `app/Services` | Business logic, CRUD, upload media, discovery public, registration, report card, dan export. |
| `config` | Konfigurasi aplikasi, database, filesystem, Firebase, dan detail program. |
| `context` | Sumber kebenaran desain, skema, template dokumen, dan referensi project. |
| `database/migrations` | Definisi tabel aplikasi. |
| `database/seeders` | Data awal admin, program, instructor, pages, setting, room, gallery, dan reels. |
| `docs` | Dokumentasi teknis project. |
| `public` | Asset publik, CSS lama, gambar, video, dan entry `index.php`. |
| `resources/views` | Blade views untuk semua halaman. |
| `routes/web` | Route web yang dipisah per modul. |
| `tests` | Unit dan feature tests berbasis Pest. |

## 6. Arsitektur Aplikasi

Project memakai pola Laravel MVC dengan tambahan service layer.

```text
Route -> Controller -> FormRequest -> Service -> Model -> Database
                         |
                         -> Blade View / Redirect / JSON / Download
```

Prinsip implementasi:

- Route dipisah per area agar mudah dikerjakan paralel oleh tim.
- Controller dibuat tipis dan fokus menerima request, memanggil service, lalu mengembalikan response.
- Validasi form dipindahkan ke FormRequest.
- Query dan workflow bisnis dipusatkan di service.
- View memakai Blade component dan layout agar konsisten.
- Akses dashboard dibatasi dengan middleware `auth` dan `role`.

## 7. Routing

File `routes/web.php` berfungsi sebagai loader:

```php
require __DIR__.'/web/public.php';
require __DIR__.'/web/auth.php';
require __DIR__.'/web/registration.php';
require __DIR__.'/web/student.php';
require __DIR__.'/web/admin/dashboard.php';
require __DIR__.'/web/admin/registrations.php';
require __DIR__.'/web/admin/payments.php';
require __DIR__.'/web/admin/academic.php';
require __DIR__.'/web/admin/content.php';
require __DIR__.'/web/admin/reports.php';
require __DIR__.'/web/instructor.php';
```

### Public Routes

| URI | Fungsi |
| --- | --- |
| `/` | Beranda ETC Planet. |
| `/about` | Profil lembaga. |
| `/programs` | Daftar program aktif. |
| `/programs/{program}` | Detail program dengan route model binding slug. |
| `/team` | Daftar instructor yang ditampilkan di halaman team. |
| `/facilities` | Fasilitas/ruangan dari CMS. |
| `/gallery` | Galeri kegiatan dari CMS. |
| `/contact` | Form kontak dan informasi lokasi. |
| `/faq` | FAQ public. |
| `/reels` | Feed reels public. |
| `/reels/{reel}` | Detail reels yang sudah publish. |
| `/chatbot/messages` | Endpoint JSON untuk chatbot public. |

### Registration Routes

| URI | Fungsi |
| --- | --- |
| `/registration` | Entry point yang redirect ke pilih program. |
| `/registration/programs` | Halaman pilih program. |
| `/registration/form/{program?}` | Form pendaftaran online. |
| `/registration` `POST` | Simpan pendaftaran. |
| `/registration/payment/{registration}` | Halaman pembayaran. |
| `/registration/payment/{registration}/proof` | Upload bukti pembayaran. |
| `/registration/payment/{registration}/confirm` | Konfirmasi pembayaran manual. |
| `/registration/confirmation/{registration}` | Halaman konfirmasi. |
| `/registration/{registration}/receipt` | Download bukti pendaftaran digital. |

### Auth Routes

| URI | Fungsi |
| --- | --- |
| `/login` | Login admin, student, dan instructor. |
| `/logout` | Logout. |
| `/forgot-password` | Form lupa password. |
| `/reset-password/{token}` | Form reset password. |

### Student Routes

Semua route student memakai middleware `auth` dan `role:student`.

| URI | Fungsi |
| --- | --- |
| `/student/dashboard` | Dashboard siswa. |
| `/student/profile` | Profil siswa dan update data tertentu. |
| `/student/classes` | Kelas siswa. |
| `/student/classes/{class}` | Detail kelas milik siswa. |
| `/student/learning-history` | Riwayat pembelajaran. |
| `/student/payments` | Riwayat pembayaran. |
| `/student/report-cards` | Daftar rapor published milik siswa. |
| `/student/report-cards/{reportCard}` | Detail rapor published. |
| `/student/report-cards/{reportCard}/download` | Download file rapor. |
| `/student/help` | Bantuan siswa. |

### Admin Routes

Semua route admin memakai middleware `auth` dan `role:admin`.

| Area | Route Utama |
| --- | --- |
| Dashboard | `/admin/dashboard` |
| Registrations | `/admin/registrations`, detail, edit, update |
| Payments | `/admin/payments`, verify, reject |
| Placement test | `/admin/placement-tests`, schedule, result |
| Academic master data | `/admin/students`, `/admin/instructors`, `/admin/programs`, `/admin/classes`, `/admin/enrollments` |
| Report cards | `/admin/report-cards`, create, store, show, edit, update, publish |
| Exports | `/admin/exports/students`, `/admin/exports/report-cards` |
| Content | `/admin/reels`, `/admin/contents`, `/admin/contact-messages`, `/admin/chatbot-logs`, `/admin/settings` |

### Instructor Routes

Semua route instructor memakai middleware `auth` dan `role:instructor`.

| URI | Fungsi |
| --- | --- |
| `/instructor/dashboard` | Dashboard instructor. |
| `/instructor/classes` | Kelas yang diajar. |
| `/instructor/classes/{class}` | Detail kelas instructor. |
| `/instructor/students` | Siswa dari kelas instructor. |
| `/instructor/report-cards` | Rapor terkait instructor. |

Dokumentasi kontrak route lengkap ada di `context/WEB_ROUTES_ETC.md`.

## 8. Role dan Hak Akses

| Role | Area Akses | Catatan |
| --- | --- | --- |
| Guest | Public website, registration, payment/confirmation dengan optional signed URL, login, forgot password | Tidak boleh mengakses dashboard. |
| Student | Student dashboard | Hanya boleh melihat data miliknya sendiri. |
| Admin | Admin dashboard dan seluruh modul operasional | Role paling luas untuk proses akademik, pembayaran, CMS, dan laporan. |
| Instructor | Instructor dashboard | Scope v1 masih ringkas. |

Middleware penting:

- `EnsureUserHasRole` mengecek nilai `users.role`.
- `OptionalSigned` menolak request jika URL memiliki `signature` tetapi signature tidak valid.
- `bootstrap/app.php` mengarahkan guest ke `/login`.

## 9. Modul Fitur

### 9.1 Public Website

Modul public website bertujuan menjadi profil resmi ETC Planet. Data utama diambil dari tabel `programs`, `contents`, `reels`, dan `users` role instructor.

Fitur:

- Beranda dengan hero, highlight program, statistik, reels, CTA pendaftaran, dan chatbot.
- About berdasarkan konten CMS `page` slug `about`.
- Program index dan detail program.
- Team berdasarkan instructor yang `show_on_team_page = true`.
- Facilities berdasarkan CMS type `room`.
- Gallery berdasarkan CMS type `gallery`.
- Contact form yang tersimpan ke `contact_messages`.
- FAQ dari CMS atau fallback default.
- Reels public untuk video yang sudah publish.
- Chatbot rule-based untuk pertanyaan program, biaya, jadwal, pendaftaran, dan kontak.

Controller utama:

- `Public\HomeController`
- `Public\AboutController`
- `Public\ProgramController`
- `Public\TeamController`
- `Public\FacilityController`
- `Public\GalleryController`
- `Public\ContactController`
- `Public\FaqController`
- `Public\ReelController`
- `Public\ChatbotController`

Service utama:

- `PublicDiscoveryService`
- `ContactMessageService`
- `ChatbotLogService`
- `ReelService`

### 9.2 Registration dan Payment Flow

Alur pendaftaran:

1. Calon siswa membuka `/registration/programs`.
2. Calon siswa memilih program aktif.
3. Calon siswa mengisi form di `/registration/form/{program?}`.
4. `StoreRegistrationRequest` memvalidasi data sesuai kebutuhan form fisik ETC.
5. `RegistrationService::createFromOnlineForm()` membuat atau memperbarui akun student dan membuat registration.
6. Sistem menghitung `payment_amount` dari `registration_fee + price`.
7. Calon siswa masuk ke halaman pembayaran.
8. Calon siswa memilih `qris` atau `bank_transfer`.
9. Calon siswa bisa upload bukti pembayaran atau mengonfirmasi pembayaran.
10. Admin memverifikasi pembayaran.
11. Admin menjadwalkan placement test offline.
12. Admin mengisi hasil placement test dan assign class.
13. Enrollment dapat dibuat untuk siswa.

Status registration:

```text
pending_payment -> paid -> placement_test -> enrolled
                                      -> rejected
                                      -> cancelled
```

Data penting:

- `registration_code` dibuat otomatis dengan format `REG-YYYYMMDD-XXXXXX`.
- Snapshot nama, email, dan nomor telepon disimpan di `registrations`.
- Profil detail siswa disimpan di `users`.
- Catatan teknis tambahan disimpan di `registrations.notes` sebagai JSON.
- Bukti pembayaran disimpan melalui `MediaStorageService`.

### 9.3 Auth

Auth menggunakan mekanisme Laravel:

- Login memakai email dan password.
- Setelah login, user diarahkan sesuai role:
  - `admin` ke `admin.dashboard`
  - `student` ke `student.dashboard`
  - `instructor` ke `instructor.dashboard`
- Logout menghapus session dan redirect ke public home.
- Forgot password dan reset password tersedia.

Seeder membuat akun admin awal:

```text
email: admin@etcplanet.test
password: password
```

Gunakan hanya untuk development atau demo lokal.

### 9.4 Student Dashboard

Student dashboard menyediakan:

- Ringkasan profil dan aktivitas.
- Profil siswa yang dapat diperbarui pada field tertentu.
- Daftar kelas aktif dan detail kelas milik siswa.
- Riwayat pembelajaran untuk enrollment completed atau dropped.
- Riwayat pembayaran dari registration milik siswa.
- Daftar dan detail report card yang sudah dipublish.
- Download report card jika `pdf_path` tersedia.
- Halaman bantuan.

Proteksi data:

- Student tidak boleh melihat kelas milik siswa lain.
- Student tidak boleh melihat report card milik siswa lain.
- Student tidak boleh melihat report card yang belum dipublish.
- Download report card gagal jika file tidak tersedia.

### 9.5 Admin Dashboard

Admin dashboard adalah pusat operasional.

Area utama:

- Dashboard statistik.
- Pendaftaran dan detail calon siswa.
- Verifikasi pembayaran.
- Placement test schedule dan result.
- Data siswa dan instructor.
- Master program.
- Master kelas.
- Enrollment siswa ke kelas.
- Report card create, preview, update, publish.
- Export rekap siswa dan report card.
- Reels management.
- CMS content management.
- Contact message inbox.
- Chatbot log.
- Settings alamat, kontak, rekening, dan QRIS.

Controller admin mengikuti namespace `App\Http\Controllers\Admin`.

### 9.6 Instructor Dashboard

Instructor dashboard v1 masih ringkas:

- Dashboard instructor.
- Kelas yang diajar.
- Detail kelas.
- Siswa terkait.
- Report card terkait kelas instructor.

Beberapa view instructor masih memakai pola shared atau placeholder internal yang sudah diuji agar route dapat diakses oleh role instructor.

### 9.7 CMS, Reels, Contact, dan Chatbot

CMS memakai tabel `contents`.

Type konten:

| Type | Fungsi |
| --- | --- |
| `page` | Halaman statis seperti about dan FAQ. |
| `gallery` | Galeri kegiatan. |
| `partner` | Data partner/lembaga kerja sama. |
| `room` | Fasilitas/ruangan. |
| `team_member_extra` | Data team tambahan jika tidak berasal dari `users`. |
| `setting` | Alamat, email, telepon, rekening, QRIS, jam operasional, dan konfigurasi public. |

Reels memakai tabel `reels`.

Fitur reels:

- Admin upload video dan thumbnail.
- Admin publish atau unpublish.
- Public hanya melihat reels yang publish.
- View count bertambah lewat endpoint `/reels/{reel}/views`.
- Like count dapat toggle dalam session lewat `/reels/{reel}/likes`.

Chatbot saat ini rule-based:

- Mendeteksi intent dari kata kunci.
- Menjawab topik pricing, registration, schedule, program, contact, dan general.
- Menyimpan log ke `chatbot_logs`.

### 9.8 Report Cards dan Export

Report card disimpan di tabel `report_cards` dan berelasi satu-satu dengan `enrollments`.

Data rapor mencakup:

- Written test: listening, vocabulary, structure, reading, writing.
- Class assessment: pronunciation, sentence arrangement, class participation, questioning skill, analyzing skill.
- Total score dan final grade.
- Next class.
- Comments.
- Instructor, academic director, managing director.
- Issued date.
- PDF path.
- Publish status.

Alur rapor:

1. Admin membuat report card dari enrollment.
2. Admin preview report card.
3. Admin publish report card.
4. Student bisa melihat dan download jika report card published dan file tersedia.

Export saat ini:

- `DocumentExportService::studentWorkbook()` membuat file XLSX sederhana menggunakan `ZipArchive`.
- `DocumentExportService::reportCardsDoc()` membuat dokumen `.doc` berbasis HTML dari Blade partial.
- Target final berdasarkan blueprint tetap mengikuti template context:
  - `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`
  - `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`

## 10. Database

Project memiliki tabel utama berikut.

| Tabel | Fungsi |
| --- | --- |
| `users` | Akun admin, student, instructor, profil siswa, dan profil instructor. |
| `programs` | Master program kursus. |
| `classes` | Kelas konkret yang berjalan. |
| `registrations` | Pendaftaran online dan data pembayaran. |
| `enrollments` | Riwayat siswa mengikuti kelas. |
| `report_cards` | Rapor akhir pembelajaran. |
| `reels` | Video pendek public dan admin. |
| `contents` | CMS polymorphic untuk page, gallery, room, setting, dan lainnya. |
| `contact_messages` | Pesan dari form kontak. |
| `chatbot_logs` | Log interaksi chatbot. |

Tabel Laravel bawaan:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

### Relasi Utama

| Relasi | Keterangan |
| --- | --- |
| `Program hasMany CourseClass` | Satu program memiliki banyak kelas. |
| `Program hasMany Registration` | Satu program dapat dipilih banyak pendaftar. |
| `CourseClass belongsTo Program` | Kelas berada di bawah satu program. |
| `CourseClass belongsTo User instructor` | Kelas dapat memiliki instructor. |
| `User hasMany Registration` | Student dapat memiliki beberapa registration. |
| `User hasMany Enrollment` | Student dapat memiliki riwayat kelas. |
| `Enrollment belongsTo User` | Enrollment milik satu student. |
| `Enrollment belongsTo CourseClass` | Enrollment menghubungkan student ke kelas. |
| `Enrollment hasOne ReportCard` | Satu enrollment punya satu rapor. |
| `ReportCard belongsTo Enrollment` | Rapor terkait satu enrollment. |
| `ReportCard belongsTo User instructor/director` | Rapor menyimpan penanda tangan. |
| `ChatbotLog belongsTo User nullable` | Log bisa terkait user login atau anonymous. |

### Enum Penting

| Field | Nilai |
| --- | --- |
| `users.role` | `admin`, `instructor`, `student` |
| `users.sex` | `M`, `F` |
| `programs.category` | `english`, `mandarin`, `japanese`, `test_prep`, `soft_skills`, `other` |
| `programs.type` | `regular`, `private`, `one_on_one` |
| `programs.target_age` | `kids`, `teen`, `adult`, `university`, `all` |
| `classes.status` | `upcoming`, `ongoing`, `completed`, `cancelled` |
| `registrations.status` | `pending_payment`, `paid`, `placement_test`, `enrolled`, `rejected`, `cancelled` |
| `registrations.payment_method` | `qris`, `bank_transfer`, `virtual_account`, `ewallet`, `manual` |
| `enrollments.status` | `active`, `completed`, `dropped` |
| `report_cards.final_grade` | `A`, `B`, `C`, `D` |
| `reels.category` | `promosi`, `dokumentasi`, `edukasi`, `testimoni`, `event` |
| `contents.type` | `page`, `gallery`, `partner`, `room`, `team_member_extra`, `setting` |

## 11. Service Layer

| Service | Fungsi |
| --- | --- |
| `BaseCrudService` | Operasi CRUD umum, pagination, filter, soft delete, restore, force delete. |
| `RegistrationService` | Online registration, payment proof, payment confirmation, admin payment verification, placement test, class assignment, enrollment from registration. |
| `ProgramService` | CRUD program dan upload thumbnail. |
| `CourseClassService` | CRUD kelas dan filter program/instructor/status. |
| `EnrollmentService` | CRUD enrollment, complete, drop. |
| `ReportCardService` | CRUD report card, attach PDF, publish, unpublish. |
| `DocumentExportService` | Generate XLSX rekap siswa dan DOC/HTML rapor. |
| `MediaStorageService` | Upload, replace, dan delete file ke storage local atau Firebase jika tersedia. |
| `PublicDiscoveryService` | Query konten public, program highlight, settings, FAQ, stats, dan jawaban chatbot. |
| `ReelService` | CRUD reels, upload video/thumbnail, publish, unpublish, views, likes. |
| `ContentService` | CRUD CMS, upload image/images, update settings. |
| `ContactMessageService` | Simpan, baca, dan tandai pesan kontak. |
| `ChatbotLogService` | Simpan log chatbot dan filter admin. |
| `UserService` | CRUD user dan avatar. |
| `AdminDashboardService` | Statistik dashboard admin dan pendaftaran terbaru. |

## 12. Validasi dan Keamanan

Validasi utama:

- `StoreRegistrationRequest` memvalidasi program aktif, data pribadi, data alamat, data orang tua, PIP/KPS/KIP, pilihan jadwal, dan pilihan jam.
- `StorePaymentProofRequest` menerima metode `qris` atau `bank_transfer`, file `jpg`, `jpeg`, `png`, atau `pdf`, maksimal 5 MB.
- `ConfirmRegistrationPaymentRequest` menerima metode `qris` atau `bank_transfer`.
- `StoreProgramRequest` dan `UpdateProgramRequest` memvalidasi master program.
- `StoreCourseClassRequest` memvalidasi kelas dan instructor role.
- `StoreEnrollmentRequest` mencegah duplicate assignment user dan class.
- `SaveReportCardRequest` memvalidasi score, grade, director, instructor, dan issued date.
- `StoreReelRequest` dan `UpdateReelRequest` memvalidasi video dan thumbnail.
- `StoreContentRequest` dan `UpdateContentRequest` memvalidasi CMS.
- `UpdateProfileRequest` membatasi field profil yang boleh diedit student.

Keamanan yang sudah diterapkan:

- CSRF protection bawaan Laravel untuk form.
- Route dashboard memakai `auth`.
- Role dashboard memakai middleware `role`.
- Public POST route memakai throttle seperti `throttle:contact`, `throttle:chatbot`, `throttle:registration`, `throttle:upload`, dan `throttle:payment`.
- Login dan forgot password memakai throttle.
- Password di-hash.
- Query student dashboard membatasi data sesuai user login.
- Signed URL optional dipakai untuk halaman payment, confirmation, dan receipt.

Catatan:

- Permission masih berbasis enum role sederhana, belum memakai package seperti `spatie/laravel-permission`.
- Audit trail khusus belum tersedia selain timestamps dan status.

## 13. File Storage

Storage upload ditangani oleh `MediaStorageService`.

Perilaku saat ini:

- Jika Firebase credentials dan bucket tidak tersedia, file disimpan ke disk `public`.
- Jika Firebase credentials, bucket, dan class `Kreait\Firebase\Factory` tersedia, service mencoba upload ke Firebase Storage.
- `.env.example` sudah menyediakan variabel:

```env
FIREBASE_CREDENTIALS=
FIREBASE_STORAGE_BUCKET=
```

Catatan penting:

- `kreait/firebase-php` belum tercatat di `composer.json` project ini.
- Untuk kondisi repo saat ini, fallback local storage adalah jalur yang paling siap dipakai.
- Jika Firebase benar-benar dipakai, tambahkan dependency yang sesuai, isi credential, dan uji upload/delete file.

## 14. Konfigurasi Environment

File referensi: `.env.example`.

Konfigurasi utama:

```env
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=etc_company_profile
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
FILESYSTEM_DISK=local
```

Catatan:

- `APP_NAME` di `.env.example` masih `Laravel`; untuk branding lokal dapat diganti menjadi `ETC Planet`.
- `FILESYSTEM_DISK=local`, sedangkan upload public fallback memakai `Storage::disk('public')` di service.
- Pastikan symbolic link storage dibuat jika file public perlu diakses browser:

```bash
php artisan storage:link
```

## 15. Instalasi Lokal

Langkah ringkas:

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

Windows PowerShell untuk menyalin env:

```powershell
Copy-Item .env.example .env
```

Buka aplikasi:

```text
http://127.0.0.1:8000
```

Panduan lebih detail ada di `docs/installation.md`.

## 16. Menjalankan Development

Backend server:

```bash
php artisan serve
```

Vite development server:

```bash
npm run dev
```

Menjalankan server, queue, dan Vite bersama lewat Composer script:

```bash
composer dev
```

Build production asset:

```bash
npm run build
```

Bersihkan cache Laravel:

```bash
php artisan optimize:clear
```

## 17. Testing

Project memakai Pest.

Menjalankan semua test:

```bash
php artisan test
```

Atau:

```bash
composer test
```

Area yang sudah dicakup test:

- Route public, auth, admin, student, instructor.
- Middleware role dan redirect guest.
- Public program listing dan detail.
- Registration program picker.
- Registration flow dan payment flow.
- Student dashboard, profile update, class access, learning history, payment history, report card access, dan download.
- Admin registrations, payments, academic master data, placement test, report card, export, content, reels, settings.
- Public discovery, contact, chatbot, reels view/like.
- Service layer CRUD dan media lifecycle.
- Export XLSX dan DOC response.

Catatan:

- Banyak feature test memakai `RefreshDatabase`.
- Pastikan database test siap sebelum menjalankan test.
- Jika memakai MySQL lokal, sesuaikan `.env.testing` bila diperlukan.

## 18. CI/CD

Saat dokumentasi ini dibuat, folder `.github/workflows` belum tersedia di repo lokal. File `docs/github-actions.md` berisi rencana workflow CI.

Rencana CI:

- Checkout code.
- Setup PHP 8.3.
- Install Composer dependency.
- Setup Node.js.
- Install NPM dependency.
- Build asset.
- Prepare `.env`.
- Generate app key.
- Jalankan test.

Jika workflow final dibuat, lokasi yang disarankan:

```text
.github/workflows/ci.yml
```

## 19. Data Seeder

Seeder utama:

- `DatabaseSeeder`
- `PublicDiscoverySeeder`

Data awal yang disediakan:

- Admin default `admin@etcplanet.test`.
- Program contoh `English Conversation`.
- Instructor contoh.
- Page CMS seperti about dan FAQ.
- Settings public seperti alamat, email, telepon, jam operasional, dan pembayaran.
- Room/facilities.
- Gallery.
- Reels contoh.

Seeder menggunakan `updateOrCreate`, sehingga aman dijalankan ulang untuk data awal yang sama.

## 20. Design System

Desain mengacu pada folder `context/stitch_etc_planet_digital_hub/` dan dokumen:

```text
context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md
```

Prinsip visual:

- Brand personality: playful professional.
- Warna utama magenta `#e6007f`.
- Heading memakai Plus Jakarta Sans.
- Body memakai Work Sans.
- Public website lebih ekspresif dan ramah.
- Dashboard lebih padat, scannable, dan operasional.

Layout penting:

- `resources/views/components/layouts/public.blade.php`
- `resources/views/components/layouts/dashboard.blade.php`
- `resources/views/components/site/navbar.blade.php`
- `resources/views/components/site/footer.blade.php`
- `resources/views/components/site/chatbot.blade.php`
- `resources/views/components/dashboard/sidebar.blade.php`

## 21. Referensi Context

Folder `context/` adalah sumber kebenaran project.

File penting:

| File | Fungsi |
| --- | --- |
| `context/Project_Charter_ETC_Updated.pdf` | Charter project. |
| `context/SKEMA_DATABASE_LENGKAP.md` | Skema database acuan. |
| `context/WEB_ROUTES_ETC.md` | Kontrak route tim. |
| `context/FORMULIR PENDAFTARAN.jpeg` | Acuan form pendaftaran fisik. |
| `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc` | Template rapor. |
| `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx` | Template rekap siswa. |
| `context/stitch_etc_planet_digital_hub/` | Screenshot dan HTML referensi desain. |
| `context/DEPENDENCY_PACKAGE_5W1H.md` | Catatan dependency praktikum. |

## 22. Dokumentasi Terkait

| Dokumen | Isi |
| --- | --- |
| `README.md` | Ringkasan project, instalasi singkat, fitur, stack, dan link dokumentasi. |
| `docs/installation.md` | Panduan instalasi lokal. |
| `docs/features.md` | Dokumentasi fitur per modul. |
| `docs/dependency.md` | Dependency, alasan, risiko, dan maintenance. |
| `docs/refactoring.md` | Catatan refactoring dan pola arsitektur. |
| `docs/github-actions.md` | Rencana GitHub Actions CI. |
| `docs/laporan-praktikum-dependency.md` | Laporan praktikum dependency. |
| `docs/implementasi-halaman-pilih-program.md` | Catatan implementasi awal halaman pilih program. |
| `CHANGELOG.md` | Catatan perubahan project. |

## 23. Batasan Implementasi Saat Ini

Beberapa hal yang perlu diketahui saat membaca kode:

- Export rapor dan rekap siswa sudah tersedia secara fungsional, tetapi belum sepenuhnya melakukan template cloning dari file context.
- GitHub Actions belum dibuat sebagai file workflow aktual.
- Chatbot masih rule-based, belum terhubung ke AI API.
- Payment gateway seperti Midtrans atau Xendit belum terintegrasi; alur masih QRIS atau transfer manual.
- Permission masih berbasis role sederhana.
- Firebase Storage bersifat opsional dan belum memiliki dependency Firebase di `composer.json`.
- Screenshot di README masih mengacu ke referensi `context`, bukan hasil screenshot final aplikasi.
- Beberapa dokumen lama seperti `docs/implementasi-halaman-pilih-program.md` merekam tahap awal dan mungkin tidak lagi mencerminkan struktur route/view terbaru.

## 24. Roadmap Pengembangan

Prioritas pengembangan berikutnya:

1. Finalisasi template-based export untuk rapor DOC dan rekap siswa XLSX sesuai file context.
2. Tambahkan workflow `.github/workflows/ci.yml`.
3. Perbarui screenshot README dengan hasil aplikasi aktual.
4. Lengkapi audit trail untuk aksi admin penting.
5. Tambahkan storage strategy final: local public, S3, atau Firebase.
6. Tambahkan payment gateway jika dibutuhkan oleh mitra.
7. Perluas dashboard instructor jika scope operasional bertambah.
8. Tambahkan authorization policy jika akses data makin granular.
9. Perbarui `.env.example` agar branding default memakai ETC Planet.
10. Tambahkan dokumentasi deployment production.

## 25. Checklist Serah Terima

Checklist untuk memastikan project siap dipresentasikan atau diserahkan:

- `.env` sudah dibuat dan database sudah dikonfigurasi.
- `composer install` berhasil.
- `npm install` berhasil.
- `php artisan key:generate` sudah dijalankan.
- `php artisan migrate --seed` berhasil.
- `php artisan storage:link` dijalankan jika butuh akses file upload.
- `npm run build` berhasil.
- `php artisan test` berhasil.
- Admin default bisa login.
- Public website bisa dibuka.
- Flow pendaftaran bisa dibuat sampai konfirmasi.
- Admin bisa melihat registration dan payment.
- Admin bisa membuat dan publish report card.
- Student bisa melihat report card published miliknya.
- Export siswa dan report card bisa diunduh.
- README dan dokumentasi sudah diperbarui.

## 26. Troubleshooting Ringkas

### Composer gagal karena versi PHP

Target project adalah PHP `^8.3`. Jika memakai PHP terlalu baru dan dependency transitif belum kompatibel, gunakan PHP 8.3 atau 8.4 untuk development. Workaround lokal tercatat di `docs/installation.md`.

### Asset tidak muncul

Jalankan:

```bash
npm run build
php artisan optimize:clear
```

Untuk development, jalankan:

```bash
npm run dev
```

### Upload tidak bisa diakses public

Pastikan storage link tersedia:

```bash
php artisan storage:link
```

Periksa juga `FILESYSTEM_DISK` dan permission folder `storage`.

### Route dashboard redirect ke login

Pastikan sudah login dengan role yang sesuai. Admin harus memakai `role = admin`, student harus `role = student`, instructor harus `role = instructor`.

### Test gagal karena database

Pastikan database test tersedia dan konfigurasi `.env.testing` sesuai. Jika memakai MySQL, database harus dibuat lebih dulu. Jika memakai SQLite, sesuaikan konfigurasi test.

## 27. Ringkasan Singkat

ETC Planet Company Profile adalah aplikasi Laravel fullstack untuk profil lembaga kursus sekaligus sistem operasional awal. Struktur project sudah mencakup public website, registration/payment flow, dashboard student, dashboard admin, dashboard instructor, CMS, reels, chatbot, report cards, export, service layer, migrations, seeders, dan feature tests.

Dokumen ini menjadi pintu masuk utama. Untuk detail lebih dalam, gunakan dokumen terkait di `docs/` dan sumber kebenaran di `context/`.

# Sprint 0 Mecca Student Panel Audit

Tanggal audit: 2026-06-09

Owner: Mecca

Sumber aktif: `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`

Catatan sumber: `context/WEB_ROUTES_ETC.md` tidak dipakai sebagai kontrak ownership terbaru karena dokumen pembagian tugas menandainya deprecated.

## Ringkasan

Sprint 0 untuk area Mecca berfokus pada alignment, audit, foundation, dan kontrak lanjutan Student Panel. Implementasi student panel sudah memiliki route, controller, view, middleware role student, validasi profile, guard kepemilikan data utama, dan test feature yang lulus.

Status Sprint 0 Mecca: lengkap untuk deliverable audit/foundation.

Yang sudah dikunci:

- Laravel sudah bisa boot setelah vendor Filament dipulihkan.
- Route Student Panel aktual sudah diverifikasi dengan `php artisan route:list --name=student`.
- Test target Mecca dan payment history yang tampil di Student Panel sudah lulus.
- Daftar gap Student Panel sudah terdokumentasi untuk Sprint 1/Sprint 2.
- Kontrak status untuk payment, enrollment, report card, dan fallback registration sudah dikunci.

## Boot Dan Dependency

Masalah awal:

- Laravel gagal boot dengan error `Class "Filament\PanelProvider" not found`.
- Penyebab langsung: paket Filament belum tersedia di `vendor`.
- `composer install` awal juga gagal karena `composer.json` meminta `maatwebsite/excel`, tetapi package itu tidak ada di `composer.lock`.
- `maatwebsite/excel ^3.1` tidak installable di environment audit ini karena konflik PHP 8.5.3 dan Laravel 13, dan tidak ada pemakaian `Maatwebsite`/`Excel` di kode runtime saat ini.

Tindakan Sprint 0:

- `composer.json` diselaraskan dengan lockfile aktif dengan menghapus require `maatwebsite/excel` yang tidak terpakai dan tidak terkunci.
- `composer install --no-scripts` berhasil.
- `composer dump-autoload --no-scripts` berhasil.
- `php artisan package:discover --ansi` berhasil dan Filament terdeteksi.
- `phpunit.xml` dikunci memakai SQLite in-memory untuk environment testing agar feature test tidak bergantung pada service MySQL lokal.

Catatan lanjutan:

- Jika export Excel ingin memakai library eksternal lagi, pilih package yang kompatibel dengan Laravel 13/PHP target pada sprint milik owner export, bukan Sprint 0 Mecca.
- Ada warning Composer non-blocking: `FakeMediaStorageService` di `tests/Feature/Services/CrudServicesTest.php` tidak mengikuti PSR-4. Warning ini tidak memengaruhi test Mecca.

## Route Inventory Student Panel

Route Student Panel memakai group `prefix('student')`, name `student.`, dan middleware `auth` + `role:student` di `routes/web/student.php`.

| Method | URI | Route name | Controller/action | Status |
| --- | --- | --- | --- | --- |
| GET | `/student/dashboard` | `student.dashboard` | `Student\DashboardController@index` | Verified |
| GET | `/student/profile` | `student.profile.show` | `Student\ProfileController@show` | Verified |
| PUT | `/student/profile` | `student.profile.update` | `Student\ProfileController@update` | Verified |
| GET | `/student/classes` | `student.classes.index` | `Student\ClassController@index` | Verified |
| GET | `/student/classes/{class}` | `student.classes.show` | `Student\ClassController@show` | Verified |
| GET | `/student/learning-history` | `student.learning-history.index` | `Student\LearningHistoryController@index` | Verified |
| GET | `/student/payments` | `student.payments.index` | `Student\PaymentController@index` | Verified |
| GET | `/student/payments/{payment}` | `student.payments.show` | `Student\PaymentController@show` | Verified |
| GET | `/student/report-cards` | `student.report-cards.index` | `Student\ReportCardController@index` | Verified |
| GET | `/student/report-cards/{reportCard}` | `student.report-cards.show` | `Student\ReportCardController@show` | Verified |
| GET | `/student/report-cards/{reportCard}/download` | `student.report-cards.download` | `Student\ReportCardDownloadController` | Verified |
| GET | `/student/help` | `student.help.index` | `Student\HelpController@index` | Verified |

Catatan route list:

- `php artisan route:list --name=student` juga menampilkan route seperti `admin.students.*` dan `instructor.students.index` karena pencarian route list berbasis substring.
- Route yang dihitung sebagai scope Mecca Sprint 0 terbaru adalah route Student Panel di atas.

## Page Audit

| Page | Route | Controller dan view | Ownership guard | Data guard | Sprint 0 status | Gap untuk sprint berikutnya |
| --- | --- | --- | --- | --- | --- | --- |
| Student dashboard | `student.dashboard` | Ada | `auth`, `role:student` | Query enrollments, registrations, dan report cards memakai user login | Verified | Migrasi card/action ke `x-ui.*`, rapikan status pembayaran terbaru, tambah empty state konsisten |
| Profile show/update | `student.profile.show`, `student.profile.update` | Ada | `auth`, `role:student`; `UpdateProfileRequest` authorize student | Update hanya pada user login dan field tervalidasi | Verified | Ganti raw input/select/textarea/button dengan `x-ui.field`, `x-ui.select`, `x-ui.textarea`, `x-ui.button` |
| Classes index | `student.classes.index` | Ada | `auth`, `role:student` | Query enrollment dari user login | Verified | Tambah filter status `active/completed/dropped`, badge status `x-ui.badge`, empty state `x-ui.empty-state` |
| Class detail | `student.classes.show` | Ada | `auth`, `role:student` | Detail dicari dari enrollment milik user login; class lain 404 | Verified | Badge status dan action pakai shared component |
| Learning history | `student.learning-history.index` | Ada | `auth`, `role:student` | Query enrollment user login dengan status `completed/dropped` | Verified | Jadikan list lebih mudah discan, pakai `x-ui.data-table` atau pattern list yang disepakati |
| Payments index | `student.payments.index` | Ada | `auth`, `role:student` | Query registration/payment berdasarkan `user_id` login | Verified | Sesuaikan label Midtrans, hilangkan fokus bukti manual pada sprint integrasi, pakai `x-ui.data-table` jika tetap list/table |
| Payment detail | `student.payments.show` | Ada | `auth`, `role:student` | `abort_unless($payment->user_id === user id, 403)` | Verified | Tambah instruksi lanjut bayar saat Snap token/redirect tersedia, ubah bukti upload menjadi legacy/deprecated |
| Report cards index | `student.report-cards.index` | Ada | `auth`, `role:student` | Hanya enrollment user login dan report published | Verified | Gunakan `x-ui.badge`, `x-ui.button`, dan empty state konsisten |
| Report card detail | `student.report-cards.show` | Ada | `auth`, `role:student` | Report harus published dan enrollment milik user login | Verified | Tampilkan assessment lebih lengkap sesuai template rapor |
| Report card download | `student.report-cards.download` | Ada | `auth`, `role:student` | Report harus published, milik user, dan file ada di storage | Verified | Nanti sesuaikan dengan Cloudinary/private document service jika integrasi storage berubah |
| Help/chatbot | `student.help.index` | Ada | `auth`, `role:student` | Tidak menampilkan data sensitif | Verified | Integrasikan chatbot UI student yang konsisten dengan public chatbot saat RAG siap |

## Status Convention

Payment status untuk tampilan Student Panel:

- `waiting_payment`: transaksi dibuat dan menunggu pembayaran.
- `paid`: pembayaran berhasil.
- `expired`: transaksi kedaluwarsa.
- `failed`: transaksi gagal.
- `cancelled`: transaksi dibatalkan.

Enrollment status:

- `active`: siswa masih mengikuti kelas.
- `completed`: kelas selesai.
- `dropped`: siswa keluar atau tidak melanjutkan kelas.

Report card status:

- `published`: rapor boleh dilihat dan diunduh siswa.
- `unpublished`: rapor belum boleh tampil untuk siswa.

Registration fallback status yang masih perlu didukung selama transisi payment lama ke Midtrans:

- `pending_payment`
- `paid`
- `placement_test`
- `enrolled`
- `rejected`
- `cancelled`

Badge color default lintas Student Panel:

- Success: `paid`, `active`, `completed`, `published`, `enrolled`.
- Warning: `waiting_payment`, `pending_payment`, `placement_test`.
- Danger: `expired`, `failed`, `cancelled`, `rejected`.
- Gray: `dropped`, `unpublished`.

## Prioritas Next Sprint Mecca

1. Student dashboard: tampilkan status kelas aktif, pembayaran terakhir, rapor terbaru, dan bantuan secara lebih compact.
2. Profile: migrasi seluruh field ke wrapper `x-ui.*`, kelompokkan data panjang dalam section yang mudah dibaca.
3. Classes: bedakan active/completed/dropped dengan filter, badge, dan empty state konsisten.
4. Learning history: ubah menjadi list/table yang mudah dipahami siswa dan orang tua.
5. Payments: ubah narasi dari upload bukti manual ke status otomatis Midtrans saat kontrak Mia siap.
6. Report cards: pertahankan guard published-only dan lengkapi tampilan assessment sesuai template.
7. Help/chatbot: siapkan UI bantuan siswa yang konsisten dengan chatbot public dan siap memakai service RAG.

## Verification

Command yang dijalankan:

```bash
composer install --no-scripts
composer dump-autoload --no-scripts
php artisan package:discover --ansi
php artisan route:list --name=student
php artisan route:list --path=student
php artisan test --filter=MeccaRoutesTest
php artisan test --filter=MiaStudentPaymentHistoryTest
```

Hasil:

- `composer install --no-scripts`: passed.
- `composer dump-autoload --no-scripts`: passed, dengan warning PSR-4 non-blocking di test helper lama.
- `php artisan package:discover --ansi`: passed.
- `php artisan route:list --name=student`: passed, 17 hasil substring, 12 route Student Panel terverifikasi.
- `php artisan route:list --path=student`: passed, hasil substring yang sama untuk route dengan kata student.
- `php artisan test --filter=MeccaRoutesTest`: passed, 22 tests, 195 assertions.
- `php artisan test --filter=MiaStudentPaymentHistoryTest`: passed, 4 tests, 17 assertions.

Acceptance Sprint 0 Mecca:

- Daftar gap per area Student Panel tersedia.
- Daftar route/halaman prioritas Student Panel tersedia.
- Standar status, badge, empty state, dan table/action untuk Student Panel sudah dikunci sebagai kontrak.
- Route dan test target bisa dijalankan tanpa MySQL lokal karena testing memakai SQLite in-memory.

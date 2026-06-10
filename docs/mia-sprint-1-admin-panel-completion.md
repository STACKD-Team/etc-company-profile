# Mia Sprint 1 Admin Panel Completion

Dokumen ini menjadi artefak penutupan Sprint 1 untuk area Mia berdasarkan `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.

## Scope Sprint 1

Sprint 1 Mia berfokus pada UI/UX improvement admin panel:

- Dashboard admin sebagai pusat operasional yang compact.
- List/detail CRUD admin lebih mudah discan.
- Filter, status badge, pagination, action, dan empty state konsisten.
- Pattern awal disiapkan untuk migrasi Filament Resource di Sprint 2.

Integrasi besar seperti Midtrans otomatis, Cloudinary penuh, RAG, Qdrant, dan resource Filament lengkap tidak dikerjakan di Sprint 1.

## Checklist Halaman

| Area | Status Sprint 1 | Catatan |
| --- | --- | --- |
| Admin dashboard | Selesai | Summary tetap compact, latest registrations memakai badge dan empty state shared. |
| Registrations list/detail | Selesai | List memakai `x-ui.data-table`, filter, safe sort, badge, action, pagination; detail dibuat compact. |
| Payments list/detail | Selesai | Monitoring manual tetap dipertahankan; list/detail memakai badge dan component shared. |
| Students list/detail | Selesai | List memakai table shared; detail menampilkan histori kelas dari `enrollments` beserta link rapor jika ada. |
| Instructors list/detail | Selesai | List dan detail dibuat compact, dengan badge tampil/internal dan ringkasan kelas. |
| Programs list | Selesai | Search, filter, safe sort, badge active, action edit, empty state. |
| Classes list | Selesai | Search, filter program/instructor/status, safe sort, badge status. |
| Enrollments list | Selesai | Form assign memakai wrapper component; list memakai search, filter, safe sort, badge. |
| Reels list | Selesai | Table compact dengan preview media, filter, sort, publish badge. |
| Contents/CMS list | Selesai | Table compact dengan preview image, type badge, publish badge, filter, sort. |
| Contact messages | Selesai | Inbox memakai table shared, status baca badge, filter, sort. |
| Chatbot logs | Selesai | Table shared dengan filter intent/feedback/tanggal dan session search. |
| Settings | Selesai | Field utama memakai `x-ui.*`, QRIS preview tetap tersedia. |
| Placement/report/export admin | Dipoles ringan | Tombol, empty state, dan badge dirapikan tanpa mengubah workflow Rasky. |

## Pattern Sprint 2 Filament

Resource naming yang direkomendasikan:

- `ProgramResource`, `ProgramPromotionResource`, `CourseClassResource`, `StudentResource`, `InstructorResource`
- `RegistrationResource`, `PaymentResource`, `EnrollmentResource`, `ReportCardResource`
- `ReelResource`, `ContentResource`, `ContactMessageResource`, `ChatbotLogResource`, `SettingResource`, `KnowledgeSourceResource`

Table pattern:

- Global search untuk kolom identitas utama.
- Filter status/type/program/instructor/date sesuai entity.
- Safe sortable columns saja: `created_at`, `updated_at`, `name/title`, `status`, `type`, `program_id`, `is_published`, dan count/amount yang sederhana.
- Status selalu memakai mapping `x-ui.badge` atau Filament BadgeColumn dengan warna yang sama.
- Action standar: view/detail, edit, publish/unpublish jika relevan, verify/reject hanya sampai Midtrans menggantikan workflow manual.

Form pattern:

- Form panjang dibagi section: identitas, program/jadwal, pembayaran, media, metadata, status.
- Upload field tetap tervalidasi MIME/size di FormRequest.
- Sprint 2 boleh mengganti Blade form menjadi Filament Form schema tanpa mengubah kontrak data.

## Gap Sprint 2

- Belum membuat Filament Resource penuh di `app/Filament/Resources`.
- Belum mengganti semua create/edit form lama menjadi Filament Form schema.
- Belum mengimplementasikan Midtrans otomatis; verify/reject manual masih dipertahankan.
- Belum migrasi storage ke Cloudinary penuh.
- Belum membuat Program Promotions, Knowledge Sources, RAG indexing, dan Qdrant integration.

## Verifikasi

Perintah verifikasi yang dijalankan untuk penutupan Sprint 1:

- `php artisan route:list --path=admin`: sukses, 58 route admin.
- `php artisan test tests/Feature/MiaRegistrationFlowTest.php tests/Feature/MiaAdminIntakeWorkflowTest.php tests/Feature/MiaStudentPaymentHistoryTest.php`: passed, 11 tests, 88 assertions.
- `php artisan test tests/Feature/WebLayerRoutesTest.php tests/Feature/FoundationComplianceTest.php`: passed, 29 tests, 161 assertions.
- `php artisan test tests/Feature/MiftahSprint6AdminTest.php`: passed, 8 tests, 142 assertions.
- `php artisan test tests/Feature/MeccaRoutesTest.php`: passed, 22 tests, 195 assertions.
- `npm.cmd run build`: sukses. `npm run build` langsung via PowerShell gagal karena execution policy `npm.ps1`, lalu dijalankan ulang memakai shim Windows `npm.cmd`.

Catatan: Pest menampilkan warning result cache tidak bisa ditulis ke `vendor/pestphp/pest/.temp/test-results` karena permission, tetapi semua test di atas tetap `passed`.

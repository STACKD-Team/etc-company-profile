# Mia Sprint 3 Source Alignment

Dokumen ini menutup Sprint 3 area Mia berdasarkan `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.

## Scope

Sprint 3 Mia diperlakukan sebagai source alignment dan route/page convention. Tidak ada perubahan URL admin atau slug Filament pada sprint ini.

Keputusan implementasi:

- Admin CRUD/RD Mia saat ini memakai Filament sebagai canonical surface di `/admin/...`.
- Blade admin lama tetap dipertahankan sebagai compatibility layer di `/admin/legacy/...` dengan route name `admin.*`.
- Route target singular seperti `/admin/student`, `/admin/payment`, dan struktur `resources/views/pages/admin/...` tetap menjadi backlog migrasi, bukan perubahan Sprint 3.

## Route Alignment

Canonical Filament route Mia yang dipakai saat ini:

- `filament.admin.resources.registrations.*` di `/admin/registrations`.
- `filament.admin.resources.payments.*` di `/admin/payments`.
- `filament.admin.resources.programs.*` di `/admin/programs`.
- `filament.admin.resources.program-promotions.*` di `/admin/program-promotions`.
- `filament.admin.resources.gallery-items.*` di `/admin/gallery-items`.
- `filament.admin.resources.partners.*` di `/admin/partners`.
- `filament.admin.resources.chatbot-logs.*` di `/admin/chatbot-logs`.
- `filament.admin.resources.contact-messages.*` di `/admin/contact-messages`.
- `filament.admin.resources.settings.*` di `/admin/settings`.
- `filament.admin.resources.rag-knowledge-sources.*` di `/admin/rag-knowledge-sources`.

Legacy Blade route names yang masih dijaga:

- `admin.dashboard` di `/admin/legacy/dashboard`.
- `admin.registrations.*` di `/admin/legacy/registrations`.
- `admin.payments.*` di `/admin/legacy/payments`.
- `admin.programs.*`, `admin.contents.*`, `admin.reels.*`, `admin.chatbot-logs.*`, dan route legacy lain tetap di `/admin/legacy/...`.

Webhook payment:

- `payments.midtrans.notification` di `POST /payments/midtrans/notification`.

## Page Convention

Target dokumen Sprint 3 untuk Blade page role-specific adalah `resources/views/pages/{role}/...`.

Untuk Mia, target ini tidak dipindahkan sekarang karena admin CRUD/RD canonical sudah menggunakan Filament Resource classes di `app/Filament/Resources`. View lama di `resources/views/admin` tetap dianggap legacy compatibility sampai migrasi Blade benar-benar diperlukan.

## Schema Alignment

Status schema saat Sprint 3:

- Payment snapshot punya dua kontrak aktif:
  - `payment_*` untuk Student Panel dan legacy payment display.
  - `midtrans_*`, `original_amount`, `discount_amount`, dan `final_amount` untuk Midtrans/Admin Filament.
- Duplicate migration blocker pada `registrations.payment_status` dan `registrations.payment_expires_at` sudah dijaga agar test database dapat migrate ulang.
- Rooms dan CMS simplification (`rooms`, `classes.room_id`, dan pembatasan `contents.type`) tetap backlog Sprint 5, bukan scope Sprint 3.

## Verification

Test alignment yang menjaga Sprint 3 Mia:

- `tests/Feature/MiaSprint3SourceAlignmentTest.php`

Perintah verifikasi yang relevan:

- `php artisan route:list --path=admin`
- `php artisan route:list --path=payments`
- `php artisan test tests/Feature/MiaSprint3SourceAlignmentTest.php`
- `php artisan test tests/Feature/MiaRegistrationFlowTest.php tests/Feature/MiaAdminIntakeWorkflowTest.php tests/Feature/MiaStudentPaymentHistoryTest.php`

# WEB ROUTES ETC PLANET

Dokumen ini adalah inventaris route web project ETC Planet. File ini hanya mencatat route, URI, route name, controller/action, middleware, layout, dan catatan teknis. Pembagian kerja developer mengikuti `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.

Catatan: route target untuk Sprint 3+ boleh berbeda dari route aktual di tabel ini. Selama route Laravel belum diubah, tabel ini tetap mencatat route yang benar-benar ada di project.

Sumber pengecekan route aktual:

```bash
php artisan route:list
```

## Aturan Umum

- Semua URI/path dan route name memakai Bahasa Inggris.
- Teks UI tetap boleh Bahasa Indonesia sesuai kebutuhan halaman.
- Halaman public memakai layout `<x-layouts.public>`.
- Halaman dashboard memakai layout `<x-layouts.dashboard area="admin|student|instructor">`.
- Gunakan controller action, bukan closure, untuk route bisnis.
- Gunakan route model binding untuk resource utama, misalnya `{program}`, `{registration}`, `{payment}`, `{reel}`, `{content}`, `{reportCard}`, `{student}`, `{instructor}`, atau `{class}`.
- Route dashboard wajib memakai middleware `auth` dan role sesuai area.
- Student hanya boleh melihat data miliknya sendiri.
- Route action seperti submit form, upload, verify, reject, publish, schedule, result, confirm, view count, dan like tetap didokumentasikan karena memengaruhi flow halaman.
- Route framework/vendor seperti Livewire asset routes, `storage/{path}`, `up`, dan Filament import/export internals tidak dicatat di inventaris ini.

## Public Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/` | `public.home` | `Public\HomeController@index` | `web` | `<x-layouts.public>` beranda |
| GET | `/about` | `public.about` | `Public\AboutController@index` | `web` | `<x-layouts.public>` profil ETC |
| GET | `/team` | `public.team.index` | `Public\TeamController@index` | `web` | `<x-layouts.public>` team/instructor public |
| GET | `/facilities` | `public.facilities.index` | `Public\FacilityController@index` | `web` | `<x-layouts.public>` fasilitas |
| GET | `/gallery` | `public.gallery.index` | `Public\GalleryController@index` | `web` | `<x-layouts.public>` galeri public |
| GET | `/contact` | `public.contact.index` | `Public\ContactController@index` | `web` | `<x-layouts.public>` form kontak |
| POST | `/contact` | `public.contact.store` | `Public\ContactController@store` | `web`, `throttle:contact` | Simpan pesan kontak |
| GET | `/faq` | `public.faq.index` | `Public\FaqController@index` | `web` | `<x-layouts.public>` FAQ |
| POST | `/chatbot/messages` | `public.chatbot.messages.store` | `Public\ChatbotController@store` | `web`, `throttle:chatbot` | JSON chatbot public |
| GET | `/programs` | `public.programs.index` | `Public\ProgramController@index` | `web` | `<x-layouts.public>` daftar program |
| GET | `/programs/{program}` | `public.programs.show` | `Public\ProgramController@show` | `web` | `<x-layouts.public>` detail program |
| GET | `/reels` | `public.reels.index` | `Public\ReelController@index` | `web` | `<x-layouts.public>` feed reels |
| GET | `/reels/{reel}` | `public.reels.show` | `Public\ReelController@show` | `web` | `<x-layouts.public>` detail/player reels |
| POST | `/reels/{reel}/views` | `public.reels.views.store` | `Public\ReelViewController@store` | `web`, `throttle:reels` | Increment view |
| POST | `/reels/{reel}/likes` | `public.reels.likes.store` | `Public\ReelLikeController@store` | `web`, `throttle:reels` | Like/unlike reels |

## Auth Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/login` | `auth.login` | `Auth\AuthenticatedSessionController@create` | `web`, `guest` | `<x-layouts.public>` login |
| POST | `/login` | `auth.login.store` | `Auth\AuthenticatedSessionController@store` | `web`, `guest`, `throttle:login` | Submit login |
| POST | `/logout` | `auth.logout` | `Auth\AuthenticatedSessionController@destroy` | `web`, `auth` | Logout |
| GET | `/forgot-password` | `auth.password.request` | `Auth\PasswordResetLinkController@create` | `web`, `guest` | Form lupa password |
| POST | `/forgot-password` | `auth.password.email` | `Auth\PasswordResetLinkController@store` | `web`, `guest`, `throttle:password` | Kirim reset link |
| GET | `/reset-password/{token}` | `auth.password.reset` | `Auth\NewPasswordController@create` | `web`, `guest` | Form reset password |
| POST | `/reset-password` | `auth.password.update` | `Auth\NewPasswordController@store` | `web`, `guest` | Update password |

## Registration Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/registration` | `registrations.start` | `Public\RegistrationStartController@index` | `web` | Entry point pendaftaran |
| GET | `/registration/programs` | `registrations.programs.index` | `Public\RegistrationProgramController@index` | `web` | Pilih program |
| GET | `/registration/form/{program?}` | `registrations.create` | `Public\RegistrationController@create` | `web` | Form pendaftaran |
| POST | `/registration` | `registrations.store` | `Public\RegistrationController@store` | `web`, `throttle:registration` | Simpan pendaftaran |
| GET | `/registration/payment/{registration}` | `registrations.payment.show` | `Public\RegistrationPaymentController@show` | `web`, `signed.optional` | Halaman pembayaran |
| POST | `/registration/payment/{registration}/proof` | `registrations.payment.proof.store` | `Public\RegistrationPaymentProofController@store` | `web`, `throttle:upload` | Upload bukti bayar |
| POST | `/registration/payment/{registration}/confirm` | `registrations.payment.confirm` | `Public\RegistrationPaymentController@confirm` | `web`, `throttle:payment` | Konfirmasi pembayaran |
| GET | `/registration/confirmation/{registration}` | `registrations.confirmation.show` | `Public\RegistrationConfirmationController@show` | `web`, `signed.optional` | Konfirmasi pendaftaran |
| GET | `/registration/{registration}/receipt` | `registrations.receipt.download` | `Public\RegistrationReceiptController@download` | `web`, `signed.optional` | Download bukti pendaftaran |

## Payment Webhook Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| POST | `/payments/midtrans/notification` | `payments.midtrans.notification` | `Payment\MidtransNotificationController` | `web`, `throttle:payment` | Webhook Midtrans; validasi signature dan audit payload di service |

## Admin Routes

### Canonical Filament Admin Routes

Route admin CRUD/RD Mia saat ini canonical di Filament Resource. Route target singular Sprint 3+ tetap roadmap; route aktual di bawah ini dipertahankan agar admin panel tidak berubah URL.

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/admin` | `filament.admin.pages.dashboard` | `Filament\Pages\Dashboard` | Filament admin auth stack | Filament admin dashboard |
| GET | `/admin/login` | `filament.admin.auth.login` | `Filament\Auth\Pages\Login` | Filament admin guest stack | Filament admin login |
| POST | `/admin/logout` | `filament.admin.auth.logout` | `Filament\Auth\Http\Controllers\LogoutController` | Filament admin auth stack | Filament admin logout |
| GET | `/admin/registrations` | `filament.admin.resources.registrations.index` | `App\Filament\Resources\Registrations\Pages\ListRegistrations` | Filament admin auth stack | Canonical pendaftaran admin |
| GET | `/admin/registrations/create` | `filament.admin.resources.registrations.create` | `App\Filament\Resources\Registrations\Pages\CreateRegistration` | Filament admin auth stack | Create pendaftaran admin |
| GET | `/admin/registrations/{record}` | `filament.admin.resources.registrations.view` | `App\Filament\Resources\Registrations\Pages\ViewRegistration` | Filament admin auth stack | Detail pendaftaran admin |
| GET | `/admin/registrations/{record}/edit` | `filament.admin.resources.registrations.edit` | `App\Filament\Resources\Registrations\Pages\EditRegistration` | Filament admin auth stack | Edit pendaftaran admin |
| GET | `/admin/payments` | `filament.admin.resources.payments.index` | `App\Filament\Resources\Payments\Pages\ListPayments` | Filament admin auth stack | Monitoring pembayaran |
| GET | `/admin/payments/{record}` | `filament.admin.resources.payments.view` | `App\Filament\Resources\Payments\Pages\ViewPayment` | Filament admin auth stack | Detail pembayaran |
| GET | `/admin/programs` | `filament.admin.resources.programs.index` | `App\Filament\Resources\Programs\Pages\ListPrograms` | Filament admin auth stack | Canonical program admin |
| GET | `/admin/programs/create` | `filament.admin.resources.programs.create` | `App\Filament\Resources\Programs\Pages\CreateProgram` | Filament admin auth stack | Create program admin |
| GET | `/admin/programs/{record}` | `filament.admin.resources.programs.view` | `App\Filament\Resources\Programs\Pages\ViewProgram` | Filament admin auth stack | Detail program admin |
| GET | `/admin/programs/{record}/edit` | `filament.admin.resources.programs.edit` | `App\Filament\Resources\Programs\Pages\EditProgram` | Filament admin auth stack | Edit program admin |
| GET | `/admin/program-promotions` | `filament.admin.resources.program-promotions.index` | `App\Filament\Resources\ProgramPromotions\Pages\ListProgramPromotions` | Filament admin auth stack | Promo program |
| GET | `/admin/program-promotions/create` | `filament.admin.resources.program-promotions.create` | `App\Filament\Resources\ProgramPromotions\Pages\CreateProgramPromotion` | Filament admin auth stack | Create promo program |
| GET | `/admin/program-promotions/{record}` | `filament.admin.resources.program-promotions.view` | `App\Filament\Resources\ProgramPromotions\Pages\ViewProgramPromotion` | Filament admin auth stack | Detail promo program |
| GET | `/admin/program-promotions/{record}/edit` | `filament.admin.resources.program-promotions.edit` | `App\Filament\Resources\ProgramPromotions\Pages\EditProgramPromotion` | Filament admin auth stack | Edit promo program |
| GET | `/admin/course-classes` | `filament.admin.resources.course-classes.index` | `App\Filament\Resources\CourseClasses\Pages\ListCourseClasses` | Filament admin auth stack | Canonical class admin |
| GET | `/admin/students` | `filament.admin.resources.students.index` | `App\Filament\Resources\Students\Pages\ListStudents` | Filament admin auth stack | Canonical siswa admin |
| GET | `/admin/instructors` | `filament.admin.resources.instructors.index` | `App\Filament\Resources\Instructors\Pages\ListInstructors` | Filament admin auth stack | Canonical instructor admin |
| GET | `/admin/enrollments` | `filament.admin.resources.enrollments.index` | `App\Filament\Resources\Enrollments\Pages\ListEnrollments` | Filament admin auth stack | Canonical enrollment admin |
| GET | `/admin/report-cards` | `filament.admin.resources.report-cards.index` | `App\Filament\Resources\ReportCards\Pages\ListReportCards` | Filament admin auth stack | Canonical rapor admin |
| GET | `/admin/reels` | `filament.admin.resources.reels.index` | `App\Filament\Resources\Reels\Pages\ListReels` | Filament admin auth stack | Reels CMS |
| GET | `/admin/contents` | `filament.admin.resources.contents.index` | `App\Filament\Resources\Contents\Pages\ListContents` | Filament admin auth stack | CMS content |
| GET | `/admin/gallery-items` | `filament.admin.resources.gallery-items.index` | `App\Filament\Resources\GalleryItems\Pages\ListGalleryItems` | Filament admin auth stack | Gallery CMS |
| GET | `/admin/partners` | `filament.admin.resources.partners.index` | `App\Filament\Resources\Partners\Pages\ListPartners` | Filament admin auth stack | Kerja Sama ETC/partner CMS |
| GET | `/admin/contact-messages` | `filament.admin.resources.contact-messages.index` | `App\Filament\Resources\ContactMessages\Pages\ListContactMessages` | Filament admin auth stack | Pesan kontak |
| GET | `/admin/chatbot-logs` | `filament.admin.resources.chatbot-logs.index` | `App\Filament\Resources\ChatbotLogs\Pages\ListChatbotLogs` | Filament admin auth stack | Chatbot logs |
| GET | `/admin/settings` | `filament.admin.resources.settings.index` | `App\Filament\Resources\Settings\Pages\ListSettings` | Filament admin auth stack | Settings |
| GET | `/admin/rag-knowledge-sources` | `filament.admin.resources.rag-knowledge-sources.index` | `App\Filament\Resources\RagKnowledgeSources\Pages\ListRagKnowledgeSources` | Filament admin auth stack | Knowledge sources RAG |
| GET | `/admin/users` | `filament.admin.resources.users.index` | `App\Filament\Resources\Users\Pages\ListUsers` | Filament admin auth stack | User management |
| GET | `/admin/exports/students` | `admin.exports.students` | `Admin\StudentExportController@index` | `web`, `auth`, `role:admin` | Form export siswa |
| POST | `/admin/exports/students` | `admin.exports.students.download` | `Admin\StudentExportController@download` | `web`, `auth`, `role:admin` | Download export siswa |
| GET | `/admin/exports/report-cards` | `admin.exports.report-cards` | `Admin\ReportCardExportController@index` | `web`, `auth`, `role:admin` | Form export rapor |
| POST | `/admin/exports/report-cards` | `admin.exports.report-cards.download` | `Admin\ReportCardExportController@download` | `web`, `auth`, `role:admin` | Download export rapor |

### Legacy Blade Admin Routes

Route Blade lama tetap tersedia untuk kompatibilitas link/test lama. Route name tetap `admin.*`, tetapi URI aktual berada di `/admin/legacy/...`.

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/admin/legacy/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | `web`, `auth`, `role:admin` | Legacy dashboard Blade |
| GET | `/admin/legacy/registrations` | `admin.registrations.index` | `Admin\RegistrationController@index` | `web`, `auth`, `role:admin` | Legacy list pendaftaran |
| GET | `/admin/legacy/registrations/{registration}` | `admin.registrations.show` | `Admin\RegistrationController@show` | `web`, `auth`, `role:admin` | Legacy detail pendaftaran |
| GET | `/admin/legacy/registrations/{registration}/edit` | `admin.registrations.edit` | `Admin\RegistrationController@edit` | `web`, `auth`, `role:admin` | Legacy edit pendaftaran |
| PUT | `/admin/legacy/registrations/{registration}` | `admin.registrations.update` | `Admin\RegistrationController@update` | `web`, `auth`, `role:admin` | Legacy update pendaftaran |
| GET | `/admin/legacy/payments` | `admin.payments.index` | `Admin\PaymentController@index` | `web`, `auth`, `role:admin` | Legacy list pembayaran |
| GET | `/admin/legacy/payments/{payment}` | `admin.payments.show` | `Admin\PaymentController@show` | `web`, `auth`, `role:admin` | Legacy detail pembayaran |
| POST | `/admin/legacy/payments/{payment}/verify` | `admin.payments.verify` | `Admin\PaymentVerificationController@verify` | `web`, `auth`, `role:admin` | Legacy verify manual |
| POST | `/admin/legacy/payments/{payment}/reject` | `admin.payments.reject` | `Admin\PaymentVerificationController@reject` | `web`, `auth`, `role:admin` | Legacy reject manual |
| GET | `/admin/legacy/placement-tests` | `admin.placement-tests.index` | `Admin\PlacementTestController@index` | `web`, `auth`, `role:admin` | Legacy placement test |
| GET | `/admin/legacy/placement-tests/{registration}` | `admin.placement-tests.show` | `Admin\PlacementTestController@show` | `web`, `auth`, `role:admin` | Legacy detail placement test |
| POST | `/admin/legacy/placement-tests/{registration}/schedule` | `admin.placement-tests.schedule` | `Admin\PlacementTestScheduleController@store` | `web`, `auth`, `role:admin` | Jadwalkan placement test |
| POST | `/admin/legacy/placement-tests/{registration}/result` | `admin.placement-tests.result.store` | `Admin\PlacementTestResultController@store` | `web`, `auth`, `role:admin` | Simpan hasil placement test |
| GET | `/admin/legacy/students` | `admin.students.index` | `Admin\StudentController@index` | `web`, `auth`, `role:admin` | Legacy list siswa |
| GET | `/admin/legacy/students/{student}` | `admin.students.show` | `Admin\StudentController@show` | `web`, `auth`, `role:admin` | Legacy detail siswa |
| GET | `/admin/legacy/instructors` | `admin.instructors.index` | `Admin\InstructorController@index` | `web`, `auth`, `role:admin` | Legacy list instructor |
| GET | `/admin/legacy/instructors/{instructor}` | `admin.instructors.show` | `Admin\InstructorController@show` | `web`, `auth`, `role:admin` | Legacy detail instructor |
| GET | `/admin/legacy/programs` | `admin.programs.index` | `Admin\ProgramController@index` | `web`, `auth`, `role:admin` | Legacy list program |
| GET | `/admin/legacy/classes` | `admin.classes.index` | `Admin\ClassController@index` | `web`, `auth`, `role:admin` | Legacy list kelas |
| GET | `/admin/legacy/enrollments` | `admin.enrollments.index` | `Admin\EnrollmentController@index` | `web`, `auth`, `role:admin` | Legacy list enrollment |
| GET | `/admin/legacy/report-cards` | `admin.report-cards.index` | `Admin\ReportCardController@index` | `web`, `auth`, `role:admin` | Legacy list rapor |
| GET | `/admin/legacy/reels` | `admin.reels.index` | `Admin\ReelController@index` | `web`, `auth`, `role:admin` | Legacy list reels |
| GET | `/admin/legacy/contents` | `admin.contents.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | Legacy CMS content |
| GET | `/admin/legacy/contact-messages` | `admin.contact-messages.index` | `Admin\ContactMessageController@index` | `web`, `auth`, `role:admin` | Legacy pesan kontak |
| GET | `/admin/legacy/chatbot-logs` | `admin.chatbot-logs.index` | `Admin\ChatbotLogController@index` | `web`, `auth`, `role:admin` | Legacy chatbot logs |
| GET | `/admin/legacy/settings` | `admin.settings.index` | `Admin\SettingController@index` | `web`, `auth`, `role:admin` | Legacy settings |

## Student Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/student/dashboard` | `student.dashboard` | `Student\DashboardController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` |
| GET | `/student/profile` | `student.profile.show` | `Student\ProfileController@show` | `web`, `auth`, `role:student` | Profil siswa |
| PUT | `/student/profile` | `student.profile.update` | `Student\ProfileController@update` | `web`, `auth`, `role:student` | Update profil siswa |
| GET | `/student/classes` | `student.classes.index` | `Student\ClassController@index` | `web`, `auth`, `role:student` | List kelas siswa |
| GET | `/student/classes/{class}` | `student.classes.show` | `Student\ClassController@show` | `web`, `auth`, `role:student` | Detail kelas siswa |
| GET | `/student/learning-history` | `student.learning-history.index` | `Student\LearningHistoryController@index` | `web`, `auth`, `role:student` | Riwayat belajar |
| GET | `/student/report-cards` | `student.report-cards.index` | `Student\ReportCardController@index` | `web`, `auth`, `role:student` | List rapor siswa |
| GET | `/student/report-cards/{reportCard}` | `student.report-cards.show` | `Student\ReportCardController@show` | `web`, `auth`, `role:student` | Detail rapor siswa |
| GET | `/student/report-cards/{reportCard}/download` | `student.report-cards.download` | `Student\ReportCardDownloadController` | `web`, `auth`, `role:student` | Download rapor siswa |
| GET | `/student/payments` | `student.payments.index` | `Student\PaymentController@index` | `web`, `auth`, `role:student` | List pembayaran siswa |
| GET | `/student/payments/{payment}` | `student.payments.show` | `Student\PaymentController@show` | `web`, `auth`, `role:student` | Detail pembayaran siswa |
| GET | `/student/help` | `student.help.index` | `Student\HelpController@index` | `web`, `auth`, `role:student` | Bantuan siswa |

## Instructor Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/instructor/dashboard` | `instructor.dashboard` | `Instructor\DashboardController@index` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` |
| GET | `/instructor/classes` | `instructor.classes.index` | `Instructor\ClassController@index` | `web`, `auth`, `role:instructor` | List kelas instructor |
| GET | `/instructor/classes/{class}` | `instructor.classes.show` | `Instructor\ClassController@show` | `web`, `auth`, `role:instructor` | Detail kelas instructor |
| GET | `/instructor/students` | `instructor.students.index` | `Instructor\StudentController@index` | `web`, `auth`, `role:instructor` | List siswa instructor |
| GET | `/instructor/report-cards` | `instructor.report-cards.index` | `Instructor\ReportCardController@index` | `web`, `auth`, `role:instructor` | List rapor instructor |
| GET | `/instructor/enrollments/{enrollment}/report-card/create` | `instructor.report-cards.create` | `Instructor\ReportCardController@create` | `web`, `auth`, `role:instructor` | Form create rapor dari enrollment |
| POST | `/instructor/enrollments/{enrollment}/report-card` | `instructor.report-cards.store` | `Instructor\ReportCardController@store` | `web`, `auth`, `role:instructor` | Simpan rapor instructor |
| GET | `/instructor/report-cards/{reportCard}` | `instructor.report-cards.show` | `Instructor\ReportCardController@show` | `web`, `auth`, `role:instructor` | Detail rapor instructor |
| GET | `/instructor/report-cards/{reportCard}/edit` | `instructor.report-cards.edit` | `Instructor\ReportCardController@edit` | `web`, `auth`, `role:instructor` | Form edit rapor instructor |
| PUT | `/instructor/report-cards/{reportCard}` | `instructor.report-cards.update` | `Instructor\ReportCardController@update` | `web`, `auth`, `role:instructor` | Update rapor instructor |

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

## Admin Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/admin` | `filament.admin.pages.dashboard` | `Filament\Pages\Dashboard` | Filament admin auth stack | Filament admin dashboard |
| GET | `/admin/login` | `filament.admin.auth.login` | `Filament\Auth\Pages\Login` | Filament admin guest stack | Filament admin login |
| POST | `/admin/logout` | `filament.admin.auth.logout` | `Filament\Auth\Http\Controllers\LogoutController` | Filament admin auth stack | Filament admin logout |
| GET | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` |
| GET | `/admin/registrations` | `admin.registrations.index` | `Admin\RegistrationController@index` | `web`, `auth`, `role:admin` | List pendaftaran |
| GET | `/admin/registrations/{registration}` | `admin.registrations.show` | `Admin\RegistrationController@show` | `web`, `auth`, `role:admin` | Detail pendaftaran |
| GET | `/admin/registrations/{registration}/edit` | `admin.registrations.edit` | `Admin\RegistrationController@edit` | `web`, `auth`, `role:admin` | Form edit pendaftaran |
| PUT | `/admin/registrations/{registration}` | `admin.registrations.update` | `Admin\RegistrationController@update` | `web`, `auth`, `role:admin` | Update pendaftaran |
| GET | `/admin/payments` | `admin.payments.index` | `Admin\PaymentController@index` | `web`, `auth`, `role:admin` | List pembayaran |
| GET | `/admin/payments/{payment}` | `admin.payments.show` | `Admin\PaymentController@show` | `web`, `auth`, `role:admin` | Detail pembayaran |
| POST | `/admin/payments/{payment}/verify` | `admin.payments.verify` | `Admin\PaymentVerificationController@verify` | `web`, `auth`, `role:admin` | Verifikasi pembayaran |
| POST | `/admin/payments/{payment}/reject` | `admin.payments.reject` | `Admin\PaymentVerificationController@reject` | `web`, `auth`, `role:admin` | Tolak pembayaran |
| GET | `/admin/placement-tests` | `admin.placement-tests.index` | `Admin\PlacementTestController@index` | `web`, `auth`, `role:admin` | List placement test |
| GET | `/admin/placement-tests/{registration}` | `admin.placement-tests.show` | `Admin\PlacementTestController@show` | `web`, `auth`, `role:admin` | Detail placement test |
| POST | `/admin/placement-tests/{registration}/schedule` | `admin.placement-tests.schedule` | `Admin\PlacementTestScheduleController@store` | `web`, `auth`, `role:admin` | Jadwalkan placement test |
| POST | `/admin/placement-tests/{registration}/result` | `admin.placement-tests.result.store` | `Admin\PlacementTestResultController@store` | `web`, `auth`, `role:admin` | Simpan hasil placement test |
| GET | `/admin/students` | `admin.students.index` | `Admin\StudentController@index` | `web`, `auth`, `role:admin` | List siswa |
| GET | `/admin/students/{student}` | `admin.students.show` | `Admin\StudentController@show` | `web`, `auth`, `role:admin` | Detail siswa |
| GET | `/admin/instructors` | `admin.instructors.index` | `Admin\InstructorController@index` | `web`, `auth`, `role:admin` | List instructor |
| GET | `/admin/instructors/{instructor}` | `admin.instructors.show` | `Admin\InstructorController@show` | `web`, `auth`, `role:admin` | Detail instructor |
| GET | `/admin/programs` | `admin.programs.index` | `Admin\ProgramController@index` | `web`, `auth`, `role:admin` | List program |
| GET | `/admin/programs/create` | `admin.programs.create` | `Admin\ProgramController@create` | `web`, `auth`, `role:admin` | Form create program |
| POST | `/admin/programs` | `admin.programs.store` | `Admin\ProgramController@store` | `web`, `auth`, `role:admin` | Simpan program |
| GET | `/admin/programs/{program}/edit` | `admin.programs.edit` | `Admin\ProgramController@edit` | `web`, `auth`, `role:admin` | Form edit program |
| PUT | `/admin/programs/{program}` | `admin.programs.update` | `Admin\ProgramController@update` | `web`, `auth`, `role:admin` | Update program |
| GET | `/admin/classes` | `admin.classes.index` | `Admin\ClassController@index` | `web`, `auth`, `role:admin` | List kelas |
| GET | `/admin/classes/create` | `admin.classes.create` | `Admin\ClassController@create` | `web`, `auth`, `role:admin` | Form create kelas |
| POST | `/admin/classes` | `admin.classes.store` | `Admin\ClassController@store` | `web`, `auth`, `role:admin` | Simpan kelas |
| GET | `/admin/classes/{class}/edit` | `admin.classes.edit` | `Admin\ClassController@edit` | `web`, `auth`, `role:admin` | Form edit kelas |
| PUT | `/admin/classes/{class}` | `admin.classes.update` | `Admin\ClassController@update` | `web`, `auth`, `role:admin` | Update kelas |
| GET | `/admin/enrollments` | `admin.enrollments.index` | `Admin\EnrollmentController@index` | `web`, `auth`, `role:admin` | List enrollment |
| POST | `/admin/enrollments` | `admin.enrollments.store` | `Admin\EnrollmentController@store` | `web`, `auth`, `role:admin` | Simpan enrollment |
| GET | `/admin/report-cards` | `admin.report-cards.index` | `Admin\ReportCardController@index` | `web`, `auth`, `role:admin` | List rapor |
| GET | `/admin/report-cards/create` | `admin.report-cards.create` | `Admin\ReportCardController@create` | `web`, `auth`, `role:admin` | Form create rapor |
| POST | `/admin/report-cards` | `admin.report-cards.store` | `Admin\ReportCardController@store` | `web`, `auth`, `role:admin` | Simpan rapor |
| GET | `/admin/report-cards/{reportCard}` | `admin.report-cards.show` | `Admin\ReportCardController@show` | `web`, `auth`, `role:admin` | Detail/preview rapor |
| GET | `/admin/report-cards/{reportCard}/edit` | `admin.report-cards.edit` | `Admin\ReportCardController@edit` | `web`, `auth`, `role:admin` | Form edit rapor |
| PUT | `/admin/report-cards/{reportCard}` | `admin.report-cards.update` | `Admin\ReportCardController@update` | `web`, `auth`, `role:admin` | Update rapor |
| POST | `/admin/report-cards/{reportCard}/publish` | `admin.report-cards.publish` | `Admin\ReportCardPublishController@store` | `web`, `auth`, `role:admin` | Publish rapor |
| GET | `/admin/exports/students` | `admin.exports.students` | `Admin\StudentExportController@index` | `web`, `auth`, `role:admin` | Form export siswa |
| POST | `/admin/exports/students` | `admin.exports.students.download` | `Admin\StudentExportController@download` | `web`, `auth`, `role:admin` | Download export siswa |
| GET | `/admin/exports/report-cards` | `admin.exports.report-cards` | `Admin\ReportCardExportController@index` | `web`, `auth`, `role:admin` | Form export rapor |
| POST | `/admin/exports/report-cards` | `admin.exports.report-cards.download` | `Admin\ReportCardExportController@download` | `web`, `auth`, `role:admin` | Download export rapor |
| GET | `/admin/reels` | `admin.reels.index` | `Admin\ReelController@index` | `web`, `auth`, `role:admin` | List reels admin |
| GET | `/admin/reels/create` | `admin.reels.create` | `Admin\ReelController@create` | `web`, `auth`, `role:admin` | Form create reels |
| POST | `/admin/reels` | `admin.reels.store` | `Admin\ReelController@store` | `web`, `auth`, `role:admin` | Simpan reels |
| GET | `/admin/reels/{reel}/edit` | `admin.reels.edit` | `Admin\ReelController@edit` | `web`, `auth`, `role:admin` | Form edit reels |
| PUT | `/admin/reels/{reel}` | `admin.reels.update` | `Admin\ReelController@update` | `web`, `auth`, `role:admin` | Update reels |
| GET | `/admin/contents` | `admin.contents.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | List CMS content |
| GET | `/admin/contents/create` | `admin.contents.create` | `Admin\ContentController@create` | `web`, `auth`, `role:admin` | Form create content |
| POST | `/admin/contents` | `admin.contents.store` | `Admin\ContentController@store` | `web`, `auth`, `role:admin` | Simpan content |
| GET | `/admin/contents/{content}/edit` | `admin.contents.edit` | `Admin\ContentController@edit` | `web`, `auth`, `role:admin` | Form edit content |
| PUT | `/admin/contents/{content}` | `admin.contents.update` | `Admin\ContentController@update` | `web`, `auth`, `role:admin` | Update content |
| GET | `/admin/contact-messages` | `admin.contact-messages.index` | `Admin\ContactMessageController@index` | `web`, `auth`, `role:admin` | List pesan kontak |
| GET | `/admin/contact-messages/{contactMessage}` | `admin.contact-messages.show` | `Admin\ContactMessageController@show` | `web`, `auth`, `role:admin` | Detail pesan kontak |
| GET | `/admin/chatbot-logs` | `admin.chatbot-logs.index` | `Admin\ChatbotLogController@index` | `web`, `auth`, `role:admin` | List chatbot logs |
| GET | `/admin/settings` | `admin.settings.index` | `Admin\SettingController@index` | `web`, `auth`, `role:admin` | Setting admin |
| PUT | `/admin/settings` | `admin.settings.update` | `Admin\SettingController@update` | `web`, `auth`, `role:admin` | Update setting admin |

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

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
| GET | `/reels/{reel}` | `public.reels.show` | `Public\ReelController@show` | `web` | Redirect ke `/reels?reel={id}` agar feed membuka reel terpilih |
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

### Canonical Blade Admin Routes

Route admin Sprint 4 Mia canonical memakai Blade di `resources/views/pages/admin/...` dengan URI singular. Filament tetap terpasang untuk komponen internal, tetapi bukan surface route admin canonical.

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | `web`, `auth`, `role:admin` | Dashboard admin Blade |
| GET | `/admin/registration` | `admin.registration.index` | `Admin\RegistrationController@index` | `web`, `auth`, `role:admin` | List pendaftaran |
| GET | `/admin/registration/create` | `admin.registration.create` | `Admin\RegistrationController@create` | `web`, `auth`, `role:admin` | Create pendaftaran manual admin |
| POST | `/admin/registration` | `admin.registration.store` | `Admin\RegistrationController@store` | `web`, `auth`, `role:admin` | Store pendaftaran manual admin |
| GET | `/admin/registration/{registration}` | `admin.registration.show` | `Admin\RegistrationController@show` | `web`, `auth`, `role:admin` | Detail pendaftaran |
| GET | `/admin/registration/{registration}/edit` | `admin.registration.edit` | `Admin\RegistrationController@edit` | `web`, `auth`, `role:admin` | Edit pendaftaran |
| PUT | `/admin/registration/{registration}` | `admin.registration.update` | `Admin\RegistrationController@update` | `web`, `auth`, `role:admin` | Update pendaftaran |
| DELETE | `/admin/registration/{registration}` | `admin.registration.destroy` | `Admin\RegistrationController@destroy` | `web`, `auth`, `role:admin` | Soft delete pendaftaran |
| GET | `/admin/payment` | `admin.payment.index` | `Admin\PaymentController@index` | `web`, `auth`, `role:admin` | Monitoring pembayaran RD |
| GET | `/admin/payment/{payment}` | `admin.payment.show` | `Admin\PaymentController@show` | `web`, `auth`, `role:admin` | Detail pembayaran RD |
| POST | `/admin/payment/{payment}/verify` | `admin.payment.verify` | `Admin\PaymentVerificationController@verify` | `web`, `auth`, `role:admin` | Verifikasi pembayaran |
| POST | `/admin/payment/{payment}/reject` | `admin.payment.reject` | `Admin\PaymentVerificationController@reject` | `web`, `auth`, `role:admin` | Tolak pembayaran |
| GET | `/admin/placement-test` | `admin.placement-test.index` | `Admin\PlacementTestController@index` | `web`, `auth`, `role:admin` | Placement test admin |
| GET | `/admin/placement-test/{registration}` | `admin.placement-test.show` | `Admin\PlacementTestController@show` | `web`, `auth`, `role:admin` | Detail placement test |
| POST | `/admin/placement-test/{registration}/schedule` | `admin.placement-test.schedule` | `Admin\PlacementTestScheduleController@store` | `web`, `auth`, `role:admin` | Jadwalkan placement test |
| POST | `/admin/placement-test/{registration}/result` | `admin.placement-test.result.store` | `Admin\PlacementTestResultController@store` | `web`, `auth`, `role:admin` | Simpan hasil placement test |
| DELETE | `/admin/placement-test/{registration}/clear` | `admin.placement-test.clear` | `Admin\PlacementTestClearController` | `web`, `auth`, `role:admin` | Clear jadwal/hasil placement test |
| GET | `/admin/student` | `admin.student.index` | `Admin\StudentController@index` | `web`, `auth`, `role:admin` | List siswa |
| GET | `/admin/student/create` | `admin.student.create` | `Admin\StudentController@create` | `web`, `auth`, `role:admin` | Create siswa |
| POST | `/admin/student` | `admin.student.store` | `Admin\StudentController@store` | `web`, `auth`, `role:admin` | Store siswa |
| GET | `/admin/student/{student}` | `admin.student.show` | `Admin\StudentController@show` | `web`, `auth`, `role:admin` | Detail siswa |
| GET | `/admin/student/{student}/edit` | `admin.student.edit` | `Admin\StudentController@edit` | `web`, `auth`, `role:admin` | Edit siswa |
| PUT | `/admin/student/{student}` | `admin.student.update` | `Admin\StudentController@update` | `web`, `auth`, `role:admin` | Update siswa |
| DELETE | `/admin/student/{student}` | `admin.student.destroy` | `Admin\StudentController@destroy` | `web`, `auth`, `role:admin` | Soft delete siswa |
| GET | `/admin/instructor` | `admin.instructor.index` | `Admin\InstructorController@index` | `web`, `auth`, `role:admin` | List instructor |
| GET | `/admin/instructor/create` | `admin.instructor.create` | `Admin\InstructorController@create` | `web`, `auth`, `role:admin` | Create instructor |
| POST | `/admin/instructor` | `admin.instructor.store` | `Admin\InstructorController@store` | `web`, `auth`, `role:admin` | Store instructor |
| GET | `/admin/instructor/{instructor}` | `admin.instructor.show` | `Admin\InstructorController@show` | `web`, `auth`, `role:admin` | Detail instructor |
| GET | `/admin/instructor/{instructor}/edit` | `admin.instructor.edit` | `Admin\InstructorController@edit` | `web`, `auth`, `role:admin` | Edit instructor |
| PUT | `/admin/instructor/{instructor}` | `admin.instructor.update` | `Admin\InstructorController@update` | `web`, `auth`, `role:admin` | Update instructor |
| DELETE | `/admin/instructor/{instructor}` | `admin.instructor.destroy` | `Admin\InstructorController@destroy` | `web`, `auth`, `role:admin` | Soft delete instructor |
| GET | `/admin/program` | `admin.program.index` | `Admin\ProgramController@index` | `web`, `auth`, `role:admin` | List program |
| GET | `/admin/program/create` | `admin.program.create` | `Admin\ProgramController@create` | `web`, `auth`, `role:admin` | Create program |
| POST | `/admin/program` | `admin.program.store` | `Admin\ProgramController@store` | `web`, `auth`, `role:admin` | Store program |
| GET | `/admin/program/{program}` | `admin.program.show` | `Admin\ProgramController@show` | `web`, `auth`, `role:admin` | Detail program dengan kelas, pendaftaran, dan promo terkait |
| GET | `/admin/program/{program}/edit` | `admin.program.edit` | `Admin\ProgramController@edit` | `web`, `auth`, `role:admin` | Edit program |
| PUT | `/admin/program/{program}` | `admin.program.update` | `Admin\ProgramController@update` | `web`, `auth`, `role:admin` | Update program |
| DELETE | `/admin/program/{program}` | `admin.program.destroy` | `Admin\ProgramController@destroy` | `web`, `auth`, `role:admin` | Soft delete program |
| GET | `/admin/class` | `admin.class.index` | `Admin\ClassController@index` | `web`, `auth`, `role:admin` | List kelas |
| GET | `/admin/class/create` | `admin.class.create` | `Admin\ClassController@create` | `web`, `auth`, `role:admin` | Create kelas |
| POST | `/admin/class` | `admin.class.store` | `Admin\ClassController@store` | `web`, `auth`, `role:admin` | Store kelas |
| GET | `/admin/class/{class}` | `admin.class.show` | `Admin\ClassController@show` | `web`, `auth`, `role:admin` | Detail kelas dengan enrollment dan link rapor |
| GET | `/admin/class/{class}/edit` | `admin.class.edit` | `Admin\ClassController@edit` | `web`, `auth`, `role:admin` | Edit kelas |
| PUT | `/admin/class/{class}` | `admin.class.update` | `Admin\ClassController@update` | `web`, `auth`, `role:admin` | Update kelas |
| DELETE | `/admin/class/{class}` | `admin.class.destroy` | `Admin\ClassController@destroy` | `web`, `auth`, `role:admin` | Soft delete kelas |
| GET | `/admin/room` | `admin.room.index` | `Admin\RoomController@index` | `web`, `auth`, `role:admin` | List room |
| GET | `/admin/room/create` | `admin.room.create` | `Admin\RoomController@create` | `web`, `auth`, `role:admin` | Create room |
| POST | `/admin/room` | `admin.room.store` | `Admin\RoomController@store` | `web`, `auth`, `role:admin` | Store room |
| GET | `/admin/room/{room}` | `admin.room.show` | `Admin\RoomController@show` | `web`, `auth`, `role:admin` | Detail room |
| GET | `/admin/room/{room}/edit` | `admin.room.edit` | `Admin\RoomController@edit` | `web`, `auth`, `role:admin` | Edit room |
| PUT/PATCH | `/admin/room/{room}` | `admin.room.update` | `Admin\RoomController@update` | `web`, `auth`, `role:admin` | Update room |
| DELETE | `/admin/room/{room}` | `admin.room.destroy` | `Admin\RoomController@destroy` | `web`, `auth`, `role:admin` | Soft delete room |
| GET | `/admin/enrollment` | `admin.enrollment.index` | `Admin\EnrollmentController@index` | `web`, `auth`, `role:admin` | List enrollment; create via form/modal context |
| POST | `/admin/enrollment` | `admin.enrollment.store` | `Admin\EnrollmentController@store` | `web`, `auth`, `role:admin` | Store enrollment |
| GET | `/admin/enrollment/{enrollment}` | `admin.enrollment.show` | `Admin\EnrollmentController@show` | `web`, `auth`, `role:admin` | Detail enrollment dengan student, class, dan rapor |
| GET | `/admin/enrollment/{enrollment}/edit` | `admin.enrollment.edit` | `Admin\EnrollmentController@edit` | `web`, `auth`, `role:admin` | Edit enrollment |
| PUT | `/admin/enrollment/{enrollment}` | `admin.enrollment.update` | `Admin\EnrollmentController@update` | `web`, `auth`, `role:admin` | Update enrollment |
| DELETE | `/admin/enrollment/{enrollment}` | `admin.enrollment.destroy` | `Admin\EnrollmentController@destroy` | `web`, `auth`, `role:admin` | Soft delete enrollment |
| GET | `/admin/report-card` | `admin.report-card.index` | `Admin\ReportCardController@index` | `web`, `auth`, `role:admin` | List rapor |
| GET | `/admin/report-card/create` | `admin.report-card.create` | `Admin\ReportCardController@create` | `web`, `auth`, `role:admin` | Create rapor |
| POST | `/admin/report-card` | `admin.report-card.store` | `Admin\ReportCardController@store` | `web`, `auth`, `role:admin` | Store rapor |
| GET | `/admin/report-card/{reportCard}` | `admin.report-card.show` | `Admin\ReportCardController@show` | `web`, `auth`, `role:admin` | Detail rapor |
| GET | `/admin/report-card/{reportCard}/edit` | `admin.report-card.edit` | `Admin\ReportCardController@edit` | `web`, `auth`, `role:admin` | Edit rapor |
| PUT | `/admin/report-card/{reportCard}` | `admin.report-card.update` | `Admin\ReportCardController@update` | `web`, `auth`, `role:admin` | Update rapor |
| DELETE | `/admin/report-card/{reportCard}` | `admin.report-card.destroy` | `Admin\ReportCardController@destroy` | `web`, `auth`, `role:admin` | Soft delete rapor |
| POST | `/admin/report-card/{reportCard}/publish` | `admin.report-card.publish` | `Admin\ReportCardPublishController@store` | `web`, `auth`, `role:admin` | Publish rapor |
| GET | `/admin/reel` | `admin.reel.index` | `Admin\ReelController@index` | `web`, `auth`, `role:admin` | List reels |
| GET | `/admin/reel/create` | `admin.reel.create` | `Admin\ReelController@create` | `web`, `auth`, `role:admin` | Create reel |
| POST | `/admin/reel` | `admin.reel.store` | `Admin\ReelController@store` | `web`, `auth`, `role:admin` | Store reel |
| GET | `/admin/reel/{reel}` | `admin.reel.show` | `Admin\ReelController@show` | `web`, `auth`, `role:admin` | Detail reel |
| GET | `/admin/reel/{reel}/edit` | `admin.reel.edit` | `Admin\ReelController@edit` | `web`, `auth`, `role:admin` | Edit reel |
| PUT | `/admin/reel/{reel}` | `admin.reel.update` | `Admin\ReelController@update` | `web`, `auth`, `role:admin` | Update reel |
| DELETE | `/admin/reel/{reel}` | `admin.reel.destroy` | `Admin\ReelController@destroy` | `web`, `auth`, `role:admin` | Soft delete reel |
| GET | `/admin/gallery` | `admin.gallery.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | Gallery CMS |
| GET | `/admin/gallery/create` | `admin.gallery.create` | `Admin\ContentController@create` | `web`, `auth`, `role:admin` | Create gallery CMS |
| POST | `/admin/gallery` | `admin.gallery.store` | `Admin\ContentController@store` | `web`, `auth`, `role:admin` | Store gallery CMS |
| GET | `/admin/gallery/{content}` | `admin.gallery.show` | `Admin\ContentController@show` | `web`, `auth`, `role:admin` | Detail gallery CMS |
| GET | `/admin/gallery/{content}/edit` | `admin.gallery.edit` | `Admin\ContentController@edit` | `web`, `auth`, `role:admin` | Edit gallery CMS |
| PUT | `/admin/gallery/{content}` | `admin.gallery.update` | `Admin\ContentController@update` | `web`, `auth`, `role:admin` | Update gallery CMS |
| DELETE | `/admin/gallery/{content}` | `admin.gallery.destroy` | `Admin\ContentController@destroy` | `web`, `auth`, `role:admin` | Soft delete gallery CMS |
| GET | `/admin/partner` | `admin.partner.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | Partner CMS |
| GET | `/admin/partner/create` | `admin.partner.create` | `Admin\ContentController@create` | `web`, `auth`, `role:admin` | Create partner CMS |
| POST | `/admin/partner` | `admin.partner.store` | `Admin\ContentController@store` | `web`, `auth`, `role:admin` | Store partner CMS |
| GET | `/admin/partner/{content}` | `admin.partner.show` | `Admin\ContentController@show` | `web`, `auth`, `role:admin` | Detail partner CMS |
| GET | `/admin/partner/{content}/edit` | `admin.partner.edit` | `Admin\ContentController@edit` | `web`, `auth`, `role:admin` | Edit partner CMS |
| PUT | `/admin/partner/{content}` | `admin.partner.update` | `Admin\ContentController@update` | `web`, `auth`, `role:admin` | Update partner CMS |
| DELETE | `/admin/partner/{content}` | `admin.partner.destroy` | `Admin\ContentController@destroy` | `web`, `auth`, `role:admin` | Soft delete partner CMS |
| GET | `/admin/testimonial` | `admin.testimonial.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | Testimonial CMS |
| GET | `/admin/testimonial/create` | `admin.testimonial.create` | `Admin\ContentController@create` | `web`, `auth`, `role:admin` | Create testimonial CMS |
| POST | `/admin/testimonial` | `admin.testimonial.store` | `Admin\ContentController@store` | `web`, `auth`, `role:admin` | Store testimonial CMS |
| GET | `/admin/testimonial/{content}` | `admin.testimonial.show` | `Admin\ContentController@show` | `web`, `auth`, `role:admin` | Detail testimonial CMS |
| GET | `/admin/testimonial/{content}/edit` | `admin.testimonial.edit` | `Admin\ContentController@edit` | `web`, `auth`, `role:admin` | Edit testimonial CMS |
| PUT | `/admin/testimonial/{content}` | `admin.testimonial.update` | `Admin\ContentController@update` | `web`, `auth`, `role:admin` | Update testimonial CMS |
| DELETE | `/admin/testimonial/{content}` | `admin.testimonial.destroy` | `Admin\ContentController@destroy` | `web`, `auth`, `role:admin` | Soft delete testimonial CMS |
| GET | `/admin/faq` | `admin.faq.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | FAQ CMS |
| GET | `/admin/faq/create` | `admin.faq.create` | `Admin\ContentController@create` | `web`, `auth`, `role:admin` | Create FAQ CMS |
| POST | `/admin/faq` | `admin.faq.store` | `Admin\ContentController@store` | `web`, `auth`, `role:admin` | Store FAQ CMS |
| GET | `/admin/faq/{content}` | `admin.faq.show` | `Admin\ContentController@show` | `web`, `auth`, `role:admin` | Detail FAQ CMS |
| GET | `/admin/faq/{content}/edit` | `admin.faq.edit` | `Admin\ContentController@edit` | `web`, `auth`, `role:admin` | Edit FAQ CMS |
| PUT | `/admin/faq/{content}` | `admin.faq.update` | `Admin\ContentController@update` | `web`, `auth`, `role:admin` | Update FAQ CMS |
| DELETE | `/admin/faq/{content}` | `admin.faq.destroy` | `Admin\ContentController@destroy` | `web`, `auth`, `role:admin` | Soft delete FAQ CMS |
| GET | `/admin/profile` | `admin.profile.index` | `Admin\SettingController@index` | `web`, `auth`, `role:admin` | Profile/settings CMS |
| PUT | `/admin/profile` | `admin.profile.update` | `Admin\SettingController@update` | `web`, `auth`, `role:admin` | Update profile/settings |
| GET | `/admin/contact-message` | `admin.contact-message.index` | `Admin\ContactMessageController@index` | `web`, `auth`, `role:admin` | Pesan kontak RD |
| GET | `/admin/contact-message/{contactMessage}` | `admin.contact-message.show` | `Admin\ContactMessageController@show` | `web`, `auth`, `role:admin` | Detail pesan kontak RD |
| GET | `/admin/chatbot-log` | `admin.chatbot-log.index` | `Admin\ChatbotLogController@index` | `web`, `auth`, `role:admin` | Chatbot logs RD |
| GET | `/admin/chatbot-log/{chatbotLog}` | `admin.chatbot-log.show` | `Admin\ChatbotLogController@show` | `web`, `auth`, `role:admin` | Detail chatbot log RD |
| GET | `/admin/exports/students` | `admin.exports.students` | `Admin\StudentExportController@index` | `web`, `auth`, `role:admin` | Form export siswa |
| POST | `/admin/exports/students` | `admin.exports.students.download` | `Admin\StudentExportController@download` | `web`, `auth`, `role:admin` | Download export siswa |
| GET | `/admin/exports/report-cards` | `admin.exports.report-cards` | `Admin\ReportCardExportController@index` | `web`, `auth`, `role:admin` | Form export rapor |
| POST | `/admin/exports/report-cards` | `admin.exports.report-cards.download` | `Admin\ReportCardExportController@download` | `web`, `auth`, `role:admin` | Download export rapor |

### Plural Admin Redirect Compatibility Routes

URL admin plural lama tetap ada sebagai redirect ke singular canonical. Route name memakai prefix `admin.legacy.*` agar tidak menjadi canonical internal link.

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET/HEAD | `/admin/registrations` | `admin.legacy.registrations.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/registration` |
| GET/HEAD | `/admin/payments` | `admin.legacy.payments.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/payment` |
| GET/HEAD | `/admin/placement-tests` | `admin.legacy.placement-tests.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/placement-test` |
| GET/HEAD | `/admin/students` | `admin.legacy.students.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/student` |
| GET/HEAD | `/admin/instructors` | `admin.legacy.instructors.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/instructor` |
| GET/HEAD | `/admin/programs` | `admin.legacy.programs.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/program` |
| GET/HEAD | `/admin/programs/{program}` | `admin.legacy.programs.show` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/program/{program}` |
| GET/HEAD | `/admin/classes` | `admin.legacy.classes.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/class` |
| GET/HEAD | `/admin/classes/{class}` | `admin.legacy.classes.show` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/class/{class}` |
| GET/HEAD | `/admin/enrollments` | `admin.legacy.enrollments.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/enrollment` |
| GET/HEAD | `/admin/enrollments/{enrollment}` | `admin.legacy.enrollments.show` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/enrollment/{enrollment}` |
| GET/HEAD | `/admin/report-cards` | `admin.legacy.report-cards.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/report-card` |
| GET/HEAD | `/admin/reels` | `admin.legacy.reels.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/reel` |
| GET/HEAD | `/admin/reels/{reel}` | `admin.legacy.reels.show` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/reel/{reel}` |
| GET/HEAD | `/admin/contents` | `admin.legacy.contents.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/gallery` |
| GET/HEAD | `/admin/settings` | `admin.legacy.settings.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/profile` |
| GET/HEAD | `/admin/contact-messages` | `admin.legacy.contact-messages.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/contact-message` |
| GET/HEAD | `/admin/chatbot-logs` | `admin.legacy.chatbot-logs.index` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/chatbot-log` |
| GET/HEAD | `/admin/chatbot-logs/{chatbotLog}` | `admin.legacy.chatbot-logs.show` | `Illuminate\Routing\RedirectController` | `web`, `auth`, `role:admin` | Redirect ke `/admin/chatbot-log/{chatbotLog}` |

## Student Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/student/dashboard` | `student.dashboard` | `Student\DashboardController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` |
| GET | `/student/profile` | `student.profile.show` | `Student\ProfileController@show` | `web`, `auth`, `role:student` | Profil siswa |
| PUT | `/student/profile` | `student.profile.update` | `Student\ProfileController@update` | `web`, `auth`, `role:student` | Update profil siswa |
| GET | `/student/class` | `student.classes.index` | `Student\ClassController@index` | `web`, `auth`, `role:student` | List kelas siswa; `/student/classes` legacy alias tanpa route name |
| GET | `/student/class/{class}` | `student.classes.show` | `Student\ClassController@show` | `web`, `auth`, `role:student` | Detail kelas siswa; `/student/classes/{class}` legacy alias tanpa route name |
| GET | `/student/learning-history` | `student.learning-history.index` | `Student\LearningHistoryController@index` | `web`, `auth`, `role:student` | Riwayat belajar |
| GET | `/student/report-card` | `student.report-cards.index` | `Student\ReportCardController@index` | `web`, `auth`, `role:student` | List rapor siswa; `/student/report-cards` legacy alias tanpa route name |
| GET | `/student/report-card/{reportCard}` | `student.report-cards.show` | `Student\ReportCardController@show` | `web`, `auth`, `role:student` | Detail rapor siswa; `/student/report-cards/{reportCard}` legacy alias tanpa route name |
| GET | `/student/report-card/{reportCard}/download` | `student.report-cards.download` | `Student\ReportCardDownloadController` | `web`, `auth`, `role:student` | Download rapor siswa; `/student/report-cards/{reportCard}/download` legacy alias tanpa route name |
| GET | `/student/payment` | `student.payments.index` | `Student\PaymentController@index` | `web`, `auth`, `role:student` | List pembayaran siswa; `/student/payments` legacy alias tanpa route name |
| GET | `/student/payment/{payment}` | `student.payments.show` | `Student\PaymentController@show` | `web`, `auth`, `role:student` | Detail pembayaran siswa; `/student/payments/{payment}` legacy alias tanpa route name |
| GET | `/student/help` | `student.help.index` | `Student\HelpController@index` | `web`, `auth`, `role:student` | Bantuan siswa |

## Instructor Routes

| Method | URI | Route Name | Controller/Action | Middleware | Layout/Notes |
| --- | --- | --- | --- | --- | --- |
| GET | `/instructor/dashboard` | `instructor.dashboard` | `Instructor\DashboardController@index` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` |
| GET | `/instructor/profile` | `instructor.profile.show` | `Instructor\ProfileController@show` | `web`, `auth`, `role:instructor` | Profil instructor |
| PUT | `/instructor/profile` | `instructor.profile.update` | `Instructor\ProfileController@update` | `web`, `auth`, `role:instructor` | Update profil instructor |
| GET | `/instructor/class` | `instructor.classes.index` | `Instructor\ClassController@index` | `web`, `auth`, `role:instructor` | List kelas instructor |
| GET | `/instructor/class/{class}` | `instructor.classes.show` | `Instructor\ClassController@show` | `web`, `auth`, `role:instructor` | Detail kelas instructor |
| GET | `/instructor/student` | `instructor.students.index` | `Instructor\StudentController@index` | `web`, `auth`, `role:instructor` | List siswa instructor |
| GET | `/instructor/report-card` | `instructor.report-cards.index` | `Instructor\ReportCardController@index` | `web`, `auth`, `role:instructor` | List rapor instructor |
| GET | `/instructor/enrollment/{enrollment}/report-card/create` | `instructor.report-cards.create` | `Instructor\ReportCardController@create` | `web`, `auth`, `role:instructor` | Form create rapor dari enrollment |
| POST | `/instructor/enrollment/{enrollment}/report-card` | `instructor.report-cards.store` | `Instructor\ReportCardController@store` | `web`, `auth`, `role:instructor` | Simpan rapor instructor |
| GET | `/instructor/report-card/{reportCard}` | `instructor.report-cards.show` | `Instructor\ReportCardController@show` | `web`, `auth`, `role:instructor` | Detail rapor instructor |
| GET | `/instructor/report-card/{reportCard}/edit` | `instructor.report-cards.edit` | `Instructor\ReportCardController@edit` | `web`, `auth`, `role:instructor` | Form edit rapor instructor |
| PUT | `/instructor/report-card/{reportCard}` | `instructor.report-cards.update` | `Instructor\ReportCardController@update` | `web`, `auth`, `role:instructor` | Update rapor instructor |

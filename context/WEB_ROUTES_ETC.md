# WEB ROUTES ETC PLANET

Dokumen ini adalah blueprint route halaman web ETC Planet. Route aktual di `routes/web.php` boleh dibuat bertahap, tetapi nama route dan URI pada dokumen ini menjadi acuan bersama tim.

## Aturan Umum

- Semua **URI/path** dan **route name** memakai Bahasa Inggris.
- Teks UI tetap boleh Bahasa Indonesia sesuai kebutuhan halaman.
- Halaman public wajib memakai layout `<x-layouts.public>`.
- Halaman dashboard wajib memakai layout `<x-layouts.dashboard area="admin|student|instructor">`.
- Gunakan route model binding untuk resource utama, misalnya `{program}`, `{registration}`, `{reel}`, `{reportCard}`, atau `{class}`.
- Route dashboard wajib memakai middleware `auth` dan role sesuai area.
- Student hanya boleh melihat data miliknya sendiri.
- Route action seperti submit form, upload, verify, publish, view count, atau like tetap didokumentasikan karena memengaruhi flow halaman.

## Penanggung Jawab

| Owner | Fokus Saat Ini | Tanggung Jawab Route |
| --- | --- | --- |
| Rasky | `english_conversation_detail_etc_planet`, `dashboard_admin_etc_planet` | Program detail, auth, admin dashboard/workflow, report cards, exports, instructor dashboard |
| Miftah | `beranda_lengkap_etc_planet` | Public website, contact, chatbot public, reels public, admin CMS/content |
| Mecca | `dashboard_siswa_etc_planet`, `pilih_program_etc_planet` | Program discovery, student dashboard, student profile/class/report pages, admin academic master data |
| Mia | `pembayaran_etc_planet`, `pendaftaran_online_lengkap_etc_planet`, `konfirmasi_pendaftaran_etc_planet` | Registration flow, payment flow, admin registration/payment verification |

## Route Table

| Owner | Method | URI | Route Name | Controller Action | Middleware | Layout | Reference/Notes |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Miftah | GET | `/` | `public.home` | `Public\HomeController@index` | `web` | `<x-layouts.public>` | Beranda lengkap, ref `beranda_lengkap_etc_planet` |
| Miftah | GET | `/about` | `public.about` | `Public\AboutController@index` | `web` | `<x-layouts.public>` | Profil lembaga, visi/misi, pendekatan belajar |
| Miftah | GET | `/team` | `public.team.index` | `Public\TeamController@index` | `web` | `<x-layouts.public>` | Instructor dengan `show_on_team_page = 1` |
| Miftah | GET | `/facilities` | `public.facilities.index` | `Public\FacilityController@index` | `web` | `<x-layouts.public>` | CMS `contents` type `room` |
| Miftah | GET | `/gallery` | `public.gallery.index` | `Public\GalleryController@index` | `web` | `<x-layouts.public>` | CMS `contents` type `gallery` |
| Miftah | GET | `/contact` | `public.contact.index` | `Public\ContactController@index` | `web` | `<x-layouts.public>` | Form kontak dan info lokasi |
| Miftah | POST | `/contact` | `public.contact.store` | `Public\ContactController@store` | `web`, `throttle:contact` | Redirect public | Simpan ke `contact_messages`, wajib `FormRequest` |
| Miftah | GET | `/faq` | `public.faq.index` | `Public\FaqController@index` | `web` | `<x-layouts.public>` | Basis FAQ chatbot |
| Miftah | POST | `/chatbot/messages` | `public.chatbot.messages.store` | `Public\ChatbotController@store` | `web`, `throttle:chatbot` | JSON | Log ke `chatbot_logs` |
| Miftah | GET | `/reels` | `public.reels.index` | `Public\ReelController@index` | `web` | `<x-layouts.public>` | Feed reels public |
| Miftah | GET | `/reels/{reel}` | `public.reels.show` | `Public\ReelController@show` | `web` | `<x-layouts.public>` | Detail/player reels |
| Miftah | POST | `/reels/{reel}/views` | `public.reels.views.store` | `Public\ReelViewController@store` | `web`, `throttle:reels` | JSON | Increment view secara terkendali |
| Miftah | POST | `/reels/{reel}/likes` | `public.reels.likes.store` | `Public\ReelLikeController@store` | `web`, `throttle:reels` | JSON | Like/unlike reels |
| Mecca | GET | `/programs` | `public.programs.index` | `Public\ProgramController@index` | `web` | `<x-layouts.public>` | Listing program dan filter kategori |
| Rasky | GET | `/programs/{program}` | `public.programs.show` | `Public\ProgramController@show` | `web` | `<x-layouts.public>` | Detail program, ref `english_conversation_detail_etc_planet` |
| Rasky | GET | `/login` | `auth.login` | `Auth\AuthenticatedSessionController@create` | `web`, `guest` | `<x-layouts.public>` | Akses admin/student/instructor |
| Rasky | POST | `/login` | `auth.login.store` | `Auth\AuthenticatedSessionController@store` | `web`, `guest`, `throttle:login` | Redirect role dashboard | Validasi credential |
| Rasky | POST | `/logout` | `auth.logout` | `Auth\AuthenticatedSessionController@destroy` | `web`, `auth` | Redirect public | Logout semua role |
| Rasky | GET | `/forgot-password` | `auth.password.request` | `Auth\PasswordResetLinkController@create` | `web`, `guest` | `<x-layouts.public>` | Form lupa password |
| Rasky | POST | `/forgot-password` | `auth.password.email` | `Auth\PasswordResetLinkController@store` | `web`, `guest`, `throttle:password` | Redirect public | Kirim reset link |
| Rasky | GET | `/reset-password/{token}` | `auth.password.reset` | `Auth\NewPasswordController@create` | `web`, `guest` | `<x-layouts.public>` | Form reset password |
| Rasky | POST | `/reset-password` | `auth.password.update` | `Auth\NewPasswordController@store` | `web`, `guest` | Redirect login | Update password |
| Mia | GET | `/registration` | `registrations.start` | `Public\RegistrationStartController@index` | `web` | `<x-layouts.public>` | Entry point daftar, redirect/CTA ke pilih program |
| Mecca | GET | `/registration/programs` | `registrations.programs.index` | `Public\RegistrationProgramController@index` | `web` | `<x-layouts.public>` | Pilih program, ref `pilih_program_etc_planet` |
| Mia | GET | `/registration/form/{program?}` | `registrations.create` | `Public\RegistrationController@create` | `web` | `<x-layouts.public>` | Form lengkap, ref `pendaftaran_online_lengkap_etc_planet` |
| Mia | POST | `/registration` | `registrations.store` | `Public\RegistrationController@store` | `web`, `throttle:registration` | Redirect payment | Simpan pendaftaran dan status `pending_payment` |
| Mia | GET | `/registration/payment/{registration}` | `registrations.payment.show` | `Public\RegistrationPaymentController@show` | `web`, `signed.optional` | `<x-layouts.public>` | Pembayaran, ref `pembayaran_etc_planet` |
| Mia | POST | `/registration/payment/{registration}/proof` | `registrations.payment.proof.store` | `Public\RegistrationPaymentProofController@store` | `web`, `throttle:upload` | Redirect payment | Upload bukti pembayaran |
| Mia | POST | `/registration/payment/{registration}/confirm` | `registrations.payment.confirm` | `Public\RegistrationPaymentController@confirm` | `web`, `throttle:payment` | Redirect confirmation | Konfirmasi sudah membayar |
| Mia | GET | `/registration/confirmation/{registration}` | `registrations.confirmation.show` | `Public\RegistrationConfirmationController@show` | `web`, `signed.optional` | `<x-layouts.public>` | Konfirmasi, ref `konfirmasi_pendaftaran_etc_planet` |
| Mia | GET | `/registration/{registration}/receipt` | `registrations.receipt.download` | `Public\RegistrationReceiptController@download` | `web`, `signed.optional` | Download | Bukti pendaftaran digital |
| Mecca | GET | `/student/dashboard` | `student.dashboard` | `Student\DashboardController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Dashboard siswa, ref `dashboard_siswa_etc_planet` |
| Mecca | GET | `/student/profile` | `student.profile.show` | `Student\ProfileController@show` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Biodata siswa |
| Mecca | PUT | `/student/profile` | `student.profile.update` | `Student\ProfileController@update` | `web`, `auth`, `role:student` | Redirect student profile | Update field profil yang diizinkan |
| Mecca | GET | `/student/classes` | `student.classes.index` | `Student\ClassController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Kelas aktif dan riwayat ringkas |
| Mecca | GET | `/student/classes/{class}` | `student.classes.show` | `Student\ClassController@show` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Hanya kelas milik siswa |
| Mecca | GET | `/student/learning-history` | `student.learning-history.index` | `Student\LearningHistoryController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Riwayat pembelajaran |
| Mecca | GET | `/student/report-cards` | `student.report-cards.index` | `Student\ReportCardController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Hanya rapor milik siswa dan sudah publish |
| Mecca | GET | `/student/report-cards/{reportCard}` | `student.report-cards.show` | `Student\ReportCardController@show` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Detail rapor published |
| Mecca | GET | `/student/report-cards/{reportCard}/download` | `student.report-cards.download` | `Student\ReportCardDownloadController@download` | `web`, `auth`, `role:student` | Download | Download rapor published |
| Mia | GET | `/student/payments` | `student.payments.index` | `Student\PaymentController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Riwayat pembayaran siswa |
| Mia | GET | `/student/payments/{payment}` | `student.payments.show` | `Student\PaymentController@show` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Detail pembayaran milik siswa |
| Mecca | GET | `/student/help` | `student.help.index` | `Student\HelpController@index` | `web`, `auth`, `role:student` | `<x-layouts.dashboard area="student">` | Bantuan/chatbot siswa |
| Rasky | GET | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Dashboard admin, ref `dashboard_admin_etc_planet` |
| Mia | GET | `/admin/registrations` | `admin.registrations.index` | `Admin\RegistrationController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | List pendaftaran |
| Mia | GET | `/admin/registrations/{registration}` | `admin.registrations.show` | `Admin\RegistrationController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Detail pendaftaran |
| Mia | GET | `/admin/registrations/{registration}/edit` | `admin.registrations.edit` | `Admin\RegistrationController@edit` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Edit data pendaftaran oleh admin |
| Mia | PUT | `/admin/registrations/{registration}` | `admin.registrations.update` | `Admin\RegistrationController@update` | `web`, `auth`, `role:admin` | Redirect admin registrations | Update via `FormRequest` |
| Mia | GET | `/admin/payments` | `admin.payments.index` | `Admin\PaymentController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Verifikasi pembayaran |
| Mia | GET | `/admin/payments/{payment}` | `admin.payments.show` | `Admin\PaymentController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Detail pembayaran dan bukti upload |
| Mia | POST | `/admin/payments/{payment}/verify` | `admin.payments.verify` | `Admin\PaymentVerificationController@verify` | `web`, `auth`, `role:admin` | Redirect admin payments | Ubah status menjadi `paid` |
| Mia | POST | `/admin/payments/{payment}/reject` | `admin.payments.reject` | `Admin\PaymentVerificationController@reject` | `web`, `auth`, `role:admin` | Redirect admin payments | Tolak pembayaran dengan catatan |
| Rasky | GET | `/admin/placement-tests` | `admin.placement-tests.index` | `Admin\PlacementTestController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Jadwal dan hasil placement test |
| Rasky | GET | `/admin/placement-tests/{registration}` | `admin.placement-tests.show` | `Admin\PlacementTestController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Detail placement untuk pendaftar |
| Rasky | POST | `/admin/placement-tests/{registration}/schedule` | `admin.placement-tests.schedule` | `Admin\PlacementTestScheduleController@store` | `web`, `auth`, `role:admin` | Redirect placement test | Jadwalkan test offline |
| Rasky | POST | `/admin/placement-tests/{registration}/result` | `admin.placement-tests.result.store` | `Admin\PlacementTestResultController@store` | `web`, `auth`, `role:admin` | Redirect placement test | Simpan hasil dan rekomendasi kelas |
| Mecca | GET | `/admin/students` | `admin.students.index` | `Admin\StudentController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Data siswa |
| Mecca | GET | `/admin/students/{student}` | `admin.students.show` | `Admin\StudentController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Detail siswa |
| Mecca | GET | `/admin/instructors` | `admin.instructors.index` | `Admin\InstructorController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Data instructor |
| Mecca | GET | `/admin/instructors/{instructor}` | `admin.instructors.show` | `Admin\InstructorController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Detail instructor |
| Mecca | GET | `/admin/programs` | `admin.programs.index` | `Admin\ProgramController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Master program |
| Mecca | GET | `/admin/programs/create` | `admin.programs.create` | `Admin\ProgramController@create` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form create program |
| Mecca | POST | `/admin/programs` | `admin.programs.store` | `Admin\ProgramController@store` | `web`, `auth`, `role:admin` | Redirect admin programs | Simpan program |
| Mecca | GET | `/admin/programs/{program}/edit` | `admin.programs.edit` | `Admin\ProgramController@edit` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form edit program |
| Mecca | PUT | `/admin/programs/{program}` | `admin.programs.update` | `Admin\ProgramController@update` | `web`, `auth`, `role:admin` | Redirect admin programs | Update program |
| Mecca | GET | `/admin/classes` | `admin.classes.index` | `Admin\ClassController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Master kelas |
| Mecca | GET | `/admin/classes/create` | `admin.classes.create` | `Admin\ClassController@create` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form create kelas |
| Mecca | POST | `/admin/classes` | `admin.classes.store` | `Admin\ClassController@store` | `web`, `auth`, `role:admin` | Redirect admin classes | Simpan kelas |
| Mecca | GET | `/admin/classes/{class}/edit` | `admin.classes.edit` | `Admin\ClassController@edit` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form edit kelas |
| Mecca | PUT | `/admin/classes/{class}` | `admin.classes.update` | `Admin\ClassController@update` | `web`, `auth`, `role:admin` | Redirect admin classes | Update kelas |
| Mecca | GET | `/admin/enrollments` | `admin.enrollments.index` | `Admin\EnrollmentController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Data enrollment |
| Mecca | POST | `/admin/enrollments` | `admin.enrollments.store` | `Admin\EnrollmentController@store` | `web`, `auth`, `role:admin` | Redirect admin enrollments | Assign siswa ke kelas |
| Rasky | GET | `/admin/report-cards` | `admin.report-cards.index` | `Admin\ReportCardController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | CRUD rapor |
| Rasky | GET | `/admin/report-cards/create` | `admin.report-cards.create` | `Admin\ReportCardController@create` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form create rapor |
| Rasky | POST | `/admin/report-cards` | `admin.report-cards.store` | `Admin\ReportCardController@store` | `web`, `auth`, `role:admin` | Redirect report cards | Simpan rapor |
| Rasky | GET | `/admin/report-cards/{reportCard}` | `admin.report-cards.show` | `Admin\ReportCardController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Preview rapor sebelum publish |
| Rasky | GET | `/admin/report-cards/{reportCard}/edit` | `admin.report-cards.edit` | `Admin\ReportCardController@edit` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form edit rapor |
| Rasky | PUT | `/admin/report-cards/{reportCard}` | `admin.report-cards.update` | `Admin\ReportCardController@update` | `web`, `auth`, `role:admin` | Redirect report cards | Update rapor |
| Rasky | POST | `/admin/report-cards/{reportCard}/publish` | `admin.report-cards.publish` | `Admin\ReportCardPublishController@store` | `web`, `auth`, `role:admin` | Redirect report cards | Publish agar siswa bisa download |
| Rasky | GET | `/admin/exports/students` | `admin.exports.students` | `Admin\StudentExportController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Export Excel rekap siswa |
| Rasky | POST | `/admin/exports/students` | `admin.exports.students.download` | `Admin\StudentExportController@download` | `web`, `auth`, `role:admin` | Download | Generate dari template XLSX |
| Rasky | GET | `/admin/exports/report-cards` | `admin.exports.report-cards` | `Admin\ReportCardExportController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Export/download dokumen rapor |
| Rasky | POST | `/admin/exports/report-cards` | `admin.exports.report-cards.download` | `Admin\ReportCardExportController@download` | `web`, `auth`, `role:admin` | Download | Generate dari template DOC |
| Miftah | GET | `/admin/reels` | `admin.reels.index` | `Admin\ReelController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Admin reels |
| Miftah | GET | `/admin/reels/create` | `admin.reels.create` | `Admin\ReelController@create` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form upload reels |
| Miftah | POST | `/admin/reels` | `admin.reels.store` | `Admin\ReelController@store` | `web`, `auth`, `role:admin` | Redirect admin reels | Upload video dan thumbnail via service |
| Miftah | GET | `/admin/reels/{reel}/edit` | `admin.reels.edit` | `Admin\ReelController@edit` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form edit reels |
| Miftah | PUT | `/admin/reels/{reel}` | `admin.reels.update` | `Admin\ReelController@update` | `web`, `auth`, `role:admin` | Redirect admin reels | Update metadata/publish status |
| Miftah | GET | `/admin/contents` | `admin.contents.index` | `Admin\ContentController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | CMS page/gallery/partner/room/setting |
| Miftah | GET | `/admin/contents/create` | `admin.contents.create` | `Admin\ContentController@create` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form create content |
| Miftah | POST | `/admin/contents` | `admin.contents.store` | `Admin\ContentController@store` | `web`, `auth`, `role:admin` | Redirect admin contents | Simpan content CMS |
| Miftah | GET | `/admin/contents/{content}/edit` | `admin.contents.edit` | `Admin\ContentController@edit` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Form edit content |
| Miftah | PUT | `/admin/contents/{content}` | `admin.contents.update` | `Admin\ContentController@update` | `web`, `auth`, `role:admin` | Redirect admin contents | Update content CMS |
| Miftah | GET | `/admin/contact-messages` | `admin.contact-messages.index` | `Admin\ContactMessageController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Inbox pesan kontak |
| Miftah | GET | `/admin/contact-messages/{contactMessage}` | `admin.contact-messages.show` | `Admin\ContactMessageController@show` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Detail pesan kontak |
| Miftah | GET | `/admin/chatbot-logs` | `admin.chatbot-logs.index` | `Admin\ChatbotLogController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Log chatbot |
| Miftah | GET | `/admin/settings` | `admin.settings.index` | `Admin\SettingController@index` | `web`, `auth`, `role:admin` | `<x-layouts.dashboard area="admin">` | Setting kontak, sosial, rekening, QRIS |
| Miftah | PUT | `/admin/settings` | `admin.settings.update` | `Admin\SettingController@update` | `web`, `auth`, `role:admin` | Redirect admin settings | Update setting umum |
| Rasky | GET | `/instructor/dashboard` | `instructor.dashboard` | `Instructor\DashboardController@index` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` | Dashboard instructor v1 minimal |
| Rasky | GET | `/instructor/classes` | `instructor.classes.index` | `Instructor\ClassController@index` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` | Kelas yang diajar |
| Rasky | GET | `/instructor/classes/{class}` | `instructor.classes.show` | `Instructor\ClassController@show` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` | Detail kelas milik instructor |
| Rasky | GET | `/instructor/students` | `instructor.students.index` | `Instructor\StudentController@index` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` | Siswa dari kelas yang diajar |
| Rasky | GET | `/instructor/report-cards` | `instructor.report-cards.index` | `Instructor\ReportCardController@index` | `web`, `auth`, `role:instructor` | `<x-layouts.dashboard area="instructor">` | Assessment/rapor terkait kelas instructor |

## Ringkasan Pembagian

| Owner | Jumlah Route | Area Utama |
| --- | ---: | --- |
| Miftah | 28 | Public website, reels, contact, chatbot, CMS/content |
| Mecca | 28 | Program discovery, student dashboard, academic master data |
| Mia | 18 | Registration, payment, admin verification |
| Rasky | 29 | Auth, program detail, admin dashboard/workflow, report cards, exports, instructor |

Pembagian tidak dihitung hanya dari jumlah route mentah. Mia memiliki route lebih sedikit karena halaman pendaftaran dan pembayaran memiliki form besar, upload bukti, validasi, dan workflow yang lebih berat. Mecca dan Rasky memegang banyak halaman dashboard yang sebagian bisa memakai pola CRUD berulang.

## Catatan Implementasi

- Controller tetap tipis: validasi memakai `FormRequest`, business logic memakai service.
- Route admin dapat dibuat dengan `Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])`.
- Route student dapat dibuat dengan `Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])`.
- Route instructor dapat dibuat dengan `Route::prefix('instructor')->name('instructor.')->middleware(['auth', 'role:instructor'])`.
- Untuk route public, gunakan prefix name `public.` kecuali flow registration yang memakai prefix name `registrations.`.
- Untuk halaman yang punya referensi Stitch, ikuti folder referensi yang tercatat di kolom notes.

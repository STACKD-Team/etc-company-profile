# Feature Documentation

Dokumen ini menjadi baseline dokumentasi fitur ETC Planet. Detail fitur akan dilengkapi lagi pada minggu ke-14 setelah analisis kebutuhan dan implementasi utama makin stabil.

## Public Website

| Item | Keterangan |
| --- | --- |
| Tujuan | Menampilkan profil resmi ETC Planet, program, fasilitas, galeri, reels, FAQ, dan kontak. |
| Aktor | Pengunjung, calon siswa, orang tua. |
| Alur | Pengunjung membuka beranda, melihat program, membaca detail, menghubungi ETC, atau lanjut ke pendaftaran. |
| Route | `/`, `/about`, `/programs`, `/programs/{program}`, `/team`, `/facilities`, `/gallery`, `/contact`, `/faq`, `/reels` |
| Controller | `Public\HomeController`, `Public\ProgramController`, `Public\ContactController`, `Public\ReelController`, dan controller public lain. |
| Screenshot | `context/stitch_etc_planet_digital_hub/beranda_lengkap_etc_planet/screen.png` |
| Status | Baseline tersedia, dilengkapi sesuai progres implementasi. |

## Registration dan Payment Flow

| Item | Keterangan |
| --- | --- |
| Tujuan | Mendigitalisasi proses pendaftaran calon siswa dari pilih program sampai konfirmasi pembayaran. |
| Aktor | Calon siswa, orang tua, admin. |
| Alur | Calon siswa memilih program, mengisi form, memilih metode pembayaran, upload bukti, lalu menerima konfirmasi pendaftaran. |
| Route | `/registration`, `/registration/programs`, `/registration/form/{program?}`, `/registration/payment/{registration}`, `/registration/confirmation/{registration}` |
| Controller | `Public\RegistrationStartController`, `Public\RegistrationProgramController`, `Public\RegistrationController`, `Public\RegistrationPaymentController`, `Public\RegistrationConfirmationController`. |
| Screenshot | `context/stitch_etc_planet_digital_hub/pilih_program_etc_planet/screen.png`, `context/stitch_etc_planet_digital_hub/pendaftaran_online_lengkap_etc_planet/screen.png`, `context/stitch_etc_planet_digital_hub/pembayaran_etc_planet/screen.png`, `context/stitch_etc_planet_digital_hub/konfirmasi_pendaftaran_etc_planet/screen.png` |
| Status | Baseline tersedia, validasi detail mengikuti form fisik di `context/FORMULIR PENDAFTARAN.jpeg`. |

## Student Dashboard

| Item | Keterangan |
| --- | --- |
| Tujuan | Memberi siswa akses ke data pribadi, kelas, riwayat belajar, pembayaran, bantuan, dan rapor. |
| Aktor | Student. |
| Alur | Siswa login, masuk dashboard, melihat kelas aktif, riwayat pembayaran, dan mengunduh rapor yang sudah dipublish admin. |
| Route | `/student/dashboard`, `/student/profile`, `/student/classes`, `/student/learning-history`, `/student/payments`, `/student/report-cards`, `/student/help` |
| Controller | `Student\DashboardController`, `Student\ProfileController`, `Student\ClassController`, `Student\PaymentController`, `Student\ReportCardController`, dan controller student lain. |
| Screenshot | `context/stitch_etc_planet_digital_hub/dashboard_siswa_etc_planet/screen.png` |
| Status | Baseline tersedia; akses wajib memakai middleware `auth` dan `role:student`. |

## Admin Dashboard

| Item | Keterangan |
| --- | --- |
| Tujuan | Membantu admin mengelola operasional ETC dari pendaftaran sampai laporan. |
| Aktor | Admin. |
| Alur | Admin login, memantau dashboard, memverifikasi pembayaran, menjadwalkan placement test, mengelola siswa/kelas/program, membuat rapor, export laporan, dan mengelola konten. |
| Route | `/admin/dashboard`, `/admin/registrations`, `/admin/payments`, `/admin/placement-tests`, `/admin/students`, `/admin/programs`, `/admin/classes`, `/admin/enrollments`, `/admin/report-cards`, `/admin/exports/students`, `/admin/reels`, `/admin/contents`, `/admin/settings` |
| Controller | Controller di namespace `Admin\*Controller`. |
| Screenshot | `context/stitch_etc_planet_digital_hub/dashboard_admin_etc_planet/screen.png` |
| Status | Baseline tersedia; akses wajib memakai middleware `auth` dan `role:admin`. |

## Instructor Dashboard

| Item | Keterangan |
| --- | --- |
| Tujuan | Menyediakan area ringkas untuk instructor melihat kelas, siswa, dan report card terkait. |
| Aktor | Instructor. |
| Alur | Instructor login, melihat dashboard, kelas yang diajar, siswa, dan report card terkait. |
| Route | `/instructor/dashboard`, `/instructor/classes`, `/instructor/students`, `/instructor/report-cards` |
| Controller | `Instructor\DashboardController`, `Instructor\ClassController`, `Instructor\StudentController`, `Instructor\ReportCardController`. |
| Screenshot | Belum ada screenshot khusus; dashboard mengikuti pola dashboard internal. |
| Status | Baseline tersedia; scope v1 minimal. |

## Report Cards dan Export

| Item | Keterangan |
| --- | --- |
| Tujuan | Menghasilkan rapor akhir pembelajaran dan rekap siswa sesuai template dokumen ETC. |
| Aktor | Admin, student, instructor. |
| Alur | Admin membuat report card, preview, publish, lalu siswa dapat melihat dan mengunduh rapor. Admin juga dapat export rekap siswa. |
| Route | `/admin/report-cards`, `/admin/report-cards/{reportCard}/publish`, `/student/report-cards`, `/student/report-cards/{reportCard}/download`, `/admin/exports/students`, `/admin/exports/report-cards` |
| Controller | `Admin\ReportCardController`, `Admin\ReportCardPublishController`, `Student\ReportCardController`, `Student\ReportCardDownloadController`, `Admin\StudentExportController`, `Admin\ReportCardExportController`. |
| Screenshot | Mengikuti dashboard admin dan dashboard siswa. |
| Status | Output wajib mengikuti template `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc` dan `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`. |

## CMS, Reels, Contact, dan Chatbot

| Item | Keterangan |
| --- | --- |
| Tujuan | Mengelola konten dinamis website dan interaksi awal pengunjung. |
| Aktor | Admin, pengunjung. |
| Alur | Admin mengelola reels, konten CMS, setting, pesan kontak, dan chatbot logs. Pengunjung melihat konten, mengirim pesan, menonton reels, dan memakai chatbot. |
| Route | `/reels`, `/chatbot/messages`, `/contact`, `/admin/reels`, `/admin/contents`, `/admin/contact-messages`, `/admin/chatbot-logs`, `/admin/settings` |
| Controller | `Public\ReelController`, `Public\ChatbotController`, `Public\ContactController`, `Admin\ReelController`, `Admin\ContentController`, `Admin\ContactMessageController`, `Admin\ChatbotLogController`, `Admin\SettingController`. |
| Screenshot | `context/stitch_etc_planet_digital_hub/beranda_lengkap_etc_planet/screen.png` dan `context/stitch_etc_planet_digital_hub/dashboard_admin_etc_planet/screen.png` |
| Status | Baseline tersedia; detail konten mengikuti tabel `contents`, `reels`, `contact_messages`, dan `chatbot_logs`. |

## Catatan Update Minggu ke-14

Saat analisis kebutuhan selesai, dokumen ini perlu dilengkapi dengan:

- Screenshot aplikasi aktual, bukan hanya referensi context.
- Detail validasi setiap form penting.
- Kondisi sukses, gagal, empty state, dan error state.
- Hak akses per fitur.
- Acceptance criteria per fitur.

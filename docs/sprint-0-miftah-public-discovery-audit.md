# Sprint 0 Miftah Public Discovery Audit

Dokumen ini adalah artefak Sprint 0 untuk area Miftah sesuai `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`. Scope audit hanya public discovery: home, about, team, facilities, gallery, contact, FAQ, chatbot public, reels public, dan program discovery UI.

## Status Foundation

- Boot Laravel sudah dipulihkan dengan dependency Filament terpasang kembali di `vendor/filament`.
- `php artisan route:list --name=public` berhasil dan menampilkan 15 route public Miftah.
- Lockfile Composer sebelumnya tidak sinkron karena `composer.json` meminta `maatwebsite/excel`, tetapi `composer.lock` belum memuat package tersebut. Lockfile disinkronkan agar dependency install dapat berjalan.
- Karena PHP lokal adalah 8.5.3 sedangkan `phpoffice/phpspreadsheet 1.30.5` mendeklarasikan dukungan sampai `<8.5`, Composer update dijalankan dengan `--ignore-platform-req=php` untuk environment dev ini.

## Route Matrix

| Route name | Method | URI | Controller | Middleware penting | View/response | Data source | Status | Gap Sprint berikutnya |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `public.home` | GET | `/` | `Public\HomeController@index` | `web` | `public.home` | `PublicDiscoveryService`, `programs`, `reels`, `users`, `contents` | Siap Sprint 0 | Polish value proposition dan CTA untuk Sprint 1. |
| `public.about` | GET | `/about` | `Public\AboutController@index` | `web` | `public.about` | `contents` type `page`, settings | Siap Sprint 0 | Pastikan konten final ETC sudah dipublish. |
| `public.team.index` | GET | `/team` | `Public\TeamController@index` | `web` | `public.team.index` | `users` role instructor | Siap Sprint 0 | Tambahkan foto/bio final saat data siap. |
| `public.facilities.index` | GET | `/facilities` | `Public\FacilityController@index` | `web` | `public.facilities.index` | `contents` type `room` | Siap Sprint 0 | Lengkapi media fasilitas final. |
| `public.gallery.index` | GET | `/gallery` | `Public\GalleryController@index` | `web` | `public.gallery.index` | `contents` type `gallery` | Siap Sprint 0 | Lengkapi galeri final dan alt text. |
| `public.contact.index` | GET | `/contact` | `Public\ContactController@index` | `web` | `public.contact.index` | settings, optional active program | Siap Sprint 0 | Tambahkan map/link sosial final bila data tersedia. |
| `public.contact.store` | POST | `/contact` | `Public\ContactController@store` | `web`, `throttle:contact` | redirect | `StoreContactMessageRequest`, `ContactMessageService` | Siap Sprint 0 | Bisa dipindah ke shared field wrapper jika diputuskan untuk form public. |
| `public.faq.index` | GET | `/faq` | `Public\FaqController@index` | `web` | `public.faq.index` | `contents` page `faq`, FAQ fallback | Siap Sprint 0 | FAQ menjadi bahan knowledge RAG saat Sprint 3. |
| `public.chatbot.messages.store` | POST | `/chatbot/messages` | `Public\ChatbotController@store` | `web`, `throttle:chatbot` | JSON | `StoreChatbotMessageRequest`, `PublicDiscoveryService`, `ChatbotLogService` | Siap Sprint 0 | Masih rule-based; perlu RAG NVIDIA + Qdrant di Sprint 3 bersama Mia. |
| `public.reels.index` | GET | `/reels` | `Public\ReelController@index` | `web` | `public.reels.index` | published `reels` | Siap Sprint 0 | Redesign menjadi vertical short-video feed di Sprint 1. |
| `public.reels.show` | GET | `/reels/{reel}` | `Public\ReelController@show` | `web` | `public.reels.show` | published `reels` | Siap Sprint 0 | Detail perlu terasa lebih seperti reel, bukan artikel. |
| `public.reels.views.store` | POST | `/reels/{reel}/views` | `Public\ReelViewController@store` | `web`, `throttle:reels` | JSON | `ReelService` | Siap Sprint 0 | Pertimbangkan de-dupe view lebih ketat di Sprint polish. |
| `public.reels.likes.store` | POST | `/reels/{reel}/likes` | `Public\ReelLikeController@store` | `web`, `throttle:reels` | JSON | session liked reels, `ReelService` | Siap Sprint 0 | Pertimbangkan auth/session analytics jika dibutuhkan. |
| `public.programs.index` | GET | `/programs` | `Public\ProgramController@index` | `web` | `public.programs.index` | active `programs` | Siap Sprint 0 | Filter/card bisa dipoles Sprint 1. |
| `public.programs.show` | GET | `/programs/{program}` | `Public\ProgramController@show` | `web` | `public.programs.show` | active `programs`, class/instructor, `config/program_details.php` | Siap Sprint 0 | Jadwal/instructor perlu data final agar detail makin akurat. |

## Prioritas Halaman

| Prioritas | Halaman/flow | Alasan |
| --- | --- | --- |
| P0 | Home, programs index, contact, chatbot, reels index/show | Pintu masuk calon siswa/orang tua dan flow discovery utama. |
| P1 | Program detail dan filter program | Membantu calon siswa memahami biaya, durasi, target usia, jadwal, dan CTA daftar. |
| P2 | About, team, facilities, gallery, FAQ | Membangun kredibilitas dan menjawab pertanyaan umum. |
| P3 | Redesign reels vertical short-video | Backlog UI Sprint 1; foundation endpoint view/like sudah siap. |

## Audit Gap Sprint 0

- Raw field/button: form contact dan chatbot public masih memakai field/button Blade custom. Ini dapat diterima untuk public marketing page sesuai dokumen, tetapi perlu diputuskan apakah form public juga akan memakai wrapper `x-ui.*`.
- Raw table/modal/badge/pagination: tidak ditemukan kebutuhan table/dashboard di area public Miftah. Kontrak `x-ui.data-table` lebih relevan untuk dashboard/admin/student/instructor.
- Media storage: `MediaStorageService` masih mendukung local public storage dan Firebase fallback. Ini gap integrasi global menuju Cloudinary, bukan pekerjaan implementasi Miftah di Sprint 0.
- Payment manual: registration/payment masih memakai bukti transfer/manual. Ini area lintas-owner dan dicatat sebagai gap menuju Midtrans, bukan implementasi Miftah di Sprint 0.
- Chatbot: endpoint public sudah tervalidasi, throttled, dan logging ke `chatbot_logs`, tetapi jawaban masih rule-based. RAG dengan NVIDIA dan Qdrant adalah backlog Sprint 3 bersama Mia.
- Reels: public hanya menampilkan published reels dan endpoint view/like sudah terkontrol, tetapi pengalaman UI belum sepenuhnya vertical short-video app. Ini prioritas Sprint 1.
- Hardcode bisnis: public view masih punya fallback teks/asset untuk empty state dan development. Data utama program, konten, settings, reels, dan instructor sudah diambil dari DB/config.

## Standar Foundation Miftah

- URI dan route name public tetap Bahasa Inggris dan tidak diubah.
- Controller public tetap tipis: menerima request, memanggil service/query terarah, lalu mengembalikan view/JSON/redirect.
- Request mutating memakai FormRequest: contact dan chatbot sudah tervalidasi.
- Tidak memakai `$request->all()` di controller public.
- Public layout memakai satu sumber navbar, footer, dan chatbot melalui `resources/views/components/layouts/public.blade.php`.
- Semua POST public yang relevan memakai throttle: `contact`, `chatbot`, dan `reels`.
- Public hanya menampilkan program aktif dan reels yang sudah publish.

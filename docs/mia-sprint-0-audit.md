# Audit Sprint 0 Mia - ETC Planet

Dokumen ini menjadi artefak Sprint 0 untuk Mia. Fokus Sprint 0 adalah alignment, audit gap, prioritas halaman, dan keputusan teknis awal. Implementasi penuh Midtrans, Cloudinary, RAG, Qdrant, dan Filament Resource masuk sprint berikutnya.

## Identitas Owner

- Developer: Mia.
- Area utama: admin panel, Filament admin resources, dashboard admin, CMS/settings, registration/payment monitoring, Midtrans, Cloudinary, RAG admin tooling, dan knowledge source uploader.
- Sumber ownership aktif: `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.
- Catatan: `context/WEB_ROUTES_ETC.md` tetap berguna sebagai referensi route lama, tetapi dokumen pembagian tugas menyatakan ownership terbaru mengikuti dokumen pembagian tugas.

## Audit Route dan Foundation

Fondasi yang sudah ada:

- Route web sudah dipisah per modul melalui `routes/web.php`.
- Route area Mia yang aktif sudah tersedia untuk registration, admin registrations, admin payments, dan student payments.
- Shared UI component dashboard sudah tersedia di `resources/views/components/ui/`.
- `composer.json` dan `composer.lock` sudah mencantumkan `filament/filament`.

Catatan validasi:

- `php artisan route:list` sudah berhasil setelah dependency vendor dipulihkan.
- Route audit menampilkan 120 route.
- Route Mia untuk registration, payment, admin registrations, admin payments, student payments, dan route Filament admin sudah terdeteksi.
- Dependency dipulihkan dengan `composer update maatwebsite/excel --with-all-dependencies --ignore-platform-req=php` karena PHP lokal audit adalah 8.5.3, sedangkan stack project menargetkan PHP 8.3.
- Catatan untuk developer: gunakan PHP 8.3 agar Composer tidak perlu flag ignore platform requirement.

## Audit Gap Area Mia

Route dan halaman prioritas Mia:

- `admin.dashboard`.
- `admin.registrations.*`.
- `admin.payments.*`.
- `admin.settings.*`.
- `admin.contents.*`.
- `admin.reels.*`.
- `admin.chatbot-logs.*`.
- `admin.knowledge-sources.*` sebagai area baru untuk RAG admin tooling.

Gap UI/admin panel:

- Banyak halaman admin masih memakai raw `<input>`, `<select>`, `<button>`, dan `<table>`.
- Halaman yang perlu migrasi awal ke Filament Resource atau `x-ui.*`: registrations, payments, settings, contents, reels, chatbot logs, students, instructors, programs, classes, enrollments.
- `AdminPanelProvider` sudah disiapkan untuk memakai warna primary magenta ETC, tetapi resource Filament utama belum dibuat.

Gap payment:

- Flow pembayaran masih manual dengan upload bukti transfer.
- Admin payment page masih menyediakan action verify/reject manual.
- Route proof upload dan verify/reject masih aktif sebagai workflow utama.
- Belum ada `config/midtrans.php`, `MidtransPaymentService`, webhook notification controller, atau tabel audit `midtrans_notifications`.

Gap storage:

- `MediaStorageService` masih memakai Firebase jika tersedia dan local public disk sebagai fallback.
- `.env.example` sebelumnya masih memakai env Firebase.
- Belum ada config/disk Cloudinary dan belum ada kontrak public id/path untuk delete/retrieve file Cloudinary.

Gap RAG dan knowledge source:

- Chatbot belum memakai RAG dengan NVIDIA dan Qdrant.
- Belum ada tabel `rag_knowledge_sources` dan `rag_knowledge_chunks`.
- Belum ada service `RagChatService`, `KnowledgeSourceService`, `TextExtractionService`, `EmbeddingService`, `QdrantVectorService`, atau job indexing.
- Belum ada halaman admin `Knowledge Sources`.

## Keputusan Integrasi Sprint 0

- Midtrans menggantikan upload bukti pembayaran manual sebagai flow utama.
- Route upload bukti dan verify/reject admin tidak langsung dihapus; tandai deprecated sampai flow Midtrans stabil.
- Cloudinary menggantikan Firebase/local storage untuk file, foto, video, dan dokumen utama.
- Local storage tetap boleh dipakai sebagai fallback development.
- RAG chatbot memakai NVIDIA endpoint dan Qdrant vector database.
- Knowledge source dikelola admin lewat Filament Resource/action.
- Semua payload Midtrans notification harus disimpan untuk audit dan diproses idempotent.
- Semua upload knowledge source harus menyimpan arsip file, metadata, status indexing, preview teks, dan error message bila gagal.

## Prioritas Sprint Berikutnya untuk Mia

1. Migrasi admin registrations dan payments menjadi monitoring yang compact, searchable, filterable, sortable, dan memakai badge status konsisten.
2. Tambahkan config awal Midtrans, Cloudinary, NVIDIA, dan Qdrant.
3. Implementasikan Midtrans service, webhook, audit table, dan mapping status.
4. Ubah `MediaStorageService` agar memakai Cloudinary melalui satu service upload/delete/URL.
5. Buat Filament Resource prioritas: registrations, payments/transactions, settings, contents/CMS, reels, chatbot logs, dan knowledge sources.
6. Implementasikan migration dan service RAG knowledge source.

## Acceptance Sprint 0 Mia

- Ada artefak audit gap dan prioritas area Mia.
- Keputusan integrasi Midtrans, Cloudinary, NVIDIA RAG, dan Qdrant terdokumentasi.
- Status route audit terdokumentasi dengan blocker yang jelas.
- Env contract integrasi tersedia di `.env.example`.
- Admin Filament brand sudah diarahkan ke magenta ETC.

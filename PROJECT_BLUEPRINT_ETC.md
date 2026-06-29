# PROJECT BLUEPRINT ETC PLANET

## 1. Ringkasan Project

**Nama project**: Website Company Profile ETC Planet Padang  
**Mitra**: LKP ETC (Education Tutorial Centre) / ETC Planet  
**Alamat mitra**: Jl. S. Parman No. 202B, Ulak Karang Selatan, Padang  
**Stack utama**: Laravel fullstack, PHP 8.3, MySQL, Blade, Vite, Tailwind CSS 4  
**Sumber kebenaran project**: semua file di folder `context/`

Website ini adalah platform company profile sekaligus sistem operasional awal untuk ETC Planet. Project tidak hanya menampilkan profil lembaga, tetapi juga mendigitalisasi proses promosi, pendaftaran, pembayaran, pengelolaan data siswa, rapor akhir pembelajaran, dan rekap data siswa.

Target SDG dari Project Charter:

- **SDG 4 - Pendidikan Berkualitas**: memperluas akses informasi layanan pendidikan non-formal dan mempermudah pemantauan proses belajar siswa.
- **SDG 8 - Pekerjaan Layak dan Pertumbuhan Ekonomi**: membantu pertumbuhan lembaga pendidikan lokal melalui digitalisasi pemasaran dan pendaftaran.
- **SDG 9 - Industri, Inovasi, dan Infrastruktur**: membangun infrastruktur digital mandiri dengan reels, chatbot AI, dan pembayaran digital.

Masalah utama saat ini:

- Informasi dan konsultasi awal masih banyak melalui WhatsApp/Instagram.
- Pendaftaran masih memakai formulir fisik.
- Rekap data siswa masih dimasukkan manual ke Excel.
- Placement test tetap offline, tetapi jadwal dan data pendaftar perlu terdigitalisasi.
- Rapor akhir pembelajaran perlu bisa dibuat dan diunduh siswa.

Tujuan sistem:

- Menjadi website resmi ETC Planet yang kredibel dan modern.
- Menyediakan reels/video pendek mandiri agar promosi tidak sepenuhnya bergantung pada media sosial pihak ketiga.
- Menyediakan pendaftaran online dengan data sama seperti formulir fisik.
- Menyediakan pembayaran awal melalui QRIS atau transfer bank.
- Menyediakan dashboard siswa untuk biodata, riwayat pembelajaran, pembayaran, dan rapor.
- Menyediakan dashboard admin untuk CMS, pendaftaran, pembayaran, placement test, kelas, siswa, rapor, reels, chatbot log, dan export dokumen.

## 2. Sumber Konteks Wajib

Implementasi harus selalu mengacu pada file berikut:

- `context/Project_Charter_ETC_Updated.pdf`
- `context/SKEMA_DATABASE_LENGKAP.md`
- `context/FORMULIR PENDAFTARAN.jpeg`
- `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`
- `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`
- `context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md`
- Semua contoh HTML dan screenshot di `context/stitch_etc_planet_digital_hub/`

Catatan penting:

- Folder `context/` adalah sumber kebenaran dokumentasi project.
- File yang masih ada di root dengan nama serupa tidak dijadikan sumber utama apabila sudah ada versi di `context/`.
- Output dokumen rapor dan Excel harus mengikuti template contoh, bukan hanya mirip secara isi.

## 3. Arsitektur Sistem

### 3.1 Stack Aplikasi

- **Backend**: Laravel 13.
- **Bahasa**: PHP 8.3.
- **Database**: MySQL dengan engine InnoDB dan charset `utf8mb4`.
- **Frontend**: Blade, Vite, Tailwind CSS 4.
- **Auth**: Laravel authentication dengan role `admin`, `student`, `instructor`.
- **File storage**: Laravel Storage untuk gambar, video reels, bukti pembayaran, avatar, thumbnail, dan dokumen hasil generate.
- **Document generation**: template-based generation untuk Word/PDF rapor dan Excel rekap siswa.
- **Payment**: QRIS dan transfer bank pada scope awal; payment gateway seperti Midtrans/Xendit dapat ditambahkan sebagai pengembangan.
- **AI chatbot**: service chatbot untuk FAQ program, harga, jadwal, pendaftaran, dan informasi umum ETC.

### 3.2 Role

**Admin**

- Mengelola dashboard operasional.
- Verifikasi pembayaran.
- Menjadwalkan placement test.
- Menentukan kelas setelah placement test.
- Mengelola siswa, program, kelas, instructor, CMS, reels, dan pesan kontak.
- Generate dokumen rapor dan rekap siswa.

**Student**

- Melihat dashboard siswa.
- Mengelola atau melihat biodata.
- Melihat kelas aktif dan riwayat pembelajaran.
- Melihat riwayat pembayaran.
- Mengunduh atau mencetak rapor yang sudah dipublish admin.

**Instructor**

- Menjadi pengajar kelas.
- Ditampilkan di halaman team jika `show_on_team_page = 1`.
- Menjadi Talent pada dokumen rapor.
- Memiliki dashboard untuk melihat profile, class yang diajar, student terkait, dan report card/assessment sesuai policy.

### 3.3 Prinsip Implementasi

- Gunakan pola Laravel MVC yang umum: route, controller, request validation, model, policy/middleware, Blade view.
- Validasi semua input user, terutama pendaftaran, login, upload bukti pembayaran, upload reels, dan CMS.
- Gunakan CSRF protection untuk semua form.
- Gunakan authorization berbasis role untuk dashboard.
- Jangan hard delete data penting akademik/finansial apabila masih dibutuhkan sebagai riwayat.
- Semua dokumen output memakai template contoh sebagai baseline.

## 4. Database Blueprint

Skema database mengikuti `context/SKEMA_DATABASE_LENGKAP.md`. Total tabel utama: 11.

### 4.1 Ringkasan Tabel

| Tabel | Fungsi |
| --- | --- |
| `users` | Akun admin, instructor, dan student, termasuk profil siswa/instructor |
| `programs` | Master program kursus |
| `rooms` | Master ruangan/fasilitas belajar |
| `classes` | Kelas konkret yang berjalan |
| `registrations` | Pendaftaran online dan data pembayaran |
| `enrollments` | Riwayat siswa mengikuti kelas |
| `report_cards` | Rapor akhir pembelajaran |
| `reels` | Video pendek/reels interaktif |
| `contents` | CMS sederhana untuk galeri, partner, profile, FAQ, dan testimonial |
| `contact_messages` | Pesan dari form kontak |
| `chatbot_logs` | Log tanya-jawab chatbot |

### 4.2 Relasi Utama

- `programs` memiliki banyak `classes`.
- `programs` memiliki banyak `registrations`.
- `classes` dimiliki oleh `programs` dan opsional memiliki `instructor`.
- `classes` opsional memiliki `room` dari tabel `rooms`.
- `rooms` memiliki banyak `classes`.
- `registrations` opsional terhubung ke `users` dan `classes`, wajib terhubung ke `programs`.
- `enrollments` menghubungkan `users` student dengan `classes`.
- `report_cards` satu-satu dengan `enrollments`.
- `report_cards` memiliki instructor, academic director, dan managing director dari `users`.
- `chatbot_logs` opsional terhubung ke `users`.

### 4.3 Flow Data Utama

1. Calon siswa memilih program.
2. Calon siswa mengisi form registrasi sesuai `context/FORMULIR PENDAFTARAN.jpeg`.
3. Sistem membuat `registrations` dengan status `pending_payment`.
4. Calon siswa memilih QRIS atau transfer bank dan upload bukti jika metode manual.
5. Admin memverifikasi pembayaran dan status menjadi `paid`.
6. Admin menjadwalkan placement test offline.
7. Setelah placement test dinilai, admin menentukan class.
8. Sistem membuat user student jika belum ada, lalu membuat enrollment.
9. Setelah kelas selesai, admin membuat report card.
10. Admin publish report card agar siswa bisa melihat dan mengunduhnya.

Status pendaftaran:

```text
pending_payment -> paid -> placement_test -> enrolled
                                      -> rejected
                                      -> cancelled
```

## 5. Form Registrasi Online

Form registrasi online harus mengikuti isi form fisik `context/FORMULIR PENDAFTARAN.jpeg`.

### 5.1 Field Wajib/Utama

Field utama:

- Date
- Full Name / Nama Lengkap
- Place / Date of Birth / Tempat Tgl Lahir
- Sex / Jenis Kelamin
- Address / Alamat
- Religion / Agama
- Occupation / School / College / Pekerjaan / Sekolah / Kampus
- Mobile Phone / No Handphone
- Email / Nama Email
- NISN
- NIK
- KPS Receiver / Penerima KPS
- No KPS
- Worthy of PIP / Layak PIP
- The reason is worth PIP / Alasan Layak PIP
- No KIP
- Kewarganegaraan
- Alamat
- RT/RW
- Kode Pos
- Nama Desa / Kelurahan
- Provinsi
- Kab/Kota
- Kecamatan
- Kelurahan
- Tinggal Bersama
- Alat Transportasi
- Nama Ibu
- Nama Ayah

Catatan mapping:

- Field identitas, alamat, dan keluarga masuk ke profil student di `users`.
- Snapshot nama/email/phone tetap disimpan di `registrations` untuk menjaga histori pendaftaran.
- Pilihan program, jadwal hari, dan jam disimpan di `registrations` sebagai preferensi awal.

### 5.2 Pilihan Applying For

Pilihan program pada form harus mengikuti gambar fisik:

- TK
- Pre/Super Toddlers
- SD/Super Toddlers
- SMP/Teen
- SMA/Excel Teen
- Dewasa/Adult University
- Khusus/Private
- Test TOEFL/TOEIC/IELTS
- Preparation TOEFL/TOEIC/IELTS/UN

Mapping ke `programs` harus dibuat eksplisit di seed/admin CMS. Nama tampilan boleh mengikuti bahasa user, tetapi nilai bisnis harus tetap mengacu ke pilihan fisik tersebut.

### 5.3 Pilihan Days Schedule

- Mon-Wed
- Tues-Thurs
- Wed-Fri
- Sat-Sun
- Request Schedule

Mapping awal ke `registrations.preferred_days`:

| Form fisik | Nilai sistem |
| --- | --- |
| Mon-Wed | `mon_wed` |
| Tues-Thurs | `tue_thu` |
| Wed-Fri | `wed_fri` |
| Sat-Sun | `sat_sun` |
| Request Schedule | `request` |

### 5.4 Pilihan Time Schedule

- 09.00-10.30
- 11.00-12.30
- 13.00-14.30
- 15.00-16.30
- 17.00-18.30

Simpan sebagai teks di `registrations.preferred_time` agar sesuai dengan format asli.

### 5.5 Tanda Tangan Form

Form fisik memiliki area tanda tangan:

- Kasir/CRE
- Customer

Untuk v1 digital:

- Tanda tangan manual tidak wajib pada form online.
- Sistem harus menyimpan `registration_code`, waktu submit, dan identitas pembayaran sebagai bukti digital.
- Jika nanti dibuat bukti pendaftaran PDF, layout boleh meniru form fisik dan menyertakan area tanda tangan.

## 6. Output Dokumen Wajib

Dokumen output adalah bagian kritis project. Jangan membuat layout bebas. Gunakan pendekatan template-based generation.

### 6.1 Rapor Akhir Pembelajaran

Template wajib:

- `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`

Format output wajib mempertahankan struktur contoh:

- Judul: `STUDENT EVALUATION`
- Identitas:
  - `NAME`
  - `CLASS`
  - `DAYS`
  - `TIME`
- Bagian `WRITTEN TEST`
- Kolom `SCORE`
- Bagian `OVERALL CLASS ASSESMENT` sesuai template contoh
- Item written test:
  - Listening
  - Vocabulary
  - Structure
  - Reading
  - Writing
- Item class assessment:
  - Pronunciation Fluency
  - Sentence and Word Arrangement
  - Class Participation
  - Questioning Skill
  - Analyzing Skill
- `TOTAL SCORE`
- `NEXT CLASS`
- `Comments and Suggestions`
- Tanda tangan:
  - Managing Director
  - Academic Director
  - Talent

Mapping data rapor:

| Template rapor | Sumber data |
| --- | --- |
| NAME | `users.full_name` atau `users.name` dari enrollment |
| CLASS | `classes.name` |
| DAYS | `classes.schedule_days` |
| TIME | `classes.schedule_time` |
| Listening | `report_cards.score_listening` |
| Vocabulary | `report_cards.score_vocabulary` |
| Structure | `report_cards.score_structure` |
| Reading | `report_cards.score_reading` |
| Writing | `report_cards.score_writing` |
| Pronunciation Fluency | `report_cards.grade_pronunciation` |
| Sentence and Word Arrangement | `report_cards.grade_sentence_arrangement` |
| Class Participation | `report_cards.grade_class_participation` |
| Questioning Skill | `report_cards.grade_questioning_skill` |
| Analyzing Skill | `report_cards.grade_analyzing_skill` |
| Total Score | `report_cards.total_score` |
| Next Class | `report_cards.next_class` |
| Comments and Suggestions | `report_cards.comments` |
| Talent | `report_cards.instructor_id` |
| Academic Director | `report_cards.academic_director_id` |
| Managing Director | `report_cards.managing_director_id` |

Aturan output:

- Output utama mengikuti format `.doc` contoh.
- PDF boleh disediakan sebagai hasil turunan download, tetapi layout harus tetap sama.
- Jika memakai library, gunakan template cloning atau templating dokumen, bukan membangun ulang layout secara bebas.
- Admin harus bisa preview sebelum publish.
- Student hanya bisa melihat rapor yang `is_published = 1`.

### 6.2 Laporan Rekapan Siswa Terdaftar

Template wajib:

- `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`

Workbook contoh:

- Sheet: `Sheet1`
- Judul: `BUKU INDUK ENGLISH TUTORIAL CENTRE 2025`

Header wajib:

- NO
- NO INDUK
- NAME
- CLASS
- SEX
  - M
  - F
- BIRTH
  - PLACE
  - DATE
- STATUS
- PLACE/ADDRESS
- TGL DAFTAR
- CONTACT PERSON
- PHOTO
- KET

Mapping data Excel:

| Kolom Excel | Sumber data |
| --- | --- |
| NO | Nomor urut export |
| NO INDUK | `users.no_induk` |
| NAME | `users.full_name` atau `users.name` |
| CLASS | `classes.name` melalui enrollment atau assigned registration |
| SEX M/F | `users.sex` |
| BIRTH PLACE | `users.place_of_birth` |
| BIRTH DATE | `users.date_of_birth` |
| STATUS | `users.status` |
| PLACE/ADDRESS | Gabungan tempat/alamat dari profil siswa |
| TGL DAFTAR | `registrations.created_at` atau tanggal pendaftaran resmi |
| CONTACT PERSON | `users.mobile_phone` atau `registrations.applicant_phone` |
| PHOTO | `users.avatar` atau kosong sesuai template |
| KET | Catatan admin atau `registrations.notes` |

Aturan output:

- Output utama tetap `.xlsx`.
- Layout, heading, warna, border, lebar kolom, merge cell, dan style harus mengikuti template contoh.
- Gunakan template cloning agar format sama persis semampu library.
- Export harus bisa difilter berdasarkan tahun/periode/status/program/kelas.

## 7. Halaman dan Modul

### 7.1 Public Website

**Beranda**

- Hero ETC Planet.
- CTA `Daftar Sekarang` dan `Lihat Program`.
- Statistik lembaga.
- Program unggulan.
- Alur pendaftaran.
- Galeri reels.
- Testimoni.
- Pengajar profesional.
- Footer dan chatbot.

Referensi: `context/stitch_etc_planet_digital_hub/beranda_lengkap_etc_planet/`

**Program**

- Daftar program.
- Filter kategori.
- Card program dengan harga, target usia, tipe, durasi, dan CTA detail/daftar.

**Detail Program**

- Deskripsi program.
- Yang akan dipelajari.
- Jadwal.
- Instruktur.
- Harga/biaya pendaftaran.
- CTA daftar.

Referensi: `context/stitch_etc_planet_digital_hub/english_conversation_detail_etc_planet/`

**Reels**

- Feed video pendek 9:16.
- Kategori promosi, dokumentasi, edukasi, testimoni, event.
- Counter view dan like.
- Player modal/detail.

**Tentang ETC**

- Profil lembaga.
- Visi/misi.
- Nilai dan pendekatan pembelajaran.
- Alamat dan kontak.

**Team/Pengajar**

- Diambil dari `users` role instructor dengan `show_on_team_page = 1`.
- Menampilkan foto, jabatan, spesialisasi, dan bio.

**Fasilitas/Ruangan**

- Diambil dari tabel `rooms`.
- Tampilkan nama ruangan, kapasitas, fasilitas, dan foto.

**Galeri Kegiatan**

- Diambil dari `contents` type `gallery`.
- Bisa berisi multi image dan metadata event.

**Kontak**

- Form kontak ke `contact_messages`.
- Alamat, WhatsApp, Instagram, dan map.

**FAQ**

- Pertanyaan umum program, harga, jadwal, pendaftaran, pembayaran, dan placement test.
- FAQ menjadi basis knowledge sederhana untuk chatbot.

**Login**

- Akses admin dan siswa.

### 7.2 Registration Flow

**Pilih Program**

- Menampilkan pilihan program sesuai form fisik dan data `programs`.
- Menampilkan ringkasan biaya pendaftaran dan biaya program.

Referensi: `context/stitch_etc_planet_digital_hub/pilih_program_etc_planet/`

**Form Registrasi**

- Field lengkap sesuai `context/FORMULIR PENDAFTARAN.jpeg`.
- Section disarankan:
  - Data diri
  - Data pendidikan/kontak
  - Data KPS/PIP/KIP
  - Alamat
  - Orang tua
  - Pilihan program dan jadwal
  - Ringkasan

Referensi visual: `context/stitch_etc_planet_digital_hub/pendaftaran_online_lengkap_etc_planet/`

**Pembayaran**

- Ringkasan pesanan.
- Metode QRIS.
- Metode transfer bank.
- Upload bukti pembayaran.
- Checkbox konfirmasi sudah membayar.

Referensi: `context/stitch_etc_planet_digital_hub/pembayaran_etc_planet/`

**Konfirmasi**

- Detail pendaftaran.
- Registration code.
- Status pembayaran/menunggu verifikasi.
- CTA unduh bukti pendaftaran dan masuk dashboard.

Referensi: `context/stitch_etc_planet_digital_hub/konfirmasi_pendaftaran_etc_planet/`

### 7.3 Student Dashboard

Referensi: `context/stitch_etc_planet_digital_hub/dashboard_siswa_etc_planet/`

Halaman wajib:

- Overview dashboard.
- Profil saya/biodata.
- Kelas saya.
- Riwayat pembelajaran.
- Rapor.
- Riwayat pembayaran.
- Bantuan/chatbot.

Fitur utama:

- Melihat kelas aktif.
- Melihat progress kelas jika data tersedia.
- Mengunduh rapor yang sudah dipublish.
- Melihat histori pembayaran dan status pendaftaran.
- Mengubah data profil yang diizinkan.

### 7.4 Admin Dashboard

Referensi: `context/stitch_etc_planet_digital_hub/dashboard_admin_etc_planet/`

Halaman wajib:

- Overview dashboard.
- Pendaftaran.
- Verifikasi pembayaran.
- Placement test.
- Data siswa.
- Data instructor.
- Program.
- Kelas.
- Room.
- Enrollment.
- Rapor.
- Reels.
- CMS konten.
- Pesan kontak.
- Chatbot logs.
- Export dokumen.
- Pengaturan.

Fitur utama:

- Melihat statistik siswa, pendaftaran, pendapatan, dan kelas aktif.
- Memproses pendaftaran baru.
- Verifikasi bukti pembayaran.
- Input hasil placement test.
- Assign siswa ke kelas.
- Membuat enrollment.
- Generate rapor dari data report card.
- Publish rapor ke dashboard siswa.
- Export laporan rekap siswa sesuai template Excel.
- Upload dan publish reels.
- Mengelola profile ETC, galeri, partner, testimonial, FAQ, room, dan setting operasional yang diperlukan.

## 8. Design System

Desain wajib mengikuti:

- `context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md`
- HTML dan screenshot di folder Stitch.

### 8.1 Brand

Personality: **Playful Professional**.

Kesan yang harus muncul:

- Ramah untuk siswa.
- Kredibel untuk orang tua dan profesional.
- Modern, bersih, dan akademik.
- Enerjik tanpa terasa berantakan.

### 8.2 Warna

Warna utama:

- Magenta/primary: `#e6007f`
- Primary dark: `#b90065`
- Charcoal: `#2D2D2D`
- Warm surface: `#fff8f8`
- Light pink containers: `#FFE6F3`, `#ffe8ed`, `#ffe1e8`

Distribusi visual:

- 60 persen warm off-white/surface.
- 30 persen neutral/charcoal.
- 10 persen magenta accent.

### 8.3 Tipografi

- Heading: Plus Jakarta Sans, bold.
- Body: Work Sans, regular.
- Tombol: Plus Jakarta Sans, bold.

### 8.4 Komponen

- Button utama: pill, magenta, teks putih, tinggi minimal 48px.
- Button sekunder: outline atau surface.
- Input: radius 8px, border 1px, focus magenta.
- Card: radius sekitar 16px, padding minimal 24px, shadow halus.
- Chip/pill: light pink background.
- Checkbox/radio: hit area besar, active magenta.
- Dashboard sidebar: charcoal background dengan active magenta.
- Mobile bottom nav: charcoal background, active magenta.

### 8.5 Layout

- Container desktop maksimal 1200px.
- Grid 12 kolom desktop, 4 kolom mobile.
- Spacing memakai kelipatan 4px.
- Section publik boleh memakai diagonal cut dan blob dekoratif seperti Stitch.
- Dashboard harus lebih utilitarian, padat, dan mudah discan.

## 9. CMS dan Konten

Gunakan tabel `contents` untuk:

- `gallery`: galeri kegiatan.
- `partner`: partner/lembaga kerja sama.
- `profile`: visi, misi, alamat, telepon, dan informasi umum ETC Padang.
- `faq`: pertanyaan dan jawaban yang mudah dipakai admin awam.
- `testimonial`: testimoni dengan rating 1-5.

Gunakan tabel `rooms` untuk:

- fasilitas/ruangan, termasuk nama, deskripsi, kapasitas, dan gambar.

CMS admin harus menyediakan:

- List konten per tipe.
- Create/edit/delete atau unpublish.
- Upload image/images.
- Display order.
- Status publish.
- Form CMS harus memakai field yang mudah dipahami. Contoh FAQ hanya question dan answer; gallery hanya title, description, dan image; testimonial memiliki nama, role/asal, pesan, rating, dan foto bila ada.

## 10. Reels

Reels adalah fitur unggulan sesuai Project Charter.

Data disimpan di `reels`:

- Title.
- Description.
- Video path.
- Thumbnail path.
- Duration.
- Category.
- Views count.
- Likes count.
- Publish status.
- Published at.

Aturan:

- Video harus dioptimasi agar tidak membebani server.
- Tampilkan thumbnail sebelum video diputar.
- View count bertambah secara terkendali.
- Admin dapat upload, edit, publish, unpublish.
- Public dapat menonton reels dari halaman reels dan section beranda.

## 11. Chatbot AI

Chatbot menjawab pertanyaan umum 24/7:

- Informasi program.
- Harga dan biaya pendaftaran.
- Jadwal.
- Alur pendaftaran.
- Placement test.
- Kontak dan alamat.

Data log disimpan di `chatbot_logs`:

- Session ID.
- User ID opsional.
- User message.
- Bot response.
- Intent.
- Helpful feedback.
- Created at.

V1 boleh memakai rule-based FAQ terlebih dahulu. Integrasi AI API dapat ditambahkan bila siap.

## 12. Keamanan dan Validasi

Wajib:

- CSRF untuk semua form.
- Rate limiting untuk login, contact, chatbot, dan upload.
- Validasi server-side untuk semua input.
- Validasi MIME dan ukuran file untuk upload bukti pembayaran, gambar, dokumen, video.
- Authorization berdasarkan role.
- Password di-hash.
- Data pembayaran tidak boleh diubah sembarang user.
- Student hanya melihat data miliknya.
- Admin action penting sebaiknya punya audit trail minimal melalui timestamps dan status.

## 13. Testing dan Acceptance Criteria

### 13.1 Testing Teknis

- Unit/feature test untuk registration flow.
- Test validasi form registrasi sesuai field fisik.
- Test status pendaftaran.
- Test verifikasi pembayaran.
- Test assign class dan create enrollment.
- Test generate rapor.
- Test export Excel rekap siswa.
- Test authorization admin/student.
- Test contact form.
- Test chatbot log.
- Test reels publish/unpublish.

### 13.2 Acceptance Criteria

Project dianggap sesuai blueprint jika:

- Semua sumber konteks di `context/` sudah diikuti.
- Halaman public, registration, student, dan admin tersedia sesuai page map.
- Form registrasi online memiliki field dan pilihan yang sama dengan form fisik.
- Rapor siswa mengikuti template `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`.
- Rekapan siswa mengikuti template `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`.
- Dashboard siswa bisa menampilkan rapor yang sudah dipublish.
- Dashboard admin bisa generate dokumen dan export laporan.
- Desain visual konsisten dengan Stitch dan DESIGN.md.

## 14. Roadmap Implementasi Disarankan

1. Foundation Laravel: env, auth, role, layout, design tokens.
2. Database: migrations sesuai skema, models, relationships, seed awal.
3. Public website: beranda, program, detail program, kontak, reels read-only.
4. Registration flow: pilih program, form sesuai fisik, pembayaran, konfirmasi.
5. Source alignment Sprint 3+: sinkronkan route/page convention, schema room, CMS type, dan ownership docs.
6. Admin operational flow: dashboard, CRUD/RD utama, datatable konsisten, detail card, related table, export modal, dan CMS dropdown.
7. Rooms dan CMS simplification: tabel `rooms`, `classes.room_id`, CMS profile/gallery/partner/faq/testimonial dengan form ramah admin.
8. Shared role workflow: admin/instructor/student memakai page pattern yang sama dengan policy dan query scope berbeda.
9. Report cards dan export dokumen: CRUD/publish, generate template, download siswa, rekap siswa sesuai template.
10. Integrasi besar setelah flow stabil: Midtrans, Cloudinary, RAG chatbot, Qdrant, dan final polish responsive.

## 15. Keputusan dan Asumsi

- Placement test tetap offline di lokasi ETC.
- Website hanya memfasilitasi pendaftaran awal, pembayaran, dan penjadwalan placement test.
- Penentuan kelas dilakukan admin setelah placement test.
- Output rapor utama mengikuti contoh `.doc`; PDF boleh menjadi turunan.
- Output rekap siswa utama adalah `.xlsx`.
- Template dokumen tidak boleh diganti tanpa persetujuan project owner.
- Dashboard instructor masuk scope flow baru: profile, class, student terkait, dan report card/assessment sesuai policy.

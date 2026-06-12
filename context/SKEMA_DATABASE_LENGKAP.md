# Skema Database — Website Company Profile ETC Padang

**Project**: Website Company Profile ETC (Education Tutorial Centre) Padang
**Stack**: Laravel Fullstack + MySQL
**Charset**: `utf8mb4` / **Engine**: `InnoDB`
**Total Tabel**: 11

---

## Daftar Isi

1. [Ringkasan Tabel](#ringkasan-tabel)
2. [Detail Tabel](#detail-tabel)
   - [1. users](#1-users)
   - [2. programs](#2-programs)
   - [3. rooms](#3-rooms)
   - [4. classes](#4-classes)
   - [5. registrations](#5-registrations)
   - [6. enrollments](#6-enrollments)
   - [7. report_cards](#7-report_cards)
   - [8. reels](#8-reels)
   - [9. contents](#9-contents)
   - [10. contact_messages](#10-contact_messages)
   - [11. chatbot_logs](#11-chatbot_logs)
3. [Diagram Relasi (ERD)](#diagram-relasi-erd)
4. [Daftar Foreign Key](#daftar-foreign-key)
5. [Catatan Implementasi](#catatan-implementasi)

---

## Ringkasan Tabel

| # | Nama Tabel | Fungsi Utama | Jumlah Kolom |
|---|---|---|---|
| 1 | `users` | Akun pengguna + profil siswa + profil instruktur | 35 |
| 2 | `programs` | Master program kursus (English, Mandarin, TOEFL, dll) | 14 |
| 3 | `rooms` | Master ruangan/fasilitas belajar | 7 |
| 4 | `classes` | Kelas konkret yang berjalan (Teen 4, Level 1, dll) | 11 |
| 5 | `registrations` | Pendaftaran online + pembayaran | 19 |
| 6 | `enrollments` | Riwayat siswa di setiap kelas | 7 |
| 7 | `report_cards` | Rapor akhir pembelajaran | 19 |
| 8 | `reels` | Video pendek interaktif | 12 |
| 9 | `contents` | Konten CMS sederhana: galeri, partner, profile, FAQ, testimonial | 10 |
| 10 | `contact_messages` | Pesan dari form kontak pengunjung | 9 |
| 11 | `chatbot_logs` | Riwayat percakapan AI chatbot | 8 |

---

## Detail Tabel

### 1. `users`

**Fungsi**: Tabel master untuk semua akun (admin, instruktur, siswa). Field profil disimpan langsung di sini, dengan kolom yang relevan untuk masing-masing role di-set nullable.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `name` | VARCHAR(150) | NO | — | Nama tampilan (display name) |
| `email` | VARCHAR(150) | NO | — | **UNIQUE**, untuk login |
| `email_verified_at` | TIMESTAMP | YES | NULL | Verifikasi email Laravel |
| `password` | VARCHAR(255) | NO | — | Hash bcrypt |
| `role` | ENUM | NO | `student` | `admin`, `instructor`, `student` |
| `avatar` | VARCHAR(255) | YES | NULL | Path foto profil |
| `is_active` | TINYINT(1) | NO | 1 | 1=aktif, 0=disabled oleh admin |
| `remember_token` | VARCHAR(100) | YES | NULL | Token "remember me" Laravel |
| `no_induk` | VARCHAR(20) | YES | NULL | **UNIQUE**. No induk siswa ETC. NULL untuk admin/instruktur |
| `full_name` | VARCHAR(150) | YES | NULL | Nama lengkap di formulir |
| `place_of_birth` | VARCHAR(100) | YES | NULL | Tempat lahir |
| `date_of_birth` | DATE | YES | NULL | Tanggal lahir |
| `sex` | ENUM | YES | NULL | `M`, `F` |
| `religion` | VARCHAR(30) | YES | NULL | Agama |
| `nationality` | VARCHAR(50) | YES | `Indonesia` | Kewarganegaraan |
| `status` | VARCHAR(50) | YES | NULL | Pelajar/Mahasiswa/Karyawan/Dosen/dll |
| `occupation_school` | VARCHAR(150) | YES | NULL | Pekerjaan/Sekolah/Kampus |
| `mobile_phone` | VARCHAR(20) | YES | NULL | No HP |
| `nisn` | VARCHAR(20) | YES | NULL | Nomor Induk Siswa Nasional |
| `nik` | VARCHAR(20) | YES | NULL | Nomor Induk Kependudukan |
| `kps_receiver` | TINYINT(1) | NO | 0 | Penerima KPS (1=ya) |
| `no_kps` | VARCHAR(30) | YES | NULL | Nomor KPS |
| `worthy_of_pip` | TINYINT(1) | NO | 0 | Layak PIP (1=ya) |
| `no_kip` | VARCHAR(30) | YES | NULL | Nomor KIP |
| `address` | TEXT | YES | NULL | Alamat lengkap |
| `rt_rw` | VARCHAR(10) | YES | NULL | RT/RW |
| `postal_code` | VARCHAR(10) | YES | NULL | Kode pos |
| `village` | VARCHAR(100) | YES | NULL | Desa/Kelurahan |
| `sub_district` | VARCHAR(100) | YES | NULL | Kecamatan |
| `district` | VARCHAR(100) | YES | NULL | Kab/Kota |
| `province` | VARCHAR(100) | YES | NULL | Provinsi |
| `living_with` | VARCHAR(100) | YES | NULL | Tinggal bersama (Orang Tua/Wali) |
| `transportation` | VARCHAR(50) | YES | NULL | Alat transportasi ke ETC |
| `mother_name` | VARCHAR(150) | YES | NULL | Nama ibu |
| `father_name` | VARCHAR(150) | YES | NULL | Nama ayah |
| `instructor_position` | VARCHAR(100) | YES | NULL | Managing/Academic Director/Instructor/CRE |
| `instructor_specialization` | VARCHAR(100) | YES | NULL | English/Mandarin/Japanese |
| `instructor_bio` | TEXT | YES | NULL | Bio singkat untuk halaman team |
| `show_on_team_page` | TINYINT(1) | NO | 0 | Tampilkan di section "Meet The Team" |
| `created_at` | TIMESTAMP | YES | NULL | Timestamp Laravel |
| `updated_at` | TIMESTAMP | YES | NULL | Timestamp Laravel |

**Indexes**:
- `PRIMARY KEY (id)`
- `UNIQUE (email)`
- `UNIQUE (no_induk)`
- `INDEX idx_users_role (role)`
- `INDEX idx_users_no_induk (no_induk)`

**Relasi keluar**: Tidak ada (root table)

**Relasi masuk**:
- `classes.instructor_id` → `users.id`
- `registrations.user_id` → `users.id`
- `enrollments.user_id` → `users.id`
- `report_cards.instructor_id`, `academic_director_id`, `managing_director_id` → `users.id`
- `chatbot_logs.user_id` → `users.id`

---

### 2. `programs`

**Fungsi**: Master data program kursus yang ditawarkan ETC. Kategori (English, Mandarin, dll) jadi kolom enum, bukan tabel terpisah.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `name` | VARCHAR(150) | NO | — | Nama program (English Conversation Teen, IELTS Prep) |
| `slug` | VARCHAR(170) | NO | — | **UNIQUE**, URL-friendly |
| `category` | ENUM | NO | `english` | `english`, `mandarin`, `japanese`, `test_prep`, `soft_skills`, `other` |
| `type` | ENUM | NO | `regular` | `regular`, `private`, `one_on_one` |
| `target_age` | ENUM | YES | `all` | `kids`, `teen`, `adult`, `university`, `all` |
| `description` | TEXT | YES | NULL | Deskripsi panjang |
| `duration_meetings` | INT | YES | 16 | Jumlah pertemuan (default 16x sesuai company profile) |
| `max_students` | INT | YES | 10 | Kapasitas siswa per kelas |
| `price` | DECIMAL(12,2) | NO | 0 | Biaya program |
| `registration_fee` | DECIMAL(12,2) | NO | 200000 | Biaya pendaftaran tetap (Rp 200.000) |
| `thumbnail` | VARCHAR(255) | YES | NULL | Path gambar thumbnail |
| `is_active` | TINYINT(1) | YES | 1 | 1=ditampilkan di website |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `UNIQUE (slug)`
- `INDEX idx_programs_category (category)`
- `INDEX idx_programs_active (is_active)`

**Relasi keluar**: Tidak ada

**Relasi masuk**:
- `classes.program_id` → `programs.id`
- `registrations.program_id` → `programs.id`

---

### 3. `rooms`

**Fungsi**: Master ruangan/fasilitas belajar ETC. Room dikelola sebagai tabel sendiri agar admin bisa CRUD room dan class cukup memilih room dari data yang tersedia.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `name` | VARCHAR(150) | NO | — | Nama ruangan, contoh Hard Rock, Disneyland, Louis Vuitton |
| `description` | TEXT | YES | NULL | Deskripsi ruangan/fasilitas |
| `capacity` | INT UNSIGNED | YES | NULL | Kapasitas siswa |
| `image` | VARCHAR(500) | YES | NULL | Path gambar ruangan |
| `created_at` | TIMESTAMP | YES | NULL | Timestamp Laravel |
| `updated_at` | TIMESTAMP | YES | NULL | Timestamp Laravel |

**Indexes**:
- `PRIMARY KEY (id)`
- `INDEX idx_rooms_name (name)`

**Relasi masuk**:
- `classes.room_id` → `rooms.id`

---

### 4. `classes`

**Fungsi**: Kelas konkret yang sedang/akan berjalan. Contoh dari rapor: `TEEN 4` dengan jadwal `TUESDAY AND THURSDAY` jam `17.30`. Contoh dari Excel: `Level 1`, `Manic English Camp`, `TOEFL STIKES`.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `program_id` | BIGINT UNSIGNED | NO | — | **FK** → `programs.id` |
| `instructor_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `users.id` (role=instructor) |
| `room_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `rooms.id` |
| `name` | VARCHAR(100) | NO | — | Nama kelas (TEEN 4, Level 1, Private VIP) |
| `schedule_days` | VARCHAR(50) | YES | NULL | Hari (TUESDAY AND THURSDAY) |
| `schedule_time` | VARCHAR(50) | YES | NULL | Jam (17:30 - 19:00) |
| `start_date` | DATE | YES | NULL | Tanggal mulai kelas |
| `end_date` | DATE | YES | NULL | Tanggal selesai kelas |
| `status` | ENUM | YES | `upcoming` | `upcoming`, `ongoing`, `completed`, `cancelled` |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `INDEX idx_classes_status (status)`

**Foreign Keys**:
- `program_id` → `programs(id)` `ON DELETE CASCADE`
- `instructor_id` → `users(id)` `ON DELETE SET NULL`
- `room_id` → `rooms(id)` `ON DELETE SET NULL`

**Relasi masuk**:
- `registrations.class_id` → `classes.id`
- `enrollments.class_id` → `classes.id`

---

### 5. `registrations`

**Fungsi**: Pendaftaran online calon siswa + data pembayaran (digabung). Bisa diisi tanpa akun dulu (`user_id` nullable), data calon disimpan di field `applicant_*`.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `registration_code` | VARCHAR(30) | NO | — | **UNIQUE**, contoh: `REG-2026-00001` |
| `user_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `users.id` (NULL jika belum ada akun) |
| `program_id` | BIGINT UNSIGNED | NO | — | **FK** → `programs.id` |
| `class_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `classes.id`. Diisi admin setelah placement test |
| `applicant_name` | VARCHAR(150) | NO | — | Snapshot nama calon |
| `applicant_email` | VARCHAR(150) | NO | — | Snapshot email |
| `applicant_phone` | VARCHAR(20) | NO | — | Snapshot no HP |
| `preferred_days` | ENUM | YES | NULL | `mon_wed`, `tue_thu`, `wed_fri`, `sat_sun`, `request` |
| `preferred_time` | VARCHAR(20) | YES | NULL | 09:00-10:30, 11:00-12:30, dst |
| `placement_test_at` | DATETIME | YES | NULL | Jadwal placement test luring |
| `placement_test_result` | TEXT | YES | NULL | Catatan hasil placement test |
| `payment_method` | ENUM | YES | NULL | `qris`, `bank_transfer`, `virtual_account`, `ewallet`, `manual` |
| `payment_amount` | DECIMAL(12,2) | YES | NULL | Jumlah dibayar |
| `payment_gateway_id` | VARCHAR(100) | YES | NULL | ID transaksi Midtrans/Xendit |
| `payment_proof` | VARCHAR(255) | YES | NULL | Bukti transfer (untuk metode manual) |
| `paid_at` | TIMESTAMP | YES | NULL | Waktu pembayaran berhasil |
| `status` | ENUM | NO | `pending_payment` | `pending_payment` → `paid` → `placement_test` → `enrolled` (atau `rejected`/`cancelled`) |
| `notes` | TEXT | YES | NULL | Catatan tambahan |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `UNIQUE (registration_code)`
- `INDEX idx_reg_status (status)`
- `INDEX idx_reg_email (applicant_email)`

**Foreign Keys**:
- `user_id` → `users(id)` `ON DELETE SET NULL`
- `program_id` → `programs(id)` `ON DELETE RESTRICT`
- `class_id` → `classes(id)` `ON DELETE SET NULL`

**Alur status**:
```
pending_payment → paid → placement_test → enrolled
                                     ↓
                                  rejected
```

---

### 6. `enrollments`

**Fungsi**: Junction table siswa ↔ kelas. Satu siswa bisa enrolled di banyak kelas (Teen 4 → Teen 5 → IELTS Prep). Inilah dasar **Riwayat Pembelajaran** di Dashboard Siswa.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `user_id` | BIGINT UNSIGNED | NO | — | **FK** → `users.id` (siswa) |
| `class_id` | BIGINT UNSIGNED | NO | — | **FK** → `classes.id` |
| `enrolled_at` | DATE | NO | — | Tanggal mulai ikut kelas |
| `completed_at` | DATE | YES | NULL | Tanggal selesai (NULL = masih aktif) |
| `status` | ENUM | YES | `active` | `active`, `completed`, `dropped` |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `UNIQUE (user_id, class_id)` — cegah duplikat enrollment

**Foreign Keys**:
- `user_id` → `users(id)` `ON DELETE CASCADE`
- `class_id` → `classes(id)` `ON DELETE RESTRICT`

**Relasi masuk**:
- `report_cards.enrollment_id` → `enrollments.id` (1:1)

---

### 7. `report_cards`

**Fungsi**: Rapor akhir pembelajaran. Mereplikasi 100% struktur dokumen "STUDENT EVALUATION" milik ETC: 5 Written Test (skor /20), 5 Class Assessment (grade A-D), Total Score, Next Class, Comments, plus 3 tanda tangan jabatan.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `enrollment_id` | BIGINT UNSIGNED | NO | — | **UNIQUE FK** → `enrollments.id` (1:1) |
| `score_listening` | TINYINT UNSIGNED | YES | NULL | Skor 0-20 |
| `score_vocabulary` | TINYINT UNSIGNED | YES | NULL | Skor 0-20 |
| `score_structure` | TINYINT UNSIGNED | YES | NULL | Skor 0-20 |
| `score_reading` | TINYINT UNSIGNED | YES | NULL | Skor 0-20 |
| `score_writing` | TINYINT UNSIGNED | YES | NULL | Skor 0-20 |
| `grade_pronunciation` | ENUM | YES | NULL | `A`, `B`, `C`, `D` |
| `grade_sentence_arrangement` | ENUM | YES | NULL | `A`, `B`, `C`, `D` |
| `grade_class_participation` | ENUM | YES | NULL | `A`, `B`, `C`, `D` |
| `grade_questioning_skill` | ENUM | YES | NULL | `A`, `B`, `C`, `D` |
| `grade_analyzing_skill` | ENUM | YES | NULL | `A`, `B`, `C`, `D` |
| `total_score` | TINYINT UNSIGNED | YES | NULL | Total 0-100 |
| `final_grade` | ENUM | YES | NULL | A:84-95, B:72-83, C:60-71, D:<60 |
| `next_class` | VARCHAR(100) | YES | NULL | Rekomendasi kelas berikutnya (TEEN 5) |
| `comments` | TEXT | YES | NULL | Komentar & saran instruktur |
| `instructor_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `users.id` (Talent/Instruktur kelas) |
| `academic_director_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `users.id` |
| `managing_director_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `users.id` |
| `issued_at` | DATE | YES | NULL | Tanggal rapor terbit |
| `pdf_path` | VARCHAR(255) | YES | NULL | Path PDF rapor yang sudah di-generate |
| `is_published` | TINYINT(1) | YES | 0 | 1=sudah dirilis ke siswa |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `UNIQUE (enrollment_id)`

**Foreign Keys**:
- `enrollment_id` → `enrollments(id)` `ON DELETE CASCADE`
- `instructor_id` → `users(id)` `ON DELETE SET NULL`
- `academic_director_id` → `users(id)` `ON DELETE SET NULL`
- `managing_director_id` → `users(id)` `ON DELETE SET NULL`

**Skala penilaian** (di-handle sisi aplikasi):
- A: 84-95 (Excellent)
- B: 72-83 (Very Good)
- C: 60-71 (Good)
- D: < 60 (Fail)

---

### 8. `reels`

**Fungsi**: Video pendek interaktif (fitur unggulan ETC sesuai Project Charter). `views_count` dan `likes_count` di-cache langsung di tabel ini untuk performa.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `title` | VARCHAR(200) | NO | — | Judul reel |
| `description` | TEXT | YES | NULL | Deskripsi/caption |
| `video_path` | VARCHAR(500) | NO | — | Path file video di storage/cloud |
| `thumbnail_path` | VARCHAR(255) | YES | NULL | Path thumbnail |
| `duration_seconds` | INT | YES | NULL | Durasi video dalam detik |
| `category` | ENUM | YES | `edukasi` | `promosi`, `dokumentasi`, `edukasi`, `testimoni`, `event` |
| `views_count` | INT UNSIGNED | YES | 0 | Jumlah view (counter) |
| `likes_count` | INT UNSIGNED | YES | 0 | Jumlah like (counter) |
| `is_published` | TINYINT(1) | YES | 0 | 1=tampil di website |
| `published_at` | TIMESTAMP | YES | NULL | Waktu publish |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `INDEX idx_reels_published (is_published, published_at)`

**Foreign Keys**: Tidak ada (standalone)

---

### 9. `contents`

**Fungsi**: Tabel konten CMS sederhana untuk konten yang tidak membutuhkan tabel khusus. Room tidak lagi disimpan di `contents`; room memakai tabel `rooms`.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `type` | ENUM | NO | — | `gallery`, `partner`, `profile`, `faq`, `testimonial` |
| `title` | VARCHAR(200) | NO | — | Judul/nama konten |
| `slug` | VARCHAR(220) | YES | NULL | URL-friendly bila diperlukan; tidak wajib tampil sebagai field admin awam |
| `body` | LONGTEXT | YES | NULL | Konten utama / deskripsi |
| `image` | VARCHAR(500) | YES | NULL | Gambar utama |
| `images` | JSON | YES | NULL | Multiple images (untuk gallery) |
| `meta` | JSON | YES | NULL | Field tambahan internal per tipe; form admin harus memakai label mudah dipahami |
| `display_order` | INT | YES | 0 | Urutan tampil |
| `is_published` | TINYINT(1) | YES | 1 | 1=tampil di website |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `INDEX idx_contents_type (type)`
- `INDEX idx_contents_slug (slug)`

**Kontrak form CMS per tipe**:

- `gallery`: tampilkan field `title`, `body` sebagai description/caption, dan `image`.
- `partner`: tampilkan field nama partner (`title`), deskripsi (`body`), logo (`image`), dan link/website bila ada.
- `profile`: tampilkan field visi, misi, alamat, telepon, dan informasi umum ETC Padang dengan label non-teknis.
- `faq`: tampilkan field question dan answer saja.
- `testimonial`: tampilkan nama, role/asal, pesan, rating 1-5, dan foto bila ada.
- Field teknis seperti `slug`, `meta`, dan raw JSON tidak boleh menjadi field utama untuk admin awam.

**Contoh isi `meta` per tipe**:
```json
// type=partner
{ "website": "https://partner.com" }

// type=faq
{ "question": "Bagaimana cara daftar?", "answer": "Isi form pendaftaran online." }

// type=testimonial
{ "role": "Orang tua siswa", "rating": 5 }

// type=gallery
{ "alt": "Kegiatan kelas ETC" }
```

**Foreign Keys**: Tidak ada (standalone)

---

### 10. `contact_messages`

**Fungsi**: Pesan dari form kontak pengunjung website. Alternatif digital dari konsultasi awal lewat WhatsApp/Instagram.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `name` | VARCHAR(150) | NO | — | Nama pengirim |
| `email` | VARCHAR(150) | NO | — | Email pengirim |
| `phone` | VARCHAR(20) | YES | NULL | No HP (opsional) |
| `subject` | VARCHAR(200) | YES | NULL | Subjek pesan |
| `message` | TEXT | NO | — | Isi pesan |
| `is_read` | TINYINT(1) | YES | 0 | 1=sudah dibaca admin |
| `replied_at` | TIMESTAMP | YES | NULL | Waktu admin membalas |
| `created_at` | TIMESTAMP | YES | NULL | — |
| `updated_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `INDEX idx_contact_read (is_read)`

**Foreign Keys**: Tidak ada (standalone)

---

### 11. `chatbot_logs`

**Fungsi**: Log percakapan AI Chatbot (24/7 customer service sesuai Project Charter). Satu baris = satu pasang tanya-jawab. Sesi dilacak via `session_id` (UUID) untuk pengunjung anonim.

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | **Primary Key** |
| `session_id` | VARCHAR(64) | NO | — | UUID sesi browser |
| `user_id` | BIGINT UNSIGNED | YES | NULL | **FK** → `users.id` (NULL jika anonim) |
| `user_message` | TEXT | NO | — | Pertanyaan pengguna |
| `bot_response` | TEXT | NO | — | Jawaban chatbot |
| `intent` | VARCHAR(50) | YES | NULL | `program`, `pricing`, `schedule`, `registration`, `general` |
| `is_helpful` | TINYINT(1) | YES | NULL | Feedback user (thumbs up/down) |
| `created_at` | TIMESTAMP | YES | NULL | — |

**Indexes**:
- `PRIMARY KEY (id)`
- `INDEX idx_chat_session (session_id)`
- `INDEX idx_chat_intent (intent)`

**Foreign Keys**:
- `user_id` → `users(id)` `ON DELETE SET NULL`

---

## Diagram Relasi (ERD)

Catatan: Diagram ASCII berikut adalah ringkasan relasi utama. Relasi `rooms.id` -> `classes.room_id` juga berlaku dan menjadi sumber data room/fasilitas untuk class.

```
┌─────────────────────────────────────────────────────────────────────┐
│                                                                     │
│                          ┌──────────────┐                           │
│                          │    users     │                           │
│                          │  (PK: id)    │                           │
│                          └──────┬───────┘                           │
│                                 │                                   │
│           ┌─────────────────────┼─────────────────────┐             │
│           │                     │                     │             │
│           │ instructor_id       │ user_id             │ user_id     │
│           ▼                     ▼                     ▼             │
│   ┌──────────────┐     ┌────────────────┐    ┌───────────────┐      │
│   │   classes    │◀────│ registrations  │    │  enrollments  │      │
│   │  (PK: id)    │     │   (PK: id)     │    │  (PK: id)     │      │
│   └──────┬───────┘     └────────┬───────┘    └───────┬───────┘      │
│          │                      │                    │              │
│          │ program_id           │ program_id         │ class_id     │
│          ▼                      ▼                    │              │
│   ┌──────────────┐     ┌────────────────┐            │              │
│   │   programs   │◀────┤ (FK to programs│            │              │
│   │  (PK: id)    │     └────────────────┘            │              │
│   └──────────────┘                                   │              │
│                                                      ▼              │
│                                              ┌───────────────┐      │
│                                              │ report_cards  │      │
│                                              │  (PK: id)     │      │
│                                              │ (1:1 enroll)  │      │
│                                              └───────────────┘      │
│                                                                     │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐              │
│   │    reels     │  │   contents   │  │contact_msgs  │              │
│   │ (standalone) │  │ (standalone) │  │ (standalone) │              │
│   └──────────────┘  └──────────────┘  └──────────────┘              │
│                                                                     │
│   ┌──────────────┐                                                  │
│   │ chatbot_logs │ → user_id (opsional) → users                     │
│   └──────────────┘                                                  │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

**Alur data utama** (registration → enrollment → report card):

```
Calon siswa isi form  →  registrations  →  paid  →  placement test  →  admin assign class
                                                                              │
                                                                              ▼
                                                                       enrollments (1 baris baru)
                                                                              │
                                                                              ▼
                                                                       end of class period
                                                                              │
                                                                              ▼
                                                                       report_cards (1 baris)
                                                                              │
                                                                              ▼
                                                                  Siswa download di Dashboard
```

---

## Daftar Foreign Key

Ringkasan semua relasi antar tabel:

| Tabel Asal | Kolom | Tabel Tujuan | Kolom Tujuan | ON DELETE |
|---|---|---|---|---|
| `classes` | `program_id` | `programs` | `id` | CASCADE |
| `classes` | `instructor_id` | `users` | `id` | SET NULL |
| `classes` | `room_id` | `rooms` | `id` | SET NULL |
| `registrations` | `user_id` | `users` | `id` | SET NULL |
| `registrations` | `program_id` | `programs` | `id` | RESTRICT |
| `registrations` | `class_id` | `classes` | `id` | SET NULL |
| `enrollments` | `user_id` | `users` | `id` | CASCADE |
| `enrollments` | `class_id` | `classes` | `id` | RESTRICT |
| `report_cards` | `enrollment_id` | `enrollments` | `id` | CASCADE |
| `report_cards` | `instructor_id` | `users` | `id` | SET NULL |
| `report_cards` | `academic_director_id` | `users` | `id` | SET NULL |
| `report_cards` | `managing_director_id` | `users` | `id` | SET NULL |
| `chatbot_logs` | `user_id` | `users` | `id` | SET NULL |

**Strategi `ON DELETE`**:
- **CASCADE**: Untuk relasi parent-child kuat. Jika user dihapus, enrollment-nya juga ikut dihapus.
- **RESTRICT**: Untuk data akademik/finansial yang tidak boleh hilang. Program tidak bisa dihapus jika masih ada registrasi/enrollment yang merujuknya.
- **SET NULL**: Untuk relasi opsional. Jika instruktur dihapus, kelas tetap ada tapi instructor_id jadi NULL (bisa di-assign ulang).

---

## Catatan Implementasi

### 1. Migration Order di Laravel

Urutan pembuatan migration harus mengikuti dependensi FK:

```
1. users
2. programs
3. rooms                (no dependency)
4. classes              (depends on: programs, users, rooms)
5. registrations        (depends on: users, programs, classes)
6. enrollments          (depends on: users, classes)
7. report_cards         (depends on: enrollments, users)
8. reels                (no dependency)
9. contents             (no dependency)
10. contact_messages    (no dependency)
11. chatbot_logs        (depends on: users)
```

### 2. Soft Delete

Disarankan menambahkan kolom `deleted_at TIMESTAMP NULL` dan trait `SoftDeletes` di Eloquent untuk tabel-tabel berikut, agar data historis tidak hilang:

- `users`
- `programs`
- `classes`
- `registrations`
- `enrollments`

### 3. Eloquent Relationships (Laravel)

Contoh relasi yang perlu didefinisikan:

```php
// User Model
public function enrollments() { return $this->hasMany(Enrollment::class); }
public function registrations() { return $this->hasMany(Registration::class); }
public function classesTaught() { return $this->hasMany(ClassModel::class, 'instructor_id'); }

// Program Model
public function classes() { return $this->hasMany(ClassModel::class); }
public function registrations() { return $this->hasMany(Registration::class); }

// Room Model
public function classes() { return $this->hasMany(ClassModel::class, 'room_id'); }

// Class Model
public function program() { return $this->belongsTo(Program::class); }
public function instructor() { return $this->belongsTo(User::class, 'instructor_id'); }
public function room() { return $this->belongsTo(Room::class); }
public function enrollments() { return $this->hasMany(Enrollment::class); }

// Enrollment Model
public function user() { return $this->belongsTo(User::class); }
public function class() { return $this->belongsTo(ClassModel::class); }
public function reportCard() { return $this->hasOne(ReportCard::class); }

// ReportCard Model
public function enrollment() { return $this->belongsTo(Enrollment::class); }
public function instructor() { return $this->belongsTo(User::class, 'instructor_id'); }
public function academicDirector() { return $this->belongsTo(User::class, 'academic_director_id'); }
public function managingDirector() { return $this->belongsTo(User::class, 'managing_director_id'); }
```

### 4. Field Khusus Per Role di `users`

Karena `users` menampung 3 role, gunakan **scope** atau **accessor** di model untuk akses yang lebih bersih:

```php
// Scope contoh
public function scopeStudents($query)    { return $query->where('role', 'student'); }
public function scopeInstructors($query) { return $query->where('role', 'instructor'); }
public function scopeAdmins($query)      { return $query->where('role', 'admin'); }
```

### 5. JSON Columns

Tabel `contents.meta`, `contents.images` menggunakan tipe JSON. Pastikan MySQL ≥ 5.7.8 (idealnya 8.0+). Di Eloquent, gunakan cast:

```php
protected $casts = [
    'meta' => 'array',
    'images' => 'array',
];
```

### 6. Generate Dokumen (Project Charter)

Fitur "generate dokumen" di Dashboard Admin tidak butuh tabel khusus — tinggal query langsung dari tabel yang ada:

| Dokumen | Sumber Query |
|---|---|
| Rekapan siswa terdaftar | `users WHERE role='student'` JOIN `enrollments` JOIN `classes` |
| Rekapan pendaftaran periode | `registrations WHERE created_at BETWEEN ...` |
| Riwayat pembelajaran siswa | `enrollments WHERE user_id=X` JOIN `classes` JOIN `programs` |
| Rapor akhir | `report_cards WHERE enrollment_id=X` |

### 7. Timezone

Set di `config/app.php`:
```php
'timezone' => 'Asia/Jakarta',
```

### 8. File Pendamping

- `etc_database_simple.sql` — DDL siap import: `mysql -u root -p etc_padang < etc_database_simple.sql`

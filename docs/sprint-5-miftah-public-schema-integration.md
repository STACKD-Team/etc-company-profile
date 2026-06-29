# Sprint 5 Miftah - Public Schema Integration

## Status

Implementasi Public Discovery milik Miftah telah diintegrasikan dengan schema target Sprint 5.

## Schema Minimum

- Tabel `rooms` menyediakan `name`, `description`, `capacity`, `image`, `facilities`, `is_active`, dan `display_order`.
- Model `App\Models\Room` menjadi sumber data halaman fasilitas.
- `classes.room_id` menjadi relasi nullable ke `rooms.id`.
- `contents.type` dibatasi menjadi `gallery`, `partner`, `profile`, `faq`, dan `testimonial`.

## Migrasi Data Legacy

Schema dan migrasi data memakai migration shared canonical:

- `2026_06_13_000001_create_rooms_and_align_classes.php` memindahkan room legacy dan menghubungkan class melalui `room_id`.
- `2026_06_13_000002_align_content_types_for_sprint4.php` mengubah setting/page legacy menjadi profile/FAQ lalu membatasi tipe CMS.

Tidak ada migration khusus Miftah yang membuat ulang tabel `rooms`.

## Kontrak Public

- Profile memakai `title`, `body`, `image`, dan metadata organisasi.
- Profile mendukung record terpadu dengan slug `etc-profile`, `about`, atau `company-profile`.
- Record profile per-slug seperti `address`, `vision`, dan `phone` tetap didukung dan mengoverride metadata profile terpadu.
- FAQ memakai `title` sebagai pertanyaan dan `body` sebagai jawaban.
- Testimonial memakai `title`, `body`, `image`, `meta.role`, dan `meta.rating`.
- Gallery tetap dapat tampil hanya dengan `title`, `body`, dan `image`.
- Facilities hanya membaca tabel `rooms`.
- Semua content public selain rooms hanya menampilkan record published berdasarkan `display_order`.

Halaman about, contact, FAQ, facilities, dan testimonial home memiliki empty state. Detail organisasi tidak lagi memakai alamat, telepon, atau informasi kontak fallback ketika profile belum dipublikasikan.

## Chatbot

Fallback chatbot mencari jawaban pada FAQ published. Intent kontak membaca alamat serta WhatsApp/telepon dari profile published sebelum memakai respons umum yang tidak mengarang detail organisasi.

## Batas Ownership

Implementasi ini tidak mengubah:

- Route atau middleware.
- CRUD dan resource Filament.
- Form CMS admin.
- Workflow operasional milik Mia.

## Verifikasi

Coverage khusus tersedia di `tests/Feature/MiftahSprint5PublicSchemaIntegrationTest.php`, termasuk schema, relasi room, idempotensi seeder, kompatibilitas profile, publish filtering, urutan konten, testimonial dinamis, chatbot, dan empty state.

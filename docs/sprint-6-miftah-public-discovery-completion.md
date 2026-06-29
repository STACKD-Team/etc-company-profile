# Sprint 6 Miftah Public Discovery Completion

## Scope

Sprint 6 utama membahas workflow role admin, instructor, dan student. Miftah
tetap memiliki ownership Public Discovery. Implementasi ini menutup seluruh
acceptance criteria Miftah yang masih terbuka sampai Sprint 6.

## Implementasi

- Reels menampilkan view counter dan tombol like/unlike yang terhubung ke
  endpoint ter-throttle.
- View dihitung satu kali per reel pada setiap session pengunjung.
- Reel mendukung scroll, swipe, keyboard navigation, play/pause, audio, poster,
  dan state like dari session.
- Hero memakai image profile CMS.
- Statistik kepuasan dihitung dari rating testimonial published.
- Jawaban chatbot biaya dan jadwal memakai program serta class aktif di database.
- CTA first viewport mencakup daftar, program, dan konsultasi.
- Kontak published menjadi link langsung ke WhatsApp, email, Instagram, dan map.
- Chatbot memiliki dialog semantics, live message log, Escape close, dan focus
  restoration.

## Verifikasi

Coverage khusus tersedia pada
`tests/Feature/MiftahSprint6PublicDiscoveryCompletionTest.php`. Test historis
`MiftahSprint6AdminTest.php` tetap merupakan regression area admin lama dan
bukan ownership aktif Miftah.

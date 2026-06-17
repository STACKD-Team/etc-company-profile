# Sprint 8 Miftah Final QA

## Scope

QA dan final polish ini hanya mencakup ownership Miftah pada Public Discovery:
home, about, team, facilities, gallery, contact, FAQ, chatbot public, programs,
dan reels public.

## Implementasi Final

- Reels memiliki feed vertikal full-height dengan navigasi wheel, swipe, dan
  keyboard.
- Counter view diperbarui dari endpoint terkontrol.
- Tombol like/unlike menampilkan state session, counter, loading state, live
  status untuk screen reader, dan fallback ketika request gagal.
- Rail action reels diposisikan berbeda untuk desktop, tablet, dan mobile agar
  tidak menutupi caption.
- Gallery dan partner public hanya menampilkan konten CMS published.
- Program listing/detail menampilkan cover, promo aktif, harga akhir, biaya
  pendaftaran, jadwal, instructor, syarat promo, dan CTA pendaftaran.
- Navbar, footer, dan chatbot memakai komponen reusable yang sama.
- Contact form dan chatbot tetap tervalidasi, ter-throttle, dan keyboard-ready.

## Automated QA

Coverage final tersedia di
`tests/Feature/MiftahSprint8FinalQaTest.php` dan regression suite Sprint
sebelumnya.

Smoke render headless juga dilakukan pada home dan reels dengan viewport mobile,
tablet, dan desktop. Hero, CTA, navigation, chatbot trigger, video 9:16,
caption, view counter, dan tombol like tetap terlihat tanpa horizontal overflow
atau overlap action utama.

Perintah verifikasi:

```bash
php artisan route:list
php artisan test
npm.cmd run build
```

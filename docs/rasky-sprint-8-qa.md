# Sprint 8 QA - Rasky Instructor Panel

## Scope

QA dan final polish mencakup dashboard instructor, profil, kelas, siswa, serta
workflow draft assessment. Route, schema database, publish admin, payment,
Cloudinary, Midtrans, dan RAG tidak diubah.

## Perbaikan

- Status assessment dipisahkan menjadi `not_started`, draft `incomplete`, draft
  `complete`, dan `published`.
- Rapor published tidak lagi masuk antrean assessment yang perlu dilengkapi.
- Aksi review/edit mengikuti instructor kelas saat ini dan tetap mendukung
  review oleh pembuat assessment lama setelah reassignment.
- Review assessment mengikuti istilah template ETC dan menampilkan written
  score sebagai `x/20` serta total sebagai `x/100`.
- Tampilan instructor diperkuat untuk mobile, tablet, keyboard focus, dan
  navigasi profil.
- Panel instructor tidak menampilkan NIK, NISN, alamat, atau data pembayaran.

## Acceptance Checklist

- [x] Semua route instructor memakai `auth` dan `role:instructor`.
- [x] Guest, admin, student, dan instructor yang tidak terkait ditolak.
- [x] Instructor hanya melihat kelas dan siswa yang diajar.
- [x] Draft dapat dibuat dan diperbarui hanya oleh instructor kelas saat ini.
- [x] Rapor published bersifat read-only dan publish tetap milik admin.
- [x] Skor 0-20, grade A-D, total otomatis, dan protected fields tervalidasi.
- [x] Shared UI components tetap dipakai tanpa raw dashboard form/table baru.
- [x] Tampilan menyediakan breakpoint mobile, tablet, dan desktop.
- [x] Label assessment sesuai template rapor ETC.
- [x] Route list, test project, dan production build diverifikasi.

## Verification Commands

```bash
php artisan route:list --path=instructor
php artisan test --filter=RaskyInstructor
php artisan test
npm.cmd run build
```

# Sprint 3 Miftah Source Alignment Audit

Dokumen ini mencatat implementasi dan verifikasi tanggung jawab Miftah pada
Sprint 3 berdasarkan `context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`.

## Kesimpulan

Status Sprint 3 Miftah adalah **Complete** untuk area Public Discovery.
Seluruh URI dan route name tetap, source page telah mengikuti konvensi
`resources/views/pages/public`, dan kontrak detail reels telah diselaraskan
dengan vertical feed.

Registration, payment workflow, Student Panel, Instructor Panel, dan Admin
Panel bukan ownership Miftah. Perubahan pada migration pembayaran hanya
menghilangkan deklarasi kolom duplikat yang memblokir test suite.

## Matriks Route

| Method | URI | Route name | Status |
| --- | --- | --- | --- |
| GET | `/` | `public.home` | Tetap |
| GET | `/about` | `public.about` | Tetap |
| GET | `/team` | `public.team.index` | Tetap |
| GET | `/facilities` | `public.facilities.index` | Tetap |
| GET | `/gallery` | `public.gallery.index` | Tetap |
| GET | `/contact` | `public.contact.index` | Tetap |
| POST | `/contact` | `public.contact.store` | Tetap, throttle aktif |
| GET | `/faq` | `public.faq.index` | Tetap |
| POST | `/chatbot/messages` | `public.chatbot.messages.store` | Tetap, throttle aktif |
| GET | `/programs` | `public.programs.index` | Tetap |
| GET | `/programs/{program}` | `public.programs.show` | Tetap |
| GET | `/reels` | `public.reels.index` | Tetap |
| GET | `/reels/{reel}` | `public.reels.show` | Tetap |
| POST | `/reels/{reel}/views` | `public.reels.views.store` | Tetap, throttle aktif |
| POST | `/reels/{reel}/likes` | `public.reels.likes.store` | Tetap, throttle aktif |

## Lokasi Page

| Page | View controller |
| --- | --- |
| Home | `pages.public.home` |
| About | `pages.public.about` |
| Team | `pages.public.team.index` |
| Facilities | `pages.public.facilities.index` |
| Gallery | `pages.public.gallery.index` |
| Contact | `pages.public.contact.index` |
| FAQ | `pages.public.faq.index` |
| Programs index | `pages.public.programs.index` |
| Programs show | `pages.public.programs.show` |
| Reels index | `pages.public.reels.index` |

Sepuluh page tersebut berada di `resources/views/pages/public`. Entry point
lama di `resources/views/public` telah dihapus. View registration tetap berada
di lokasi lama karena bukan ownership Miftah. Semua page tetap memakai shared
layout, navbar, footer, chatbot, dan komponen `x-ui.*`.

## Keputusan Reels

- Reel draft atau unpublished menghasilkan HTTP 404.
- Reel published pada `public.reels.show` mengalihkan ke
  `/reels?reel={id}`.
- `public.reels.index` menempatkan reel dari query `reel` sebagai item pertama
  jika reel tersebut published.
- Reels tetap memakai satu vertical feed dan tidak menambah page detail.

## Kompatibilitas Migration

- Migration pertama tetap menjadi pemilik `payment_status` dan
  `payment_expires_at`.
- Migration Sprint 2 tidak lagi menambah atau menghapus kedua kolom tersebut.
- Field unik dari kedua keluarga snapshot pembayaran tetap dipertahankan.
- Relasi `paymentPromotion` memakai `payment_promotion_id` secara eksplisit
  agar keluarga field pembayaran lama tetap dapat dibaca setelah migration.
- Service dan workflow pembayaran tidak diubah.

## Verifikasi

Verifikasi dilakukan pada 12 Juni 2026:

- `MiftahSprint3SourceAlignmentTest`: 6 test, 98 assertions, lulus.
- Seluruh test Miftah/Public Discovery: 52 test, 1.044 assertions, lulus.
- `php artisan route:list --name=public -vv --except-vendor`: 15 route, lulus.
- `npm.cmd run build`: lulus.
- `git diff --check`: lulus.
- Full `php artisan test`: 141 dari 150 test lulus, 1.885 assertions.

Sembilan kegagalan full suite yang tersisa berada di luar ownership Miftah:
test placeholder registration, ekspektasi aset/dashboard Filament, redirect
login admin, authorization area Mecca, dan variable `$formatMoney` pada view
Student Panel. Tidak ada kegagalan pada test Miftah/Public Discovery.

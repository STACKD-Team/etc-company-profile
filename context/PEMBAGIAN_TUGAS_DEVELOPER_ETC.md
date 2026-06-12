# PEMBAGIAN TUGAS DEVELOPER ETC PLANET

Dokumen ini menjadi patokan pembagian kerja developer untuk tahap penyempurnaan ETC Planet. Isi dokumen ini mengikuti blueprint project, skema database, context project aktif, dan keputusan terbaru dari tim.

Catatan nama:

- `Jamiah` pada diskusi sebelumnya adalah salah ketik dari `Mia`.
- Owner admin panel pada dokumen ini adalah `Mia`.

## 1. Tujuan Dokumen

Dokumen ini dibuat agar setiap developer punya batas kerja yang jelas, sprint yang rapi, dan standar kualitas yang sama. Fokus tahap ini bukan hanya menambah halaman, tetapi merapikan pengalaman pengguna, melengkapi integrasi penting, dan membuat panel operasional lebih siap dipakai.

Target utama:

- UI/UX lebih nyaman untuk pengguna dari semua usia.
- Form, field, selector, tabel, filter, detail page, dan CRUD panel konsisten memakai komponen Filament atau komponen reusable yang setara.
- Public discovery terasa modern dan mudah dipahami calon siswa/orang tua.
- Admin panel menjadi pusat operasional yang compact, simple, profesional, dan mudah discan.
- Student panel fokus pada informasi penting: kelas, pembayaran, riwayat belajar, dan rapor.
- Instructor panel fokus pada kelas yang diajar, siswa, dan assessment.
- Pembayaran manual dengan bukti transfer dan verifikasi admin diganti menjadi Midtrans otomatis.
- Storage file, foto, video, dokumen, dan media lain dipindahkan ke Cloudinary.
- Chatbot dikembangkan menjadi RAG chatbot dengan NVIDIA model dan Qdrant vector database.
- Admin bisa upload knowledge source untuk bahan RAG.

## 2. Sumber Acuan Wajib

Developer wajib membaca dan mengikuti file berikut sebelum mengerjakan task:

- `PROJECT_BLUEPRINT_ETC.md`
- `context/WEB_ROUTES_ETC.md`
- `context/SKEMA_DATABASE_LENGKAP.md`
- `context/FORMULIR PENDAFTARAN.jpeg`
- `context/(RAPOR AKHIR PEMBELAJARAN) SE TEEN 4.doc`
- `context/(LAPORAN REKAPAN SISWA YANG TERDAFTAR) DATA SISWA 2025.xlsx`
- `context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md`
- Referensi HTML dan screenshot di `context/stitch_etc_planet_digital_hub/`

Catatan pembagian sumber:

- `context/WEB_ROUTES_ETC.md` tetap aktif sebagai inventaris route project.
- Ownership, sprint, prioritas, dan pembagian kerja developer mengikuti dokumen pembagian tugas ini.
- Jika ada perubahan route saat mengerjakan sprint, update inventaris route di `context/WEB_ROUTES_ETC.md` dan catat dampaknya pada task/sprint terkait.

Aturan penting:

- Jangan menghapus atau mengganti file context/template contoh.
- Ownership route/modul untuk sprint penyempurnaan mengikuti dokumen ini.
- URI dan route name yang sudah ada tetap dipertahankan kecuali ada task eksplisit untuk mengubahnya.
- Roadmap route baru memakai singular English URL dengan prefix role, misalnya `/admin/student`, `/admin/report-card`, `/student/class`, dan `/instructor/report-card`. Setelah route aktual diubah, update inventaris di `context/WEB_ROUTES_ETC.md`.
- Bahasa URI dan route name tetap Bahasa Inggris.
- Teks UI boleh Bahasa Indonesia.
- Jangan membuat logic bisnis di Blade.
- Gunakan service untuk workflow kompleks seperti payment, storage, RAG indexing, document generation, dan status transition.
- Gunakan FormRequest atau validasi eksplisit.
- Jangan memakai `$request->all()` untuk mass assignment.

## 3. Ownership Area

| Developer | Area Utama | Tanggung Jawab Singkat |
| --- | --- | --- |
| Miftah | Public Discovery | Landing page, program discovery, reels public, FAQ, contact, public chatbot UI |
| Mia | Admin Panel | Filament admin resources, dashboard admin, CMS, settings, Midtrans, Cloudinary, RAG admin tooling |
| Mecca | Student Panel | Dashboard siswa, profil, kelas, pembayaran siswa, riwayat, rapor/download, bantuan siswa |
| Rasky | Instructor Panel | Dashboard instructor, kelas yang diajar, siswa instructor, assessment/report card instructor |

Pembagian ini adalah ownership kerja tahap penyempurnaan. Jika sebuah fitur menyentuh banyak area, owner utama tetap bertanggung jawab pada area miliknya dan koordinasi dilakukan melalui kontrak data/service.

## 4. Prinsip UI/UX Umum

Semua developer wajib mengikuti prinsip berikut:

- Utamakan interface yang mudah dipahami oleh siswa, orang tua, admin, dan instructor.
- Buat halaman dashboard lebih compact dan mudah discan.
- Hindari container berlapis yang membuat panel terasa berat.
- Gunakan spacing konsisten, heading ringkas, dan action yang jelas.
- Tabel harus punya search, filter, empty state, loading state, pagination, dan action yang konsisten.
- Detail entity dibuat ringkas: identitas utama di atas, status penting terlihat jelas, data tambahan dikelompokkan.
- Form panjang wajib dibagi section, step, tab, atau fieldset agar tidak melelahkan.
- Selector, date picker, upload field, textarea, toggle, checkbox, radio, badge, modal, drawer, dan table action diarahkan memakai Filament component.
- Semua state error harus menjelaskan masalah dan cara memperbaikinya.
- Mobile harus dipakai nyaman, bukan hanya "tidak rusak".
- Tipografi dan warna semua area mengikuti `context/stitch_etc_planet_digital_hub/playful_professional_identity/DESIGN.md`.
- Heading, title, label penting, dan accent text memakai Plus Jakarta Sans.
- Body text, paragraph, helper text, dan long-form content memakai Work Sans.
- Warna utama mengikuti DESIGN.md: primary/accent magenta `#e6007f`, primary token `#b90065`, warm plum charcoal/on-surface `#27171C`, light pink `#FFE6F3`, dan surface token dari design system.
- Jangan membuat palet warna baru per halaman. Jika butuh warna baru, gunakan token yang sudah ada di DESIGN.md atau status color standar yang aksesibel.
- Public page tetap mengikuti brand ETC dari DESIGN.md.
- Dashboard memakai gaya lebih utilitarian: padat, rapi, profesional, dan tidak terlalu dekoratif.

## 5. Shared UI Component System

Semua developer wajib memakai sistem komponen bersama agar tampilan admin, student, dan instructor konsisten. Komponen bersama sudah disiapkan di `resources/views/components/ui/` dan harus dipakai ulang di seluruh dashboard.

Tujuan:

- Table admin, student, dan instructor punya pola yang sama.
- Field seperti text input, select, autocomplete, date picker, file upload, toggle, checkbox, dan textarea punya perilaku yang sama.
- Modal, badge, button, panel, empty state, dan pagination tidak dibuat ulang per halaman.
- Semua table/list page wajib memakai satu wrapper `x-ui.data-table` yang sudah mencakup search, filter, sort, action toolbar, table body, empty state, dan pagination.
- Jika style komponen perlu diubah, developer cukup mengedit satu file component.
- Dashboard tidak memakai CSS component custom baru; styling memakai Tailwind utility dan komponen Filament.

### 5.1 Filament-First Component Rule

Aturan wajib:

- Jangan membuat raw `<button>`, raw `<input>`, raw `<select>`, raw modal, raw pagination, raw badge, atau raw table baru jika Filament menyediakan komponennya.
- Halaman Blade dashboard wajib memakai component project seperti `x-ui.button`, `x-ui.select`, `x-ui.badge`, `x-ui.modal`, dan sejenisnya.
- Component project di `resources/views/components/ui/` sudah memakai komponen Filament sebagai dasar.
- Admin Filament Resource boleh memakai `Filament\Forms\Components\...`, `Filament\Tables\...`, dan Filament schema langsung, tetapi schema tersebut tetap mengikuti kontrak field/table di dokumen ini.
- Student dan instructor boleh tetap memakai Blade page, tetapi field, table, action, modal, dan status badge di dalamnya wajib memakai wrapper project berbasis Filament.
- Public marketing page boleh punya layout visual khusus, tetapi form generik, action generik, dan komponen dashboard-like tetap memakai shared component jika cocok.

Contoh mapping wrapper project ke Filament:

| Project Component | Dasar Filament |
| --- | --- |
| `x-ui.button` | `<x-filament::button>` |
| `x-ui.icon-button` | `<x-filament::icon-button>` |
| `x-ui.badge` | `<x-filament::badge>` |
| `x-ui.modal` | `<x-filament::modal>` |
| `x-ui.panel` | `<x-filament::section>` atau `<x-filament::card>` |
| `x-ui.detail-card` | `x-ui.panel` |
| `x-ui.resource-header` | `x-ui.button`, `x-ui.badge`, dan Tailwind layout |
| `x-ui.description-list` | Tailwind description list |
| `x-ui.description-item` | Tailwind description item |
| `x-ui.stat-card` | `x-ui.panel`, `x-ui.badge`, dan Filament icon |
| `x-ui.action-bar` | Tailwind action layout |
| `x-ui.empty-state` | `<x-filament::empty-state>` |
| `x-ui.data-table` | `<x-filament::section>`, `<x-filament::input.wrapper>`, `<x-filament::input>`, `<x-filament::button>`, `<x-filament::empty-state>`, pagination Filament/Laravel |
| `x-ui.field` | `<x-filament::input.wrapper>` dan `<x-filament::input>` |
| `x-ui.select` | `<x-filament::input.wrapper>` dan `<x-filament::input.select>` |
| `x-ui.checkbox` | `<x-filament::input.checkbox>` |
| `x-ui.toggle` | `<x-filament::toggle>` |

### 5.2 Tailwind-First Styling Rule

Aturan styling:

- Gunakan Tailwind utility classes pada file component Blade.
- Jangan membuat file CSS baru khusus komponen dashboard.
- Jangan membuat class CSS custom seperti `.etc-table`, `.etc-field`, atau `.etc-button` jika bisa ditangani Tailwind dan Filament.
- `resources/css/app.css` hanya dipakai untuk Tailwind source/theme token dan aturan global yang benar-benar diperlukan.
- Untuk perubahan tampilan global, edit wrapper component project atau theme Filament, bukan setiap halaman.
- Gunakan design token yang sudah ada: `etc-magenta`, `etc-primary`, `etc-charcoal`, `etc-surface`, `etc-outline-variant`, `font-heading`, dan `font-body`.
- Nilai token warna dan font harus mengikuti `DESIGN.md`; jangan mengganti warna brand atau font family langsung di halaman.
- Jika ada perbedaan antara contoh lama dan `DESIGN.md`, ikuti `DESIGN.md`.

### 5.3 Komponen yang Wajib Dipakai

Field components yang wajib dipakai:

- `x-ui.field` untuk text input.
- `x-ui.email-field`.
- `x-ui.password-field`.
- `x-ui.number-field`.
- `x-ui.currency-field`.
- `x-ui.phone-field`.
- `x-ui.search-field`.
- `x-ui.date-picker`.
- `x-ui.date-time-picker`.
- `x-ui.time-picker`.
- `x-ui.select`.
- `x-ui.autocomplete`.
- `x-ui.textarea`.
- `x-ui.checkbox`.
- `x-ui.radio-group`.
- `x-ui.toggle`.
- `x-ui.file-upload`.
- `x-ui.rich-editor`.
- `x-ui.markdown-editor`.
- `x-ui.tags-input`.

General UI components yang wajib dipakai:

- `x-ui.button`.
- `x-ui.icon-button`.
- `x-ui.badge`.
- `x-ui.modal`.
- `x-ui.panel`.
- `x-ui.detail-card` untuk semua halaman detail dashboard.
- `x-ui.description-list` dan `x-ui.description-item` untuk data identitas/detail entity.
- `x-ui.resource-header` untuk header list/detail/create/edit dashboard.
- `x-ui.stat-card` untuk ringkasan angka/status di dashboard dan detail page.
- `x-ui.action-bar` untuk grouping tombol aksi.
- `x-ui.empty-state`.
- `x-ui.data-table` untuk semua table/list page dashboard.
- `x-ui.pagination` hanya untuk pagination di luar konteks table.
- `x-ui.filter-bar` hanya untuk filter non-table.

Catatan:

- Field interaktif yang butuh Livewire/Filament Resource, seperti searchable select, autocomplete kompleks, file upload async, rich editor, markdown editor, dan tags input, boleh diimplementasikan sebagai Filament Form schema pada Filament Resource/Livewire form.
- Jangan memakai raw HTML field langsung di halaman dashboard.

### 5.4 Field Contract

Semua field wrapper wajib mendukung props/behavior berikut:

- `name`.
- `label`.
- `value`.
- `placeholder`.
- `helper`.
- `required`.
- `disabled`.
- `readonly`.
- `error`.
- `old()` fallback.
- validation message konsisten.
- focus state konsisten.
- spacing konsisten.
- disabled state konsisten.

Mapping field:

- Text, email, password, number, currency, phone, dan search memakai `<x-filament::input.wrapper>` + `<x-filament::input>`.
- Select biasa memakai `<x-filament::input.wrapper>` + `<x-filament::input.select>`.
- Checkbox memakai `<x-filament::input.checkbox>`.
- Toggle memakai `<x-filament::toggle>`.
- Date picker, date-time picker, time picker, autocomplete, searchable select, file upload, rich editor, markdown editor, dan tags input memakai Filament Forms component saat berada di Filament Resource/Livewire form.
- Textarea memakai Filament textarea pattern atau input wrapper dengan textarea yang mengikuti Filament style.

### 5.5 Data Table Contract

Table admin, student, dan instructor harus konsisten.

Aturan wajib:

- Gunakan `x-ui.data-table` untuk semua table/list page Blade di admin, student, dan instructor.
- Untuk Filament Resource/Livewire table penuh, gunakan Filament Tables langsung dengan behavior yang sama.
- `x-ui.data-table` sudah mencakup search, filter slot, action toolbar, sortable header, table body, empty state, dan pagination dalam satu file component.
- Jangan memisahkan search, filter, sort, dan pagination ke wrapper lain untuk halaman table.
- `x-ui.filter-bar` dan `x-ui.pagination` tidak dipakai untuk table/list page biasa.
- Jangan membuat raw table baru di halaman dashboard.
- Semua table wajib punya pagination.
- Semua table wajib punya global search.
- Semua table wajib punya filter yang relevan.
- Semua column yang aman wajib sortable.
- Semua table wajib punya action column yang konsisten.
- Semua table wajib punya empty state.
- Semua status di table wajib memakai `x-ui.badge`.
- Pagination wajib mempertahankan query string.

Query contract untuk table server-side:

- Gunakan query param `search` untuk global search.
- Gunakan query param `sort` untuk nama column.
- Gunakan query param `direction` dengan nilai `asc` atau `desc`.
- Whitelist sortable columns di controller/service/Livewire component.
- Jangan langsung memakai nilai `sort` dari request tanpa whitelist.
- Action column, computed-only column, dan relasi kompleks boleh tidak sortable jika didokumentasikan.

### 5.6 Modal, Badge, Panel, Detail, dan Empty State Contract

Modal:

- Semua confirm delete, archive, re-index, retry payment, retry indexing, dan destructive action memakai `x-ui.modal`.
- `x-ui.modal` wajib memakai `<x-filament::modal>`.

Badge:

- Semua status memakai `x-ui.badge`.
- Warna status harus konsisten lintas admin, student, dan instructor.
- Status payment, registration, enrollment, report card, content publish, dan RAG indexing tidak boleh punya warna berbeda antar area.

Panel:

- Semua grouping dashboard memakai `x-ui.panel`.
- `x-ui.panel` wajib memakai `<x-filament::section>` atau `<x-filament::card>`.
- Hindari panel di dalam panel kecuali ada alasan interaksi yang jelas.

Detail page:

- Semua detail page admin, student, dan instructor wajib memakai `x-ui.resource-header` di bagian atas.
- Semua detail entity wajib memakai `x-ui.detail-card`.
- Data identitas, data akademik, data pembayaran, data kontak, dan data metadata ditampilkan dengan `x-ui.description-list` dan `x-ui.description-item`.
- Related records seperti histori enrollments, daftar siswa kelas, transaksi terkait, atau rapor terkait tetap memakai `x-ui.data-table`.
- Status di resource header, detail card, table, dan stat wajib memakai `x-ui.badge`.
- Tombol edit, delete, publish, export, download, dan action tambahan dikelompokkan memakai `x-ui.action-bar` berisi `x-ui.button` atau `x-ui.icon-button`.
- Destructive action wajib memakai `x-ui.modal`.

Stat card:

- Ringkasan angka/status di dashboard dan detail page memakai `x-ui.stat-card`.
- `x-ui.stat-card` tidak boleh dipakai sebagai pengganti table atau detail card.

Empty state:

- Semua halaman kosong memakai `x-ui.empty-state`.
- Empty state harus berisi judul, deskripsi pendek, dan action jika ada next step.
- `x-ui.empty-state` wajib memakai `<x-filament::empty-state>`.

### 5.7 Migration Checklist Halaman Lama ke Shared Components

Saat memigrasi halaman lama, developer wajib:

- Ganti raw table, filter form manual, search input manual, sortable header manual, action toolbar manual, empty state manual, dan pagination manual menjadi satu `x-ui.data-table`.
- Ganti raw input/select/textarea menjadi wrapper field di `x-ui.*`.
- Ganti raw button menjadi `x-ui.button` atau `x-ui.icon-button`.
- Ganti status span custom menjadi `x-ui.badge`.
- Ganti card/panel manual menjadi `x-ui.panel` jika konteksnya dashboard.
- Ganti header halaman manual menjadi `x-ui.resource-header`.
- Ganti detail card manual menjadi `x-ui.detail-card`.
- Ganti label-value detail manual menjadi `x-ui.description-list` dan `x-ui.description-item`.
- Ganti grouping tombol manual menjadi `x-ui.action-bar`.
- Ganti summary card angka/status manual menjadi `x-ui.stat-card`.
- Ganti empty state manual menjadi `x-ui.empty-state`.
- Pastikan search/filter/sort/pagination tetap bekerja.
- Pastikan `old()` value dan validation error tetap tampil.
- Jalankan `npm run build` setelah migrasi UI.

## 6. Sprint Plan

### Sprint 0 - Alignment, Audit, dan Foundation

Tujuan:

- Mengunci ownership area.
- Audit halaman yang sudah ada.
- Menentukan pola UI/UX dan komponen reusable.
- Menyiapkan kontrak environment dan service untuk integrasi besar.

Task bersama:

- Audit route aktual dengan `php artisan route:list`.
- Audit halaman yang masih memakai Blade/custom form dan perlu dipindahkan ke Filament component.
- Audit media upload yang masih memakai local/Firebase fallback.
- Audit payment flow manual yang masih memakai proof upload dan admin verify.
- Audit chatbot yang masih rule-based/FAQ biasa.
- Audit semua halaman yang masih memakai raw table, raw field, raw button, raw modal, raw badge, raw pagination, atau raw empty state.
- Buat daftar halaman prioritas per area.
- Sepakati pola naming status, badge, empty state, dan table action.

Deliverable:

- Daftar gap per area.
- Daftar route/halaman prioritas.
- Standar UI component dashboard.
- Keputusan integrasi Midtrans, Cloudinary, NVIDIA RAG, Qdrant.

### Sprint 1 - UI/UX Improvement Per Area

Tujuan:

- Merapikan pengalaman pengguna tanpa mengubah workflow besar dulu.
- Mengurangi tampilan yang terlalu penuh, membingungkan, atau tidak konsisten.

Miftah:

- Perbaiki landing page agar CTA `Daftar Sekarang` dan `Lihat Program` jelas.
- Perbaiki halaman programs agar filter dan card mudah dipahami.
- Perbaiki detail program agar informasi biaya, jadwal, target usia, dan CTA daftar cepat ditemukan.
- Tampilkan cover image program pada program card dan detail program.
- Tampilkan promo aktif pada listing/detail program dengan badge dan harga promo yang mudah dipahami.
- Tampilkan section `Kerja Sama ETC` pada public discovery jika data partner sudah dipublish dari CMS.
- Pastikan halaman public gallery menampilkan foto dari CMS `contents.type = gallery`, bukan hardcode.
- Public gallery harus responsive, mudah discan, punya empty state, dan hanya menampilkan konten gallery yang published/active.
- Perbaiki FAQ, contact, team, gallery, facilities agar konsisten brand.
- Rancang ulang reels public menjadi pengalaman vertical video.

Mia:

- Audit dashboard admin dan semua list/detail CRUD.
- Sederhanakan layout admin agar tidak terlalu banyak container.
- Buat pattern detail entity compact untuk student, registration, payment, content, reels, dan settings.
- Perbaiki status badge dan table filters di admin.
- Siapkan pattern Filament page/resource untuk sprint berikutnya.

Mecca:

- Perbaiki dashboard siswa agar langsung menunjukkan status kelas, pembayaran, dan rapor.
- Perbaiki profile siswa agar data panjang tetap mudah dibaca.
- Perbaiki kelas, learning history, payment history, dan report cards.
- Pastikan siswa/orang tua mudah memahami status pembayaran dan rapor.

Rasky:

- Perbaiki dashboard instructor agar fokus pada kelas dan action assessment.
- Perbaiki daftar kelas, detail kelas, daftar siswa, dan halaman report cards instructor.
- Pastikan instructor hanya melihat data kelas yang diajar.

Deliverable:

- UI dashboard dan public lebih konsisten.
- Empty state, loading state, error state, dan success state tersedia.
- Halaman utama nyaman di desktop dan mobile.

### Sprint 2 - Filament Component dan CRUD Standardization

Tujuan:

- Mengganti form, fields, selectors, upload field, table, filter, dan CRUD admin/panel ke pola Filament.
- Membuat pengalaman operasional lebih konsisten.
- Memigrasi halaman admin, student, dan instructor agar memakai shared component `x-ui.*` yang sudah tersedia.

Mia sebagai owner utama:

- Rapikan `AdminPanelProvider` agar brand ETC sesuai.
- Set warna primary Filament ke magenta ETC.
- Buat resource Filament untuk admin entity prioritas:
  - Programs
  - Program Promotions
  - Classes
  - Students
  - Instructors
  - Registrations
  - Payments/Transactions
  - Enrollments
  - Report Cards
  - Reels
  - Contents/CMS
  - Contact Messages
  - Chatbot Logs
  - Settings
  - Knowledge Sources
- Semua resource wajib punya:
  - search
  - filter
  - sortable safe columns
  - pagination
  - table action
  - bulk action yang aman bila diperlukan
  - badge status
  - detail/preview page
  - validation
  - authorization role admin
- Program Resource wajib mendukung upload cover image memakai Filament file upload dan Cloudinary.
- Program Resource wajib mendukung pengelolaan promo per program melalui relation/resource `Program Promotions`.
- CMS wajib punya Gallery management untuk upload satu atau banyak foto gallery memakai Filament file upload.
- Gallery item wajib mendukung title, caption/deskripsi, image/images, status publish, sort order, dan metadata lain yang diperlukan.
- CMS wajib punya bagian `Kerja Sama ETC` berbasis content type `partner`, termasuk nama instansi, logo, website/link, deskripsi, kategori/jenis kerja sama, tahun mulai, status publish, dan sort order.
- Detail siswa di admin wajib menampilkan seluruh histori kelas dari `enrollments`, termasuk program, nama kelas, instructor, tanggal mulai/selesai, status, dan link rapor jika tersedia.

Mecca dan Rasky:

- Jika student/instructor tetap memakai Blade dashboard, gunakan component project di `resources/views/components/ui/` yang berbasis Filament.
- Jika dipindahkan ke Filament panel terpisah, pastikan role guard dan route tidak konflik dengan admin.
- Semua list student/instructor wajib punya search, filter, sortable safe columns, pagination, action column, dan empty state.

Deliverable:

- Admin CRUD utama konsisten.
- Form panjang lebih mudah digunakan.
- Table entity lebih mudah dicari, difilter, dan diproses.
- Update tampilan component cukup dilakukan di file wrapper component project.

### Sprint 3 - Source Alignment dan Route/Page Convention

Tujuan:

- Mengunci flow sistem baru sebelum integrasi besar dikerjakan.
- Menyelaraskan dokumen aktif agar tidak ada konflik route, schema, ownership, dan struktur view.
- Menyiapkan kontrak implementasi halaman yang dipakai admin, instructor, dan student.

Keputusan utama:

- `context/WEB_ROUTES_ETC.md` tetap menjadi inventaris route aktual project.
- Roadmap route baru memakai singular English URL dengan prefix role:
  - Admin: `/admin/dashboard`, `/admin/student`, `/admin/instructor`, `/admin/registration`, `/admin/payment`, `/admin/placement-test`, `/admin/program`, `/admin/class`, `/admin/room`, `/admin/enrollment`, `/admin/report-card`, `/admin/reel`, `/admin/contact-message`, `/admin/chatbot-log`, `/admin/gallery`, `/admin/partner`, `/admin/testimonial`, `/admin/faq`, `/admin/profile`.
  - Instructor: `/instructor/dashboard`, `/instructor/profile`, `/instructor/class`, `/instructor/student`, `/instructor/report-card`.
  - Student: `/student/dashboard`, `/student/profile`, `/student/class`, `/student/report-card`, `/student/payment`.
- Route aktual baru boleh diubah bertahap. Setiap perubahan route harus langsung dicatat ulang di `context/WEB_ROUTES_ETC.md`.
- Untuk Sprint 3 Mia, admin CRUD/RD yang sudah berjalan di Filament tetap canonical di route aktual `/admin/...` dengan route name `filament.admin.resources.*`; route Blade lama dipertahankan di `/admin/legacy/...` sebagai compatibility layer sampai ada sprint migrasi URL khusus.
- Struktur view target:
  - Admin pages: `resources/views/pages/admin/{resource}/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`.
  - Instructor pages: `resources/views/pages/instructor/{resource}/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`.
  - Student pages: `resources/views/pages/student/{resource}/index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`.
  - Public pages: `resources/views/pages/public/...`.
- Halaman role-specific tidak boleh memakai satu file view utama bersama walaupun resource dan URL mirip.
- Shared component boleh dipakai bersama, tetapi file page utama tetap dipisah per role agar ownership dan konflik kerja lebih aman.
- Untuk admin Mia yang memakai Filament Resource, class/resource di `app/Filament/Resources` menjadi page utama saat ini; migrasi Blade lama ke `resources/views/pages/admin/...` dicatat sebagai backlog bila halaman Blade kembali dipakai sebagai canonical surface.
- Contoh struktur view role-specific:
  - `/admin/student` -> `resources/views/pages/admin/student/index.blade.php`.
  - `/admin/report-card/{reportCard}` -> `resources/views/pages/admin/report-card/show.blade.php`.
  - `/instructor/report-card/{reportCard}` -> `resources/views/pages/instructor/report-card/show.blade.php`.
  - `/student/report-card/{reportCard}` -> `resources/views/pages/student/report-card/show.blade.php`.

Deliverable:

- Dokumen roadmap, schema, dan blueprint sinkron dengan flow sistem baru.
- Route inventory tetap merepresentasikan route aktual.
- Backlog migrasi view ke folder `pages/` terdokumentasi jelas.
- Batas kerja tiap developer tetap mengikuti ownership di dokumen ini.

### Sprint 4 - Admin Operational CRUD/RD Flow

Tujuan:

- Menjadikan admin panel pusat operasional untuk semua entity utama dengan pola list, detail, create, edit, delete, dan action yang konsisten.

Scope admin:

- Dashboard: `/admin/dashboard`.
- CRUD: instructor, student, registration, placement test, program, class, room, enrollment, report card, reel, gallery, partner, testimonial, faq.
- RD: payment, contact message, chatbot log.
- Profile/settings: admin mengelola visi, misi, alamat, telepon, dan informasi umum ETC Padang.

Kontrak UX:

- Semua list page memakai datatable konsisten: global search, filter relevan, sortable safe columns, pagination, empty state, action column, dan tombol create bila resource mendukung create.
- Tombol extra memakai modal, bukan halaman terpisah, bila action bersifat pendukung. Contoh: export rekapan siswa di `/admin/student` membuka modal filter export.
- Semua detail page memiliki `x-ui.resource-header` berisi tombol edit, delete, dan tombol extra bila ada.
- Semua detail page memakai `x-ui.detail-card`.
- Semua data identitas/detail entity memakai `x-ui.description-list` dan `x-ui.description-item`.
- Jika entity punya relasi one-to-many, tampilkan related datatable di bawah detail card. Contoh: detail student menampilkan histori kelas/enrollments.
- Enrollment create tidak memakai halaman terpisah; gunakan modal dari list/detail context.
- Sidebar admin hanya menampilkan menu flow utama. CMS seperti reel, gallery, partner, testimonial, faq, dan profile dikelompokkan dalam dropdown `CMS`.

Deliverable:

- Admin punya kontrak CRUD/RD yang seragam dan bisa dikerjakan paralel.
- Shared list/detail pattern terdokumentasi sebelum implementasi page migration.
- Semua action sensitif tetap wajib policy/authorization dan FormRequest.

### Sprint 5 - Rooms, CMS Simplification, dan Schema Cleanup

Tujuan:

- Memisahkan room menjadi entity mandiri dan menyederhanakan CMS agar bisa dipakai admin awam tanpa field teknis yang membingungkan.

Database/schema target:

- Tambahkan tabel `rooms` dengan kolom `id`, `name`, `description`, `capacity`, `image`, `created_at`, `updated_at`.
- Ubah `classes.room` menjadi `classes.room_id` FK ke `rooms.id`.
- Batasi `contents.type` menjadi `gallery`, `partner`, `profile`, `faq`, `testimonial`.
- Testimonial memiliki field/form rating dengan rentang 1-5.
- Room bukan lagi `contents.type`; room dikelola melalui resource/table sendiri.

Kontrak CMS:

- FAQ form hanya menampilkan field mudah dipahami seperti `question` dan `answer`.
- Gallery form hanya menampilkan `title`, `description`, dan `image` untuk kebutuhan dasar.
- Partner form menampilkan nama partner, logo/gambar, deskripsi, dan link bila ada.
- Testimonial form menampilkan nama, role/asal, pesan, rating, dan foto bila ada.
- Profile form menampilkan visi, misi, alamat, telepon, dan informasi umum ETC Padang.
- `meta`, `slug`, dan field teknis lain tidak ditampilkan sebagai field utama ke admin awam kecuali benar-benar diperlukan dan diberi label mudah dipahami.

Deliverable:

- Kontrak schema baru terdokumentasi.
- CMS tidak lagi memaksa admin memahami struktur polymorphic teknis.
- Public facilities/room mengambil data dari `rooms`, bukan `contents.type = room`.

### Sprint 6 - Role-Specific Workflow Completion

Tujuan:

- Menyelesaikan behavior halaman admin, instructor, dan student dengan file page utama terpisah per role.
- Mencegah konflik kerja pada halaman yang resource-nya mirip seperti report-card, class, student, dan payment.

Kontrak behavior:

- Report card:
  - Admin melihat semua rapor dan bisa CRUD/publish sesuai policy.
  - Instructor melihat rapor dari kelas yang diajar dan rapor yang dia buat; bisa CRUD draft sesuai policy.
  - Student hanya melihat rapor miliknya yang sudah publish.
- Class:
  - Admin mengelola semua class.
  - Instructor hanya melihat class yang diajar.
  - Student hanya melihat class yang diikuti.
- Student:
  - Admin mengelola semua student.
  - Instructor hanya membaca student dari kelas yang diajar.
- Payment:
  - Admin membaca semua payment/registration payment.
  - Student hanya membaca payment miliknya sendiri.

Deliverable:

- Page utama dipisah per role di `resources/views/pages/admin`, `resources/views/pages/instructor`, dan `resources/views/pages/student`.
- Shared component `x-ui.*` dipakai untuk menjaga konsistensi antar role tanpa menyatukan file page utama.
- Query scope dan policy membedakan data/action per role.
- Tidak ada data student/payment/report-card yang bocor ke role yang salah.

### Sprint 7 - Integrasi Besar Setelah Flow Stabil

Tujuan:

- Melengkapi integrasi besar setelah route, schema, CMS, dan role workflow inti stabil.

Fitur besar:

- Midtrans payment gateway otomatis.
- Cloudinary storage.
- NVIDIA RAG chatbot.
- Qdrant vector database.
- Admin knowledge source upload dan indexing.
- Program cover image, gallery image, room image, partner logo, testimonial photo, reels media, dan dokumen memakai media service yang konsisten.
- Promo program memengaruhi nominal pembayaran dan snapshot harga disimpan saat registration dibuat.

Deliverable:

- Pembayaran tidak lagi memakai bukti manual sebagai flow utama.
- Media tersimpan lewat service storage yang sama.
- Chatbot mengambil konteks dari knowledge source.
- Integrasi besar tidak mengganggu flow CRUD/RD yang sudah stabil.

### Sprint 8 - Testing, QA, dan Final Polish

Tujuan:

- Memastikan semua area stabil, aman, responsive, dan siap demo.

Task bersama:

- Jalankan `php artisan route:list`.
- Jalankan `php artisan test`.
- Jalankan `npm run build`.
- Test mobile, tablet, dan desktop.
- Test role access admin/student/instructor.
- Test semua list/detail/create/edit/action utama.
- Test rooms, CMS simplified forms, report-card visibility, payment visibility, export modal, dan related datatable.
- Test integration flow jika Sprint 7 sudah diimplementasikan: Cloudinary upload, Midtrans sandbox, RAG upload/indexing/retrieval.

Deliverable:

- Semua sprint acceptance checklist terpenuhi.
- Tidak ada flow utama yang broken.
- UI nyaman, konsisten, dan mudah dipakai admin awam.

## 7. Detail Tugas Miftah - Public Discovery

Area milik Miftah:

- Public home
- About
- Team
- Facilities
- Gallery
- Contact
- FAQ
- Public chatbot UI
- Public reels
- Public program discovery UI bersama dengan route existing
- Public gallery dari CMS content yang sudah publish
- Public `Kerja Sama ETC` section dari CMS partner yang sudah publish

Route terkait:

- `public.home`
- `public.about`
- `public.team.index`
- `public.facilities.index`
- `public.gallery.index`
- `public.contact.index`
- `public.contact.store`
- `public.faq.index`
- `public.chatbot.messages.store`
- `public.reels.index`
- `public.reels.show`
- `public.reels.views.store`
- `public.reels.likes.store`
- `public.programs.index`
- `public.programs.show`

Backlog UI/UX:

- Perbaiki landing page agar value proposition ETC jelas dalam first viewport.
- CTA utama harus jelas: daftar, lihat program, tanya chatbot/kontak.
- Program cards harus menampilkan cover image, nama, kategori, target usia, durasi, harga normal, harga promo jika aktif, registration fee, dan CTA.
- Filter program harus mudah dipahami.
- Halaman detail program harus menyajikan cover image, ringkasan cepat, deskripsi, jadwal, instructor, biaya, promo aktif, syarat promo, dan CTA daftar.
- Promo aktif wajib terlihat sebagai badge/harga promo yang jelas, bukan hanya teks kecil.
- Section `Kerja Sama ETC` menampilkan logo instansi, nama, jenis kerja sama, dan link/detail jika tersedia.
- Gallery public menampilkan foto dari CMS `contents.type = gallery`, bukan data hardcode.
- Gallery public hanya menampilkan item yang published/active, punya empty state, dan tetap nyaman di mobile.
- Contact page harus memudahkan user menemukan alamat, WhatsApp, Instagram, dan map.
- FAQ harus dibuat mudah discan dan menjadi basis knowledge chatbot.
- Public chatbot UI harus ringan, jelas, dan tidak mengganggu halaman.

Backlog Reels:

- Reels public dibuat seperti Instagram/TikTok.
- Video 9:16 ditempatkan di tengah viewport.
- User bisa scroll ke atas/bawah untuk pindah reel.
- Action like/view berada di area yang mudah dijangkau.
- Detail reels tidak terasa seperti halaman artikel biasa.
- Desktop menampilkan video di tengah dengan panel metadata/action yang ringkas.
- Mobile menggunakan pengalaman full-height yang nyaman.
- View counter tetap memakai endpoint yang terkontrol.
- Like/unlike tetap memakai endpoint dan throttle yang aman.

Backlog aksesibilitas:

- CTA dan link punya label jelas.
- Video punya fallback thumbnail.
- Form contact punya validasi dan error message jelas.
- Chatbot tetap bisa digunakan dengan keyboard.

Acceptance criteria Miftah:

- Public discovery mudah dipahami calon siswa/orang tua.
- Cover image dan promo program terlihat jelas tanpa mengganggu CTA daftar.
- Public gallery menampilkan foto CMS yang publish dan tetap rapi di mobile/desktop.
- Section `Kerja Sama ETC` hanya menampilkan partner yang sudah publish/active.
- Reels terasa seperti vertical short-video app.
- Semua halaman public responsive.
- Tidak ada hardcode data bisnis jika data sudah tersedia dari DB/config.
- Tidak menduplikasi navbar/footer.

## 8. Detail Tugas Mia - Admin Panel, Integrasi, dan Operasional

Area milik Mia:

- Admin panel
- Filament admin resource
- Dashboard admin
- CMS/content
- Gallery CMS
- Program promotions
- Kerja Sama ETC/partner CMS
- Reels management
- Settings
- Contact messages
- Chatbot logs
- Registration/payment monitoring
- Midtrans payment integration
- Cloudinary storage
- RAG admin tooling
- Knowledge source uploader

Route/area terkait:

- `/admin`
- `admin.dashboard`
- `admin.registrations.*`
- `admin.payments.*`
- `admin.programs.*`
- `admin.classes.*`
- `admin.students.*`
- `admin.instructors.*`
- `admin.enrollments.*`
- `admin.report-cards.*`
- `admin.reels.*`
- `admin.contents.*`
- `admin.contact-messages.*`
- `admin.chatbot-logs.*`
- `admin.settings.*`

Backlog Admin UX:

- Jadikan admin panel sebagai pusat operasional.
- Gunakan Filament component untuk CRUD, fields, selector, upload, filter, table, badge, modal, drawer, dan action.
- Halaman list harus compact dan mudah discan.
- Halaman detail entity harus simple dan profesional.
- Pertimbangkan remove container yang tidak perlu agar interface tidak terasa berat.
- Status penting harus terlihat jelas: registration status, payment status, placement status, enrollment status, report published status, content published status, indexing status.
- Semua form upload harus punya validasi MIME/size dan feedback.

Backlog Program, Promo, Student Detail, dan CMS:

- Program admin form punya upload cover image memakai Filament file upload dan disimpan lewat Cloudinary.
- Cover image program dipakai sebagai gambar utama untuk listing/detail public, bukan sekadar thumbnail kecil.
- Admin bisa membuat, mengedit, mengaktifkan/nonaktifkan, dan menjadwalkan promo per program.
- Promo program memakai pendekatan diskon pembayaran, bukan label marketing saja.
- Promo aktif ditentukan dari status active dan periode `starts_at` sampai `ends_at`.
- Admin detail siswa menampilkan histori semua kelas dari `enrollments`.
- Histori kelas siswa menampilkan program, nama kelas, instructor, tanggal mulai, tanggal selesai, status enrollment, dan link rapor jika tersedia.
- CMS punya menu `Kerja Sama ETC` untuk mengelola instansi partner.
- Data `Kerja Sama ETC` minimal memuat nama instansi, logo, website/link, deskripsi, kategori/jenis kerja sama, tahun mulai, status publish, dan sort order.
- Logo partner wajib memakai upload field yang sama konsistennya dengan media lain.
- CMS punya menu Gallery untuk admin upload satu atau banyak foto.
- Gallery CMS memakai `contents.type = gallery`, field `image` untuk gambar utama, dan `images` JSON untuk multiple images.
- Metadata gallery seperti caption, alt text, kategori, dan sort order disimpan di `meta` atau field yang tersedia.
- Preview foto gallery wajib tersedia di admin agar admin tahu file yang sudah diupload.

### 8.1 Midtrans Payment Flow

Keputusan:

- Midtrans menggantikan flow pembayaran manual.
- Calon siswa tidak perlu upload bukti pembayaran.
- Admin tidak perlu verifikasi pembayaran manual.
- Status pembayaran berubah otomatis dari notification/callback Midtrans.

Flow baru:

```text
submit registration
-> create Midtrans transaction
-> user pays via Midtrans Snap/redirect
-> Midtrans sends notification webhook
-> app validates signature and status
-> registration becomes paid automatically
-> admin monitors payment status
```

Task Midtrans:

- Tambahkan config `config/midtrans.php`.
- Tambahkan env Midtrans di `.env.example`.
- Buat service `MidtransPaymentService`.
- Buat webhook controller untuk notification Midtrans.
- Validasi signature key Midtrans.
- Pastikan webhook idempotent.
- Simpan semua notification ke tabel audit.
- Update `registrations.status`, `paid_at`, `payment_method`, `payment_gateway_id`, dan `payment_amount` berdasarkan status Midtrans.
- Hitung nominal akhir dari promo aktif sebelum membuat transaksi Midtrans.
- Simpan snapshot original amount, discount amount, final amount, dan promo reference/title pada registration/payment.
- Mapping status Midtrans harus jelas:
  - `settlement` atau `capture` valid -> `paid`
  - `pending` -> tetap menunggu pembayaran
  - `expire` -> payment expired/cancelled sesuai kebutuhan field/status
  - `cancel`, `deny`, `failure` -> failed/rejected sesuai aturan project
- Admin payment page berubah menjadi monitoring, bukan tempat verify manual.
- Route upload bukti dan tombol verify/reject admin ditandai sebagai workflow lama, bukan workflow utama.

Tabel audit yang direncanakan:

```text
midtrans_notifications
```

Kolom minimum:

- `id`
- `registration_id` nullable FK
- `order_id`
- `transaction_id` nullable
- `payment_type` nullable
- `transaction_status`
- `fraud_status` nullable
- `status_code` nullable
- `gross_amount` nullable
- `signature_key`
- `raw_payload` JSON
- `processing_status`: `received`, `processed`, `ignored`, `failed`
- `received_at`
- `processed_at` nullable
- `error_message` nullable
- timestamps

Acceptance criteria Midtrans:

- User bisa membayar tanpa upload bukti.
- Admin tidak perlu klik verify untuk pembayaran sukses.
- Webhook duplicate tidak membuat data dobel/rusak.
- Semua payload notification tersimpan untuk audit.
- Admin bisa melihat status dan riwayat transaksi.
- Gross amount Midtrans sama dengan final amount setelah promo.
- Riwayat pembayaran menampilkan promo yang dipakai jika ada.

### 8.2 Cloudinary Storage

Keputusan:

- Cloudinary menggantikan Firebase untuk file, foto, video, dan dokumen.
- Storage lokal hanya boleh fallback development jika diperlukan.

Task Cloudinary:

- Tambahkan config `config/cloudinary.php` atau disk Cloudinary pada `config/filesystems.php`.
- Tambahkan env Cloudinary di `.env.example`.
- Update `MediaStorageService` agar upload/delete/URL memakai Cloudinary.
- Pastikan semua modul upload memakai service yang sama:
  - payment/media jika masih ada arsip
  - reels video
  - reels thumbnail
  - content image
  - gallery images dari CMS
  - program cover image/thumbnail
  - partner logo untuk `Kerja Sama ETC`
  - user avatar
  - report card document/PDF
  - RAG knowledge source file
- Simpan path/public id yang cukup untuk delete/retrieve.
- Pastikan file private/sensitif tidak dibuka publik sembarangan.

Acceptance criteria Cloudinary:

- Upload image/video/document sukses ke Cloudinary.
- Foto gallery CMS berhasil upload, replace, delete, dan preview dari Cloudinary.
- Cover image program dan logo partner tersimpan serta bisa dipreview dari Cloudinary.
- Preview URL dapat ditampilkan di UI.
- Replace file menghapus file lama jika aman.
- Delete entity membersihkan file terkait bila sesuai aturan.

### 8.3 RAG Chatbot dan Knowledge Management

Keputusan:

- Chatbot dikembangkan menjadi RAG chatbot.
- Model chat menggunakan NVIDIA endpoint:
  - base URL: `https://integrate.api.nvidia.com/v1`
  - model: `deepseek-ai/deepseek-v4-flash`
- Vector database menggunakan Qdrant.
- Admin bisa upload bahan knowledge dari admin panel.

Fitur admin:

- Buat halaman Filament `Knowledge Sources`.
- Admin bisa upload:
  - PDF
  - DOC
  - DOCX
  - TXT
  - Markdown
  - CSV
  - XLSX
  - file teks lain yang bisa diekstrak
- File disimpan ke Cloudinary sebagai arsip sumber.
- Sistem mengekstrak teks dari file.
- Teks dipecah menjadi chunk.
- Chunk dibuat embedding.
- Chunk disimpan ke Qdrant.
- Metadata chunk disimpan agar sumber bisa dilacak.
- Admin bisa publish/unpublish source.
- Admin bisa re-index source.
- Admin bisa melihat status indexing.
- Admin bisa melihat preview teks hasil ekstraksi.
- Error extraction/indexing harus jelas.

Tabel yang direncanakan:

```text
rag_knowledge_sources
rag_knowledge_chunks
```

Kolom minimum `rag_knowledge_sources`:

- `id`
- `title`
- `source_type`: `upload`, `manual`, `url`, `faq`
- `file_path` nullable
- `file_name` nullable
- `mime_type` nullable
- `file_size` nullable
- `status`: `draft`, `processing`, `ready`, `failed`, `archived`
- `is_active`
- `uploaded_by`
- `processed_at` nullable
- `error_message` nullable
- timestamps

Kolom minimum `rag_knowledge_chunks`:

- `id`
- `knowledge_source_id`
- `qdrant_point_id`
- `chunk_index`
- `content`
- `metadata` JSON
- `embedding_model`
- timestamps

Service yang direncanakan:

- `RagChatService`
- `KnowledgeSourceService`
- `TextExtractionService`
- `EmbeddingService`
- `QdrantVectorService`
- `KnowledgeIndexingJob`

Acceptance criteria RAG:

- Admin upload PDF/DOCX/TXT dan status berubah dari `processing` ke `ready`.
- Chunk tersimpan di database lokal.
- Point vector tersimpan di Qdrant.
- Admin bisa re-index.
- Chatbot mengambil konteks dari Qdrant sebelum menjawab.
- Jawaban chatbot tetap aman dan tidak mengarang ketika knowledge tidak cukup.

## 9. Detail Tugas Mecca - Student Panel

Area milik Mecca:

- Student dashboard
- Student profile
- Student classes
- Learning history
- Student payments
- Student report cards
- Student help/chatbot

Route terkait:

- `student.dashboard`
- `student.profile.show`
- `student.profile.update`
- `student.classes.index`
- `student.classes.show`
- `student.learning-history.index`
- `student.payments.index`
- `student.payments.show`
- `student.report-cards.index`
- `student.report-cards.show`
- `student.report-cards.download`
- `student.help.index`

Backlog UI/UX:

- Dashboard siswa harus menampilkan:
  - kelas aktif
  - status pembayaran terakhir
  - rapor terbaru
  - progress/riwayat belajar ringkas
  - bantuan/chatbot
- Profile siswa harus compact walaupun field banyak.
- Data sensitif ditampilkan dengan rapi dan tidak membingungkan.
- Kelas saya harus membedakan active/completed/dropped.
- Learning history harus mudah dipahami oleh siswa dan orang tua.
- Learning history wajib bersumber dari `enrollments` dan menampilkan semua histori kelas yang pernah diikuti siswa.
- Setiap item histori kelas menampilkan program, nama kelas, instructor, tanggal mulai/selesai, status, dan link rapor published jika tersedia.
- Payment history harus menampilkan status dari Midtrans.
- Payment history harus menampilkan nominal asli, potongan promo, nominal akhir, dan nama promo jika transaksi memakai promo.
- Report cards hanya menampilkan rapor yang sudah publish.
- Download rapor harus jelas dan aman.
- Help page bisa memakai chatbot UI yang konsisten dengan public chatbot.

Backlog integrasi dengan Midtrans:

- Student payment page tidak lagi meminta bukti pembayaran.
- Tampilkan status pembayaran otomatis:
  - waiting payment
  - paid
  - expired
  - failed/cancelled
- Tampilkan instruksi jika transaksi belum dibayar.
- Jika diperlukan, sediakan tombol lanjutkan pembayaran dari token/URL Midtrans yang masih valid.
- Jika pembayaran memakai promo, tampilkan snapshot promo dari registration/payment, bukan membaca ulang promo aktif saat ini.

Backlog authorization:

- Student hanya boleh melihat data miliknya sendiri.
- Student tidak boleh melihat kelas, pembayaran, enrollment, dan rapor siswa lain.
- Rapor hanya bisa didownload jika `is_published = true`.

Acceptance criteria Mecca:

- Siswa bisa memahami status belajar dan pembayaran tanpa bertanya admin.
- Siswa/orang tua bisa melihat seluruh histori kelas yang pernah diikuti.
- Siswa bisa download rapor yang sudah dipublish.
- Mobile experience nyaman untuk siswa/orang tua.
- Semua data yang ditampilkan milik siswa login.

## 10. Detail Tugas Rasky - Instructor Panel

Area milik Rasky:

- Instructor dashboard
- Instructor classes
- Instructor class detail
- Instructor students
- Instructor report cards/assessment

Route terkait:

- `instructor.dashboard`
- `instructor.classes.index`
- `instructor.classes.show`
- `instructor.students.index`
- `instructor.report-cards.index`
- `instructor.report-cards.create`
- `instructor.report-cards.store`
- `instructor.report-cards.show`
- `instructor.report-cards.edit`
- `instructor.report-cards.update`

Route assessment instructor:

- `GET /instructor/enrollments/{enrollment}/report-card/create`
- `POST /instructor/enrollments/{enrollment}/report-card`
- `GET /instructor/report-cards/{reportCard}`
- `GET /instructor/report-cards/{reportCard}/edit`
- `PUT /instructor/report-cards/{reportCard}`

Instructor hanya dapat membuat dan mengubah draft assessment untuk enrollment
dari kelas yang diajar. Rapor yang sudah dipublish bersifat read-only dan
publish/unpublish tetap menjadi kewenangan admin.

Backlog UI/UX:

- Dashboard instructor menampilkan:
  - kelas yang diajar
  - jumlah siswa
  - kelas ongoing/upcoming/completed
  - assessment/rapor yang perlu dilengkapi
- Daftar kelas harus mudah difilter berdasarkan status.
- Detail kelas menampilkan jadwal, room, program, siswa, dan status pembelajaran.
- Daftar siswa hanya berisi siswa dari kelas yang diajar.
- Tampilan siswa harus compact dan mudah discan.
- Report card/assessment page harus membantu instructor input nilai tanpa bingung.

Backlog authorization:

- Instructor hanya melihat kelas yang `instructor_id` miliknya.
- Instructor hanya melihat siswa dari kelas yang diajar.
- Instructor tidak boleh melihat data pembayaran sensitif.
- Instructor tidak boleh publish rapor jika policy menentukan publish hanya admin.

Acceptance criteria Rasky:

- Instructor punya panel yang fokus dan tidak bercampur dengan admin.
- Akses data instructor aman.
- Assessment/rapor terkait kelas bisa ditinjau dengan mudah.

## 11. Env Contract

Tambahkan variabel berikut ke `.env.example` saat implementasi integrasi dilakukan.

### NVIDIA RAG

```env
NVIDIA_API_KEY=
NVIDIA_BASE_URL=https://integrate.api.nvidia.com/v1
NVIDIA_MODEL=deepseek-ai/deepseek-v4-flash
NVIDIA_EMBEDDING_MODEL=
RAG_TOP_K=5
RAG_CHUNK_SIZE=1000
RAG_CHUNK_OVERLAP=150
```

### Qdrant

```env
QDRANT_URL=
QDRANT_API_KEY=
QDRANT_COLLECTION=etc_planet_knowledge
```

### Midtrans

```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_MERCHANT_ID=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SANITIZE=true
MIDTRANS_3DS=true
```

### Cloudinary

```env
FILESYSTEM_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_URL=
CLOUDINARY_SECURE=true
```

## 12. Database dan Migration Backlog

Migration baru yang direncanakan:

- `create_rooms_table`
- `update_classes_replace_room_with_room_id`
- `update_contents_type_for_simplified_cms`
- `create_midtrans_notifications_table`
- `create_program_promotions_table`
- `create_rag_knowledge_sources_table`
- `create_rag_knowledge_chunks_table`

Tabel promo program yang direncanakan:

```text
program_promotions
```

Kolom minimum:

- `id`
- `program_id` FK
- `title`
- `description` nullable
- `discount_type`: `percentage` atau `fixed`
- `discount_value`
- `starts_at` nullable
- `ends_at` nullable
- `is_active`
- `badge_label` nullable
- `terms` nullable
- timestamps

Perubahan/pemanfaatan yang diperlukan pada `programs`:

- Gunakan field gambar program yang tersedia, seperti `thumbnail`, sebagai cover image program.
- Jika implementasi saat ini belum punya field gambar, tambahkan field cover image/thumbnail yang menyimpan path atau Cloudinary public id.

Tabel room yang direncanakan:

```text
rooms
```

Kolom minimum:

- `id`
- `name`
- `description` nullable
- `capacity` nullable
- `image` nullable
- timestamps

Perubahan yang diperlukan pada `classes`:

- Ganti `room` text lama menjadi `room_id` nullable FK ke `rooms.id`.
- Form class memakai select room dari tabel `rooms`.
- Detail class menampilkan nama, kapasitas, dan gambar room bila ada.

Perubahan/pemanfaatan yang diperlukan pada `contents`:

- Batasi `type` menjadi `gallery`, `partner`, `profile`, `faq`, dan `testimonial`.
- Jangan pakai `contents.type = room`; room memakai tabel `rooms`.
- Gallery memakai field admin sederhana: title, description/body, image, dan publish/sort bila perlu.
- Partner memakai field admin sederhana: name/title, logo/image, description/body, website/link bila ada, dan publish/sort bila perlu.
- Profile memakai field admin sederhana: visi, misi, alamat, telepon, dan informasi umum ETC Padang.
- FAQ memakai field admin sederhana: question dan answer.
- Testimonial memakai field admin sederhana: nama, role/asal, pesan, rating 1-5, dan foto bila ada.
- Field teknis seperti `meta`, raw JSON, dan `slug` tidak menjadi field utama untuk admin awam.

Perubahan yang mungkin diperlukan pada `registrations`:

- Simpan Midtrans order ID.
- Simpan Snap token atau redirect URL bila diperlukan.
- Simpan payment status detail jika enum status utama tidak cukup.
- Simpan snapshot harga ketika registrasi dibuat:
  - original amount
  - discount amount
  - final amount
  - program promotion id/title jika promo aktif
- Pastikan tidak merusak status utama project:

```text
pending_payment -> paid -> placement_test -> enrolled
                                      -> rejected
                                      -> cancelled
```

Catatan:

- Jika perlu status payment lebih detail, gunakan kolom khusus payment status, bukan memaksakan semua variasi Midtrans ke `registrations.status`.
- Jangan menghitung ulang promo lama dari data promo aktif saat ini; gunakan snapshot pada registration/payment untuk audit.
- Jangan menghapus field payment proof sebelum semua flow lama dipastikan tidak dipakai. Tandai sebagai legacy dulu.

## 13. API, Route, dan Webhook Backlog

Route baru yang direncanakan untuk Midtrans:

- `POST /payments/midtrans/notification`
- Route name: `payments.midtrans.notification`
- Middleware: public webhook route dengan validasi signature di controller/service.

Route atau action admin RAG yang direncanakan:

- List knowledge sources.
- Create/upload knowledge source.
- Show detail knowledge source.
- Re-index knowledge source.
- Archive knowledge source.
- Delete knowledge source jika aman.

Catatan:

- Untuk admin RAG lebih disarankan memakai Filament resource/action dibanding route custom manual.
- Semua endpoint upload harus rate limited dan divalidasi.
- Webhook Midtrans tidak boleh bergantung pada session user.

## 14. Definition of Done Umum

Sebuah task dianggap selesai jika:

- Mengikuti ownership area.
- Mengikuti blueprint project dan ownership di dokumen ini.
- Tidak mengubah file context/template contoh tanpa alasan.
- Tidak memakai business logic di Blade.
- Input tervalidasi.
- Role access aman.
- UI responsive.
- Empty/error/loading/success state tersedia.
- Dashboard tidak memakai raw field, raw table, raw modal, raw badge, raw button, atau raw pagination jika shared component sudah tersedia.
- Semua shared component project memakai komponen Filament sebagai dasar.
- Styling komponen memakai Tailwind utility, bukan CSS component custom baru.
- Table dashboard punya search, filter, sortable safe columns, action column, pagination, dan empty state.
- Field dashboard mendukung label, helper, required, disabled, readonly, old value, dan validation error.
- Tidak ada data hardcode yang seharusnya dari DB/config.
- Test manual sesuai skenario sudah dilakukan.
- Jika menyentuh backend penting, test otomatis ditambahkan atau diperbarui.
- `php artisan route:list` tidak error jika route diubah.
- `php artisan test` dijalankan jika flow backend disentuh.
- `npm run build` dijalankan jika frontend asset disentuh.

## 15. QA Checklist

Checklist bersama:

- Public pages terlihat rapi di mobile dan desktop.
- Program card/detail menampilkan cover image dari data program.
- Promo aktif tampil di public program dan nominal pembayaran Midtrans memakai final amount setelah promo.
- Snapshot promo/harga tetap benar walaupun promo diubah setelah registration dibuat.
- Admin bisa upload satu atau banyak foto gallery dari CMS.
- Foto gallery tersimpan dan bisa dipreview dari Cloudinary.
- Public gallery menampilkan foto published/active dari CMS.
- Public gallery tidak menampilkan gallery draft/inactive.
- Section `Kerja Sama ETC` menampilkan partner publish beserta logo dari Cloudinary.
- Reels bisa discroll vertical dan video tetap centered.
- Registration flow masih bisa dimulai dari public page.
- Midtrans sandbox bisa membuat transaksi.
- Midtrans notification valid bisa mengubah status menjadi paid.
- Duplicate notification tidak memproses pembayaran dua kali.
- Admin payment page menjadi monitoring status, bukan manual verification utama.
- Cloudinary upload berhasil untuk image, video, dan document.
- Upload/replace/delete foto gallery berhasil lewat Cloudinary.
- Upload cover image program dan logo partner berhasil ke Cloudinary.
- RAG knowledge upload berhasil untuk PDF/DOCX/TXT.
- RAG indexing menghasilkan chunk dan Qdrant point.
- Chatbot menjawab berdasarkan knowledge yang diupload.
- Student hanya melihat pembayaran dan rapor miliknya.
- Student learning history menampilkan semua histori kelas dari `enrollments`.
- Student hanya bisa download rapor published.
- Admin detail siswa menampilkan seluruh histori kelas siswa dari `enrollments`.
- Instructor hanya melihat kelas dan siswa yang diajar.
- Admin, student, dan instructor table memakai Filament Table atau shared wrapper berbasis Filament.
- Admin, student, dan instructor table punya search/filter/sort/pagination/action column/empty state.
- Table/list page Blade memakai satu wrapper `x-ui.data-table`; search/filter/sort/pagination tidak dipisah ke component lain.
- Field seperti date picker, date-time picker, time picker, select, autocomplete, checkbox, toggle, file upload, textarea, rich editor, markdown editor, dan tags input memakai component wrapper project atau Filament Form schema.
- Badge status konsisten lintas admin, student, dan instructor.
- Modal action memakai `x-ui.modal`.
- Pagination table berasal dari `x-ui.data-table` atau Filament Table.
- Semua form panjang punya section/step yang jelas.
- Semua error upload/payment/indexing mudah dipahami.

## 16. Koordinasi Antar Developer

Aturan koordinasi:

- Jika mengubah shared layout/component, umumkan ke semua owner area.
- Jika membutuhkan field/table/modal/badge/button/pagination, gunakan component yang sudah tersedia di `resources/views/components/ui/`.
- Jika kebutuhan halaman belum tercover component yang ada, koordinasikan update shared component agar semua area tetap konsisten.
- Jika mengubah model, migration, atau service yang dipakai banyak area, buat catatan perubahan.
- Jika menambah env variable, update `.env.example` dan dokumentasikan di file ini atau dokumen integrasi.
- Jika mengubah route, catat route baru/berubah pada task atau dokumen aktif yang dipakai tim.
- Jika task menyentuh payment, koordinasi dengan Mia.
- Jika task menyentuh RAG/chatbot knowledge, koordinasi dengan Mia dan Miftah.
- Jika task menyentuh student report card display/download, koordinasi dengan Mecca.
- Jika task menyentuh instructor assessment/report cards, koordinasi dengan Rasky.

## 17. Ringkasan Beban Kerja

| Developer | Beban Utama | Output Paling Penting |
| --- | --- | --- |
| Miftah | Public discovery dan reels | Public page modern, reels vertical, chatbot public nyaman |
| Mia | Admin panel dan integrasi besar | Filament admin, Midtrans otomatis, Cloudinary, RAG uploader |
| Mecca | Student panel | Dashboard siswa, payment status, report card download |
| Rasky | Instructor panel | Dashboard instructor, kelas/siswa instructor, assessment workflow |

Dokumen ini dipakai sebagai pedoman sprint penyempurnaan. Jika ada keputusan baru, update dokumen ini agar semua developer tetap bekerja dengan acuan yang sama.

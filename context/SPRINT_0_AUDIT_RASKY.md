# Sprint 0 Audit - Rasky Instructor Panel

Dokumen ini adalah deliverable Sprint 0 untuk area Rasky berdasarkan
`context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md`. Ownership lama pada
`context/WEB_ROUTES_ETC.md` tidak dipakai untuk menentukan scope audit ini.

## Scope

Area yang diaudit:

- Instructor dashboard.
- Instructor classes dan class detail.
- Instructor students.
- Instructor report cards/assessment.

Integrasi Midtrans, Cloudinary, NVIDIA RAG, dan Qdrant berada di luar scope
implementasi Rasky. Area tersebut hanya menjadi dependency lintas tim jika
kelak memengaruhi kontrak data instructor.

## Route dan Authorization Matrix

| Priority | Route | Middleware | Data Scope | Sprint 0 Verification |
| --- | --- | --- | --- | --- |
| 1 | `instructor.dashboard` | `auth`, `role:instructor` | Agregat kelas milik instructor login | Route dan role protection tersedia; test role perlu dipertahankan |
| 2 | `instructor.classes.index` | `auth`, `role:instructor` | `classes.instructor_id = auth()->id()` | Query ter-scope; test isolasi dua instructor ditambahkan |
| 2 | `instructor.classes.show` | `auth`, `role:instructor` | Hanya kelas milik instructor login | Guard `403` tersedia; test akses silang ditambahkan |
| 3 | `instructor.students.index` | `auth`, `role:instructor` | Enrollment dari kelas instructor login | Query ter-scope; test isolasi dua instructor ditambahkan |
| 4 | `instructor.report-cards.index` | `auth`, `role:instructor` | Rapor yang terkait instructor atau kelas yang diajar | Query ter-scope; test isolasi dua instructor ditambahkan |

## Gap Matrix

| Page | Current State | Gap | Target Sprint |
| --- | --- | --- | --- |
| Dashboard | Menampilkan satu total kelas | Belum memisahkan ongoing, upcoming, completed, jumlah siswa, dan assessment yang belum lengkap | Sprint 1 |
| Classes | Raw table dan pagination Laravel | Belum memakai `x-ui.data-table`; belum ada search, filter status, safe sort, action column, badge, dan component empty state | Sprint 1-2 |
| Class detail | Menampilkan jadwal, room, program, dan status | Belum menampilkan daftar siswa dan ringkasan pembelajaran dalam tampilan instructor khusus | Sprint 1 |
| Students | Raw table dan pagination Laravel | Belum memakai `x-ui.data-table`; belum ada search, filter kelas/status, safe sort, action column, badge, dan component empty state | Sprint 1-2 |
| Report cards | Read-only raw table | Belum ada workflow input/review assessment instructor; belum memakai shared table, badge, filter, sort, dan action | Sprint 1-2 |

## UI Migration Decisions

- Semua list Blade instructor dimigrasikan ke satu wrapper `x-ui.data-table`.
- Status kelas, enrollment, dan report card memakai `x-ui.badge`.
- Empty state memakai `x-ui.empty-state` melalui wrapper table.
- Pagination table berasal dari `x-ui.data-table` dan mempertahankan query string.
- Search memakai query parameter `search`.
- Sorting memakai `sort` dan `direction`; column harus di-whitelist pada
  controller atau service.
- Filter kelas memakai status `upcoming`, `ongoing`, `completed`, atau
  `cancelled`. Filter enrollment memakai `active`, `completed`, atau `dropped`.
- Dashboard grouping berikutnya memakai `x-ui.panel`.
- Assessment input berikutnya memakai field `x-ui.*` atau Filament Form schema.

## Priority Order

1. Authorization dan isolasi data antar-instructor.
2. Dashboard instructor dan metrik operasional.
3. Daftar kelas serta detail kelas.
4. Daftar siswa dari kelas yang diajar.
5. Review dan input assessment/report card.

## Test Coverage

Sprint 0 dianggap lengkap untuk area Rasky ketika:

- Seluruh route instructor terdaftar dan memakai middleware `auth` serta
  `role:instructor`.
- Guest diarahkan ke login dan role selain instructor menerima `403`.
- Instructor hanya melihat kelas, siswa, dan rapor dari kelas yang diajar.
- Instructor menerima `403` saat membuka detail kelas instructor lain.
- Seluruh halaman instructor dapat dirender ketika dependency Filament aktif.

## Sprint 0 Decision

Foundation route, middleware, model relationship, dan shared UI component sudah
tersedia. UI instructor saat ini tetap menjadi baseline dan tidak dimigrasikan
dalam Sprint 0. Gap di atas menjadi backlog keputusan lengkap untuk Sprint 1
dan Sprint 2.

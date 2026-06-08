# Refactoring Documentation

Dokumen ini menjadi baseline pencatatan refactoring proyek ETC Planet. Pada tahap final, isi dokumen ini perlu diperbarui dengan refactoring aktual yang dilakukan selama implementasi.

## Format Pencatatan

Gunakan format berikut untuk setiap refactoring:

```markdown
## Nama Refactoring

### Sebelum
Masalah:

### Perubahan
Apa yang diubah:

### Alasan
Kenapa refactor diperlukan:

### Dampak
Hasil setelah refactor:
```

## Split Route Web per Modul

### Sebelum

Masalah:

- Route web berpotensi menumpuk jika seluruh halaman public, auth, registration, student, admin, dan instructor diletakkan dalam satu file.
- Risiko konflik lebih besar karena anggota tim mengerjakan area berbeda secara paralel.

### Perubahan

Route dipisah menjadi beberapa file:

- `routes/web/public.php`
- `routes/web/auth.php`
- `routes/web/registration.php`
- `routes/web/student.php`
- `routes/web/admin/dashboard.php`
- `routes/web/admin/registrations.php`
- `routes/web/admin/payments.php`
- `routes/web/admin/academic.php`
- `routes/web/admin/content.php`
- `routes/web/admin/reports.php`
- `routes/web/instructor.php`

`routes/web.php` menjadi loader untuk file route tersebut.

### Alasan

- Memudahkan pembagian kerja berdasarkan owner modul.
- Menjaga route lebih mudah dibaca.
- Mengurangi risiko merge conflict.

### Dampak

- Struktur route lebih modular.
- Route dapat dicek per area fitur.
- Kontrak route tetap mengacu ke `context/WEB_ROUTES_ETC.md`.

## Service Layer untuk Business Logic

### Sebelum

Masalah:

- Business logic dapat menumpuk di controller jika query, upload, status workflow, dan dokumen dikerjakan langsung di action controller.

### Perubahan

Project memakai service layer seperti:

- `RegistrationService`
- `ProgramService`
- `CourseClassService`
- `EnrollmentService`
- `ReportCardService`
- `DocumentExportService`
- `MediaStorageService`
- `AdminDashboardService`
- `BaseCrudService`

### Alasan

- Controller tetap tipis.
- Logic yang berulang dapat dipakai ulang.
- Testing service lebih mudah dilakukan.

### Dampak

- Kode lebih modular dan mudah dipelihara.
- Perubahan workflow dapat dilakukan di service tanpa memperbesar controller.

## Layout dan Component Blade

### Sebelum

Masalah:

- Header, footer, sidebar, dan layout dashboard berisiko diduplikasi di banyak Blade view.

### Perubahan

Project memakai layout dan komponen:

- `resources/views/components/layouts/public.blade.php`
- `resources/views/components/layouts/dashboard.blade.php`
- `resources/views/components/site/navbar.blade.php`
- `resources/views/components/site/footer.blade.php`
- `resources/views/components/dashboard/sidebar.blade.php`
- `resources/views/components/site/chatbot.blade.php`

### Alasan

- Tampilan public dan dashboard konsisten.
- Perubahan navbar, footer, atau sidebar cukup dilakukan di satu tempat.
- View halaman fokus pada konten utama.

### Dampak

- Duplikasi markup berkurang.
- UI lebih mudah disesuaikan dengan design system ETC Planet.

## FormRequest untuk Validasi

### Sebelum

Masalah:

- Validasi request dapat tersebar di controller dan membuat action sulit dibaca.

### Perubahan

Project memakai FormRequest untuk area public, admin, dan student, misalnya:

- `StoreRegistrationRequest`
- `StorePaymentProofRequest`
- `ConfirmRegistrationPaymentRequest`
- `StoreProgramRequest`
- `UpdateProgramRequest`
- `SaveReportCardRequest`
- `UpdateProfileRequest`

### Alasan

- Validasi input lebih terstruktur.
- Controller hanya menerima data yang sudah tervalidasi.
- Lebih aman daripada memakai `$request->all()` untuk mass assignment.

### Dampak

- Input user lebih terkontrol.
- Error validation lebih konsisten.
- Kode controller lebih ringkas.

## Catatan Final

Saat final, update dokumen ini dengan:

- Refactoring yang benar-benar dilakukan selama implementasi akhir.
- File atau modul yang terdampak.
- Bukti sebelum/sesudah jika tersedia.
- Dampak pada test atau maintainability.

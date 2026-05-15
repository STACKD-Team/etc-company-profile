# Implementasi Halaman Pilih Program ETC Planet

Dokumen ini menjelaskan langkah-langkah pembuatan halaman **Pilih Program** dengan bahasa sederhana. Tujuannya supaya kamu tidak hanya punya hasil akhir, tetapi juga mengerti kenapa file-file tertentu dibuat dan bagaimana tiap bagiannya bekerja.

## 1. Gambaran Halaman

Halaman ini adalah tahap pertama dari alur pendaftaran ETC Planet. Di desain referensi, pengguna melihat:

- header atau navbar di bagian atas,
- stepper pendaftaran berisi 4 langkah,
- daftar program belajar dalam bentuk kartu,
- ringkasan pendaftaran di sisi kanan,
- footer di bagian bawah.

Di implementasi ini, header dan footer dipisah menjadi file partial Blade. Partial artinya potongan tampilan kecil yang bisa dipakai ulang di banyak halaman.

## 2. File yang Dibuat dan Diubah

File utama:

```text
resources/views/dashboard.blade.php
```

File partial:

```text
resources/views/partials/header.blade.php
resources/views/partials/footer.blade.php
```

File CSS:

```text
public/css/pilih_program.css
```

File route:

```text
routes/web.php
```

File dokumentasi:

```text
docs/implementasi-halaman-pilih-program.md
```

## 3. Membuat Data Program di Blade

Di bagian paling atas `dashboard.blade.php`, ada blok `@php`:

```php
$programs = [
    [
        'name' => 'General English',
        'description' => 'Tingkatkan kemampuan komunikasi bahasa Inggris sehari-hari dengan metode interaktif.',
        'icon' => 'fa-solid fa-message',
        'price' => 200000,
        'checked' => true,
    ],
];
```

Blok ini berfungsi sebagai data sementara untuk daftar program. Setiap program punya:

- `name`: nama program,
- `description`: penjelasan singkat program,
- `icon`: class icon dari Font Awesome,
- `price`: biaya pendaftaran,
- `checked`: menentukan program mana yang aktif pertama kali.

Kenapa dibuat sebagai array? Karena lebih rapi daripada menulis empat kartu program secara manual satu per satu. Jika nanti ingin menambah program baru, cukup tambah data baru di array.

## 4. Memanggil Header dan Footer

Di `dashboard.blade.php`, header dipanggil seperti ini:

```blade
@include('partials.header')
```

Footer dipanggil seperti ini:

```blade
@include('partials.footer')
```

Laravel akan mencari file:

```text
resources/views/partials/header.blade.php
resources/views/partials/footer.blade.php
```

Dengan cara ini, file utama tidak terlalu panjang dan struktur halaman lebih mudah dibaca.

## 5. Membuat Header

Header dibuat di:

```text
resources/views/partials/header.blade.php
```

Isinya terdiri dari:

- brand `ETC Planet`,
- menu navigasi,
- tombol `Masuk`,
- tombol `Daftar Sekarang`.

Contoh struktur sederhananya:

```blade
<header class="site-header">
    <div class="site-header-inner">
        <a href="#" class="brand">ETC Planet</a>
        <nav class="main-nav">
            <a href="#">Beranda</a>
            <a href="#" class="is-active">Program</a>
        </nav>
    </div>
</header>
```

Class seperti `site-header`, `brand`, dan `main-nav` kemudian diatur tampilannya di CSS.

## 6. Membuat Stepper Pendaftaran

Stepper adalah bagian yang menunjukkan posisi pengguna dalam proses pendaftaran:

1. Pilih Program
2. Data Pribadi
3. Pembayaran
4. Konfirmasi

Di HTML, stepper dibuat dengan beberapa elemen `.stepper-item`. Item pertama diberi class `is-active`:

```blade
<div class="stepper-item is-active">
    <span class="stepper-number">1</span>
    <span class="stepper-label">Pilih Program</span>
</div>
```

Class `is-active` dipakai CSS untuk memberi warna pink pada langkah yang sedang aktif.

## 7. Membuat Kartu Program

Kartu program dibuat dengan perulangan:

```blade
@foreach ($programs as $program)
    <label class="program-card">
        <input type="radio" name="program">
        ...
    </label>
@endforeach
```

Setiap kartu sebenarnya adalah label untuk input radio. Ini penting karena:

- pengguna bisa memilih program dengan klik seluruh area kartu,
- pilihan tetap punya struktur form yang benar,
- lebih mudah dipakai jika nanti disambungkan ke backend.

Input radio disembunyikan secara visual dengan CSS, tetapi tetap ada di HTML. Status `checked` dari radio dipakai CSS untuk mengubah tampilan kartu yang aktif.

## 8. Menggunakan `:has()` di CSS

Di CSS ada aturan seperti ini:

```css
.program-card:has(input:checked) {
    border: 2px solid var(--pink);
}
```

Artinya: jika `.program-card` memiliki input yang sedang `checked`, maka kartu itu diberi border pink.

Dengan cara ini, kita tidak perlu menambah class aktif lewat JavaScript hanya untuk mengubah tampilan kartu.

## 9. Membuat Ringkasan Pendaftaran

Ringkasan pendaftaran dibuat di elemen:

```blade
<aside class="summary-card">
```

Bagian ini menampilkan:

- program terpilih,
- biaya pendaftaran,
- catatan biaya program,
- total sementara,
- tombol lanjut.

Nilai awal ringkasan diambil dari program yang punya `checked: true`.

## 10. Membuat Ringkasan Berubah Saat Program Dipilih

Di bagian bawah `dashboard.blade.php`, ada JavaScript kecil:

```javascript
document.querySelectorAll('input[name="program"]').forEach((radio) => {
    radio.addEventListener('change', () => {
        summaryName.textContent = radio.dataset.name;
    });
});
```

Fungsinya:

1. mencari semua input radio dengan nama `program`,
2. mendengarkan event `change`,
3. saat pengguna memilih program lain, teks ringkasan ikut berubah.

Data program disimpan di atribut `data-*`:

```blade
data-name="{{ $program['name'] }}"
data-icon="{{ $program['icon'] }}"
data-price="{{ $program['price'] }}"
```

JavaScript membaca data tersebut lewat:

```javascript
radio.dataset.name
radio.dataset.icon
radio.dataset.price
```

## 11. Mengatur Format Rupiah

Harga diformat dengan `Intl.NumberFormat`:

```javascript
const formatter = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});
```

Hasilnya dibuat menjadi format seperti:

```text
Rp 200.000
```

Ini lebih aman daripada menyusun angka secara manual.

## 12. Membuat Footer

Footer dibuat di:

```text
resources/views/partials/footer.blade.php
```

Footer berisi:

- deskripsi ETC Planet,
- daftar program,
- tautan cepat,
- kontak,
- copyright,
- pilihan bahasa.

Karena footer dipisah, halaman lain nanti cukup menulis:

```blade
@include('partials.footer')
```

## 13. Membuat CSS Utama

CSS halaman ada di:

```text
public/css/pilih_program.css
```

File ini mengatur:

- reset dasar,
- warna tema dengan CSS variable,
- header,
- layout halaman,
- stepper,
- kartu program,
- ringkasan,
- footer,
- tampilan responsif.

Contoh CSS variable:

```css
:root {
    --pink: #e6007e;
    --ink: #2b2b2f;
    --footer: #292929;
}
```

Keuntungannya, jika ingin mengganti warna utama, cukup ubah nilai `--pink` satu kali.

## 14. Membuat Tampilan Responsif

Di bagian bawah CSS ada media query:

```css
@media (max-width: 980px) {
    .registration-layout {
        grid-template-columns: 1fr;
    }
}
```

Artinya, saat layar lebih kecil dari 980px, layout yang awalnya dua kolom berubah menjadi satu kolom. Ini membuat halaman tetap nyaman dibuka di tablet atau HP.

Media query lain juga dipakai untuk:

- menyederhanakan stepper di layar kecil,
- mengubah grid program menjadi satu kolom,
- merapikan footer di mobile.

## 15. Menambahkan Route

Di `routes/web.php`, route baru ditambahkan:

```php
Route::get('/pilih-program', function () {
    return view('dashboard');
});
```

Artinya, halaman bisa dibuka lewat:

```text
/pilih-program
```

Route lama `/dashboard` tetap ada supaya halaman masih bisa dibuka dari alamat sebelumnya.

## 16. Alur Kerja Saat Halaman Dibuka

Urutannya seperti ini:

1. Browser membuka `/pilih-program` atau `/dashboard`.
2. Laravel membaca `routes/web.php`.
3. Laravel menampilkan view `dashboard`.
4. `dashboard.blade.php` memanggil partial header dan footer.
5. CSS dari `/css/pilih_program.css` mengatur tampilan.
6. Program default `General English` tampil sebagai pilihan aktif.
7. Jika pengguna memilih kartu lain, JavaScript memperbarui ringkasan.

## 17. Cara Menjalankan

Jalankan server Laravel:

```bash
php artisan serve
```

Lalu buka salah satu alamat ini di browser:

```text
http://127.0.0.1:8000/pilih-program
http://127.0.0.1:8000/dashboard
```

## 18. Catatan Pengembangan Berikutnya

Saat ini tombol `Lanjut ke Data Pribadi` masih berupa link placeholder. Untuk tahap berikutnya, tombol ini bisa diarahkan ke halaman data pribadi.

Jika nanti ingin menyimpan pilihan program ke backend, langkah umumnya:

1. ubah form agar punya `action` ke route penyimpanan,
2. tambahkan `@csrf`,
3. buat route `POST`,
4. buat controller untuk memproses pilihan,
5. simpan pilihan ke session atau database.

Dengan begitu halaman ini bisa berkembang dari tampilan statis menjadi bagian dari sistem pendaftaran yang lengkap.

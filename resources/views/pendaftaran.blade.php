<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pendaftaran Online - ETC Planet</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pendaftaran.css') }}">
    <link rel="stylesheet" href="{{ asset('css/interactive.css') }}">
</head>
<body>
<div class="page-shell">
    <nav>
        <div class="nav-logo">ETC Planet</div>
        <div class="nav-links">
            <a href="#">Beranda</a>
            <a href="#" class="nav-active">Program</a>
            <a href="#">Galeri Reels</a>
            <a href="#">Tentang Kami</a>
            <a href="#">Kontak</a>
        </div>
        <div class="nav-actions">
            <button class="btn-masuk" type="button">Masuk</button>
            <button class="btn-daftar" type="button">Daftar Sekarang</button>
        </div>
    </nav>

    <header class="page-header">
        <h1>Pendaftaran Online</h1>
        <p>Mari mulai perjalanan belajarmu bersama ETC Planet. Isi data dengan lengkap dan benar ya!</p>

        <div class="stepper" aria-label="Progress pendaftaran">
            <div class="step done">
                <div class="step-circle">&#10003;</div>
                <div class="step-label">Pilih Program</div>
            </div>
            <div class="step-line done"></div>
            <div class="step active">
                <div class="step-circle">2</div>
                <div class="step-label">Data Pribadi</div>
            </div>
            <div class="step-line"></div>
            <div class="step pending">
                <div class="step-circle">3</div>
                <div class="step-label">Pembayaran</div>
            </div>
            <div class="step-line"></div>
            <div class="step pending">
                <div class="step-circle">4</div>
                <div class="step-label">Konfirmasi</div>
            </div>
        </div>
    </header>

    <main class="registration-layout">
        <section class="form-stack">
            <article class="form-card">
                <h2 class="section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Informasi Dasar
                </h2>

                <div class="form-grid">
                    <label class="field">
                        <span>Nama Lengkap</span>
                        <input type="text" placeholder="Masukkan nama lengkap">
                    </label>
                    <label class="field">
                        <span>Email</span>
                        <input type="email" placeholder="contoh@email.com">
                    </label>
                    <label class="field">
                        <span>No. Handphone / WA</span>
                        <input type="tel" placeholder="0812xxxxxx">
                    </label>
                    <label class="field">
                        <span>Tempat Lahir</span>
                        <input type="text" placeholder="Kota Kelahiran">
                    </label>
                    <label class="field">
                        <span>Tanggal Lahir</span>
                        <input type="text" placeholder="mm/dd/yyyy">
                    </label>
                    <div class="field">
                        <span>Jenis Kelamin</span>
                        <div class="radio-group">
                            <label><input type="radio" name="jenis_kelamin" value="L"> Laki-laki</label>
                            <label><input type="radio" name="jenis_kelamin" value="P" checked> Perempuan</label>
                        </div>
                    </div>
                    <label class="field">
                        <span>Agama</span>
                        <select>
                            <option>Islam</option>
                            <option>Kristen</option>
                            <option>Katolik</option>
                            <option>Hindu</option>
                            <option>Buddha</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Pekerjaan/Sekolah/Kampus</span>
                        <input type="text" placeholder="Instansi/Pekerjaan saat ini">
                    </label>
                    <label class="field">
                        <span>NISN</span>
                        <input type="text" placeholder="Nomor Induk Siswa Nasional">
                    </label>
                    <label class="field">
                        <span>NIK</span>
                        <input type="text" placeholder="Nomor Induk Kependudukan">
                    </label>
                    <label class="field">
                        <span>Penerima KPS</span>
                        <select>
                            <option>Tidak</option>
                            <option>Ya</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>No KPS</span>
                        <input type="text" placeholder="Isi jika ya">
                    </label>
                    <label class="field">
                        <span>Layak PIP</span>
                        <select>
                            <option>Tidak</option>
                            <option>Ya</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Alasan Layak PIP</span>
                        <input type="text" placeholder="Sebutkan alasannya">
                    </label>
                    <label class="field">
                        <span>No KIP</span>
                        <input type="text" placeholder="Nomor Kartu Indonesia Pintar">
                    </label>
                </div>
            </article>

            <article class="form-card">
                <h2 class="section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Alamat
                </h2>

                <div class="form-grid">
                    <label class="field field-full">
                        <span>Alamat Lengkap</span>
                        <textarea rows="4" placeholder="Nama jalan, nomor rumah, RT/RW..."></textarea>
                    </label>
                    <label class="field">
                        <span>RT/RW</span>
                        <input type="text" placeholder="000/000">
                    </label>
                    <label class="field">
                        <span>Kode Pos</span>
                        <input type="text" placeholder="25xxx">
                    </label>
                    <label class="field">
                        <span>Provinsi</span>
                        <select name="provinsi" id="provinsi">
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Kab/Kota</span>
                        <select name="kabupaten" id="kabupaten" disabled>
                            <option value="">Pilih Kota</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Kecamatan</span>
                        <select name="kecamatan" id="kecamatan" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Kelurahan</span>
                        <select name="kelurahan" id="kelurahan" disabled>
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Tinggal Bersama</span>
                        <select>
                            <option>Orang Tua</option>
                            <option>Kos/Kontrakan</option>
                            <option>Rumah Kerabat</option>
                        </select>
                    </label>
                    <label class="field">
                        <span>Alat Transportasi</span>
                        <select>
                            <option>Kendaraan Pribadi</option>
                            <option>Kendaraan Umun</option>
                        </select>
                    </label>
                </div>
            </article>

            <div class="parent-card">
                <button class="parent-toggle" type="button" onclick="toggleSection(this)">
                    <span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Informasi Orang Tua
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="parent-panel">
                    <div class="form-grid">
                        <label class="field">
                            <span>Nama Ayah</span>
                            <input type="text" name="nama_ayah" placeholder="Masukkan nama ayah">
                        </label>
                        <label class="field">
                            <span>Nama Ibu</span>
                            <input type="text" name="nama_ibu" placeholder="Masukkan nama ibu">
                        </label>
                    </div>
                </div>
            </div>

            <div class="action-row">
                <button class="btn-kembali" type="button">Kembali</button>
                <button class="btn-lanjut" type="button" onclick="window.location.href='{{ route('pembayaran') }}'">
                    Lanjut ke Pembayaran
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </button>
            </div>
        </section>

        <aside class="summary-card">
            <h2>Ringkasan Pendaftaran</h2>

            <div class="summary-block">
                <span>Program Dipilih</span>
                <strong class="program-name">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/></svg>
                    Intensive English Preparation Course (IELTS)
                </strong>
            </div>

            <div class="summary-block">
                <span>Jadwal</span>
                <strong class="schedule">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    Senin &amp; Rabu, 16:00 WIB
                </strong>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row"><span>Biaya Pendaftaran</span><strong>Rp 200.000</strong></div>
            <div class="summary-row"><span>Biaya Program (Bulan 1)</span><strong>Rp 850.000</strong></div>

            <div class="summary-total">
                <strong>Total Pembayaran</strong>
                <span>Rp 1.050.000</span>
            </div>
        </aside>
    </main>

    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-logo">ETC Planet</div>
                <p class="footer-description">Membuka pintu dunia melalui bahasa. Kami berdedikasi untuk memberikan pengalaman belajar terbaik dengan pengajar profesional.</p>
                <p class="footer-copy">&copy; 2024 ETC Planet. Jl. S. Parman No. 202B, Padang.</p>
            </div>
            <div class="footer-col">
                <h4>Tautan Penting</h4>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat &amp; Ketentuan</a>
            </div>
            <div class="footer-col">
                <h4>Dukungan</h4>
                <a href="#">FAQ</a>
                <a href="#">Karir</a>
            </div>
        </div>
    </footer>
</div>

<script>
const wilayahApiBase = 'https://www.emsifa.com/api-wilayah-indonesia/api';

function toggleSection(el) {
    const chevron = el.querySelector('.chevron');
    const panel = el.nextElementSibling;

    chevron.classList.toggle('open');

    if (panel && panel.classList.contains('parent-panel')) {
        panel.classList.toggle('open');
    }
}

function setOptions(select, items, placeholder) {
    select.innerHTML = `<option value="">${placeholder}</option>`;

    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.name;
        select.appendChild(option);
    });
}

function resetSelect(select, placeholder) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    select.disabled = true;
}

async function fetchWilayah(path) {
    const response = await fetch(`${wilayahApiBase}/${path}`);

    if (!response.ok) {
        throw new Error('Gagal memuat data wilayah');
    }

    return response.json();
}

async function loadProvinces() {
    const provinceSelect = document.getElementById('provinsi');

    try {
        provinceSelect.disabled = true;
        setOptions(provinceSelect, await fetchWilayah('provinces.json'), 'Pilih Provinsi');
        provinceSelect.disabled = false;
    } catch (error) {
        provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const provinceSelect = document.getElementById('provinsi');
    const regencySelect = document.getElementById('kabupaten');
    const districtSelect = document.getElementById('kecamatan');
    const villageSelect = document.getElementById('kelurahan');

    loadProvinces();

    provinceSelect.addEventListener('change', async () => {
        resetSelect(regencySelect, 'Pilih Kota');
        resetSelect(districtSelect, 'Pilih Kecamatan');
        resetSelect(villageSelect, 'Pilih Kelurahan');

        if (!provinceSelect.value) return;

        try {
            setOptions(regencySelect, await fetchWilayah(`regencies/${provinceSelect.value}.json`), 'Pilih Kota');
            regencySelect.disabled = false;
        } catch (error) {
            regencySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
        }
    });

    regencySelect.addEventListener('change', async () => {
        resetSelect(districtSelect, 'Pilih Kecamatan');
        resetSelect(villageSelect, 'Pilih Kelurahan');

        if (!regencySelect.value) return;

        try {
            setOptions(districtSelect, await fetchWilayah(`districts/${regencySelect.value}.json`), 'Pilih Kecamatan');
            districtSelect.disabled = false;
        } catch (error) {
            districtSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
        }
    });

    districtSelect.addEventListener('change', async () => {
        resetSelect(villageSelect, 'Pilih Kelurahan');

        if (!districtSelect.value) return;

        try {
            setOptions(villageSelect, await fetchWilayah(`villages/${districtSelect.value}.json`), 'Pilih Kelurahan');
            villageSelect.disabled = false;
        } catch (error) {
            villageSelect.innerHTML = '<option value="">Gagal memuat kelurahan</option>';
        }
    });
});
</script>
</body>
</html>

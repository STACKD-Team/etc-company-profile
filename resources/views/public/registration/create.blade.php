<x-layouts.public title="Pendaftaran Online" navbar-active="program">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pendaftaran.css') }}">
        <link rel="stylesheet" href="{{ asset('css/interactive.css') }}">
    @endpush

@php
    $selectedProgramId = (string) old('program_id', $selectedProgram?->id);
    $programPayload = $programs->mapWithKeys(fn ($program) => [
        $program->id => [
            'name' => $program->name,
            'registration_fee' => (float) $program->registration_fee,
            'price' => (float) $program->price,
        ],
    ]);
@endphp

<div class="page-shell">
    <header class="page-header">
        <h1>Pendaftaran Online</h1>
        <p>Isi data sesuai formulir pendaftaran ETC Planet agar admin bisa memproses jadwal dan pembayaran dengan tepat.</p>

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

    @if (session('status'))
        <div class="registration-alert">{{ session('status') }}</div>
    @endif

    <form class="registration-layout" method="POST" action="{{ route('registrations.store') }}">
        @csrf

        <section class="form-stack">
            <article class="form-card">
                <h2 class="section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Informasi Dasar
                </h2>

                <div class="form-grid">
                    <label class="field">
                        <span>Nama Lengkap</span>
                        <input name="full_name" type="text" value="{{ old('full_name') }}" placeholder="Masukkan nama lengkap" required>
                        @error('full_name') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Email</span>
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="contoh@email.com" required>
                        @error('email') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>No. Handphone / WA</span>
                        <input name="mobile_phone" type="tel" value="{{ old('mobile_phone') }}" placeholder="0812xxxxxx" required>
                        @error('mobile_phone') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Tempat Lahir</span>
                        <input name="place_of_birth" type="text" value="{{ old('place_of_birth') }}" placeholder="Kota kelahiran" required>
                        @error('place_of_birth') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Tanggal Lahir</span>
                        <input name="date_of_birth" type="date" value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <div class="field">
                        <span>Jenis Kelamin</span>
                        <div class="radio-group">
                            <label><input type="radio" name="sex" value="M" @checked(old('sex') === 'M') required> Laki-laki</label>
                            <label><input type="radio" name="sex" value="F" @checked(old('sex', 'F') === 'F') required> Perempuan</label>
                        </div>
                        @error('sex') <small class="field-error">{{ $message }}</small> @enderror
                    </div>
                    <label class="field">
                        <span>Agama</span>
                        <select name="religion" required>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $religion)
                                <option value="{{ $religion }}" @selected(old('religion') === $religion)>{{ $religion }}</option>
                            @endforeach
                        </select>
                        @error('religion') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Kewarganegaraan</span>
                        <input name="nationality" type="text" value="{{ old('nationality', 'Indonesia') }}" placeholder="Kewarganegaraan" required>
                        @error('nationality') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Pekerjaan/Sekolah/Kampus</span>
                        <input name="occupation_school" type="text" value="{{ old('occupation_school') }}" placeholder="Instansi/Pekerjaan saat ini" required>
                        @error('occupation_school') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>NISN</span>
                        <input name="nisn" type="text" value="{{ old('nisn') }}" placeholder="Nomor Induk Siswa Nasional">
                        @error('nisn') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>NIK</span>
                        <input name="nik" type="text" value="{{ old('nik') }}" placeholder="Nomor Induk Kependudukan">
                        @error('nik') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Penerima KPS</span>
                        <select name="kps_receiver" required>
                            <option value="0" @selected(old('kps_receiver', '0') === '0')>Tidak</option>
                            <option value="1" @selected(old('kps_receiver') === '1')>Ya</option>
                        </select>
                        @error('kps_receiver') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>No KPS</span>
                        <input name="no_kps" type="text" value="{{ old('no_kps') }}" placeholder="Isi jika ya">
                        @error('no_kps') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Layak PIP</span>
                        <select name="worthy_of_pip" required>
                            <option value="0" @selected(old('worthy_of_pip', '0') === '0')>Tidak</option>
                            <option value="1" @selected(old('worthy_of_pip') === '1')>Ya</option>
                        </select>
                        @error('worthy_of_pip') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Alasan Layak PIP</span>
                        <input name="pip_reason" type="text" value="{{ old('pip_reason') }}" placeholder="Sebutkan alasannya">
                        @error('pip_reason') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>No KIP</span>
                        <input name="no_kip" type="text" value="{{ old('no_kip') }}" placeholder="Nomor Kartu Indonesia Pintar">
                        @error('no_kip') <small class="field-error">{{ $message }}</small> @enderror
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
                        <textarea name="address" rows="4" placeholder="Nama jalan, nomor rumah, RT/RW..." required>{{ old('address') }}</textarea>
                        @error('address') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>RT/RW</span>
                        <input name="rt_rw" type="text" value="{{ old('rt_rw') }}" placeholder="000/000">
                        @error('rt_rw') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Kode Pos</span>
                        <input name="postal_code" type="text" value="{{ old('postal_code') }}" placeholder="25xxx">
                        @error('postal_code') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Provinsi</span>
                        <input name="province" type="text" value="{{ old('province', 'Sumatera Barat') }}" placeholder="Provinsi">
                        @error('province') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Kab/Kota</span>
                        <input name="district" type="text" value="{{ old('district', 'Padang') }}" placeholder="Kabupaten/Kota">
                        @error('district') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Kecamatan</span>
                        <input name="sub_district" type="text" value="{{ old('sub_district') }}" placeholder="Kecamatan">
                        @error('sub_district') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Desa / Kelurahan</span>
                        <input name="village" type="text" value="{{ old('village') }}" placeholder="Kelurahan">
                        @error('village') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Tinggal Bersama</span>
                        <select name="living_with">
                            @foreach (['Orang Tua', 'Wali', 'Kos/Kontrakan', 'Rumah Kerabat', 'Sendiri'] as $option)
                                <option value="{{ $option }}" @selected(old('living_with') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('living_with') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Alat Transportasi</span>
                        <select name="transportation">
                            @foreach (['Kendaraan Pribadi', 'Kendaraan Umum', 'Jalan Kaki', 'Ojek Online'] as $option)
                                <option value="{{ $option }}" @selected(old('transportation') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('transportation') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </div>
            </article>

            <article class="form-card">
                <h2 class="section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Orang Tua dan Jadwal
                </h2>

                <div class="form-grid">
                    <label class="field">
                        <span>Nama Ayah</span>
                        <input name="father_name" type="text" value="{{ old('father_name') }}" placeholder="Masukkan nama ayah" required>
                        @error('father_name') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Nama Ibu</span>
                        <input name="mother_name" type="text" value="{{ old('mother_name') }}" placeholder="Masukkan nama ibu" required>
                        @error('mother_name') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Applying For</span>
                        <select name="applying_for" required>
                            @foreach ($applyingForOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('applying_for') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('applying_for') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Program Website</span>
                        <select name="program_id" id="program_id" required>
                            @forelse ($programs as $program)
                                <option value="{{ $program->id }}" @selected($selectedProgramId === (string) $program->id)>{{ $program->name }}</option>
                            @empty
                                <option value="">Belum ada program aktif</option>
                            @endforelse
                        </select>
                        @error('program_id') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Days Schedule</span>
                        <select name="preferred_days" id="preferred_days" required>
                            @foreach ($preferredDayOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('preferred_days') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('preferred_days') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="field">
                        <span>Time Schedule</span>
                        <select name="preferred_time" id="preferred_time" required>
                            @foreach ($preferredTimeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('preferred_time') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('preferred_time') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </div>
            </article>

            <div class="action-row">
                <a class="btn-kembali" href="{{ route('registrations.programs.index') }}">Kembali</a>
                <button class="btn-lanjut" type="submit" @disabled($programs->isEmpty())>
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
                    <span id="summary-program">{{ $selectedProgram?->name ?? 'Belum ada program aktif' }}</span>
                </strong>
            </div>

            <div class="summary-block">
                <span>Jadwal</span>
                <strong class="schedule">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span id="summary-schedule">Mon-Wed, 09.00-10.30</span>
                </strong>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row"><span>Biaya Pendaftaran</span><strong id="summary-registration">Rp 0</strong></div>
            <div class="summary-row"><span>Biaya Program</span><strong id="summary-program-fee">Rp 0</strong></div>

            <div class="summary-total">
                <strong>Total Pembayaran</strong>
                <span id="summary-total">Rp 0</span>
            </div>
        </aside>
    </form>
</div>

@push('scripts')
<script>
const registrationPrograms = @json($programPayload);

function rupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(value || 0).replace(/\s/g, ' ');
}

function updateRegistrationSummary() {
    const programSelect = document.getElementById('program_id');
    const daySelect = document.getElementById('preferred_days');
    const timeSelect = document.getElementById('preferred_time');
    const program = registrationPrograms[programSelect.value] || null;

    document.getElementById('summary-program').textContent = program ? program.name : 'Belum ada program aktif';
    document.getElementById('summary-schedule').textContent = `${daySelect.options[daySelect.selectedIndex]?.text || '-'}, ${timeSelect.value || '-'}`;
    document.getElementById('summary-registration').textContent = rupiah(program?.registration_fee || 0);
    document.getElementById('summary-program-fee').textContent = rupiah(program?.price || 0);
    document.getElementById('summary-total').textContent = rupiah((program?.registration_fee || 0) + (program?.price || 0));
}

document.addEventListener('DOMContentLoaded', () => {
    ['program_id', 'preferred_days', 'preferred_time'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', updateRegistrationSummary);
    });

    updateRegistrationSummary();
});
</script>
@endpush
</x-layouts.public>

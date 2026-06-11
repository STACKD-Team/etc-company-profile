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
    $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
    $livingWith = ['Orang Tua', 'Wali', 'Kos/Kontrakan', 'Rumah Kerabat', 'Sendiri'];
    $transportations = ['Kendaraan Pribadi', 'Kendaraan Umum', 'Jalan Kaki', 'Ojek Online'];
    $toOptions = static fn (array $values): array => collect($values)->mapWithKeys(fn ($value) => [$value => $value])->all();
    $yesNoOptions = ['0' => 'Tidak', '1' => 'Ya'];
    $programOptions = $programs->mapWithKeys(fn ($program) => [$program->id => $program->name])->all();
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
        <x-ui.alert status="success" title="Pendaftaran tersimpan" class="registration-alert">
            {{ session('status') }}
        </x-ui.alert>
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
                    <x-ui.field name="full_name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" required />
                    <x-ui.email-field name="email" label="Email" placeholder="contoh@email.com" required />
                    <x-ui.phone-field name="mobile_phone" label="No. Handphone / WA" placeholder="0812xxxxxx" required />
                    <x-ui.field name="place_of_birth" label="Tempat Lahir" placeholder="Kota kelahiran" required />
                    <x-ui.date-picker name="date_of_birth" label="Tanggal Lahir" required />
                    <x-ui.radio-group
                        name="sex"
                        label="Jenis Kelamin"
                        value="F"
                        :options="['M' => 'Laki-laki', 'F' => 'Perempuan']"
                        required
                    />
                    <x-ui.select name="religion" label="Agama" :options="$toOptions($religions)" required />
                    <x-ui.field name="nationality" label="Kewarganegaraan" value="Indonesia" placeholder="Kewarganegaraan" required />
                    <x-ui.field name="occupation_school" label="Pekerjaan/Sekolah/Kampus" placeholder="Instansi/Pekerjaan saat ini" required />
                    <x-ui.field name="nisn" label="NISN" placeholder="Nomor Induk Siswa Nasional" />
                    <x-ui.field name="nik" label="NIK" placeholder="Nomor Induk Kependudukan" />
                    <x-ui.radio-group name="kps_receiver" label="Penerima KPS" value="0" :options="$yesNoOptions" required />
                    <x-ui.field name="no_kps" label="No KPS" placeholder="Isi jika ya" />
                    <x-ui.radio-group name="worthy_of_pip" label="Layak PIP" value="0" :options="$yesNoOptions" required />
                    <x-ui.field name="pip_reason" label="Alasan Layak PIP" placeholder="Sebutkan alasannya" />
                    <x-ui.field name="no_kip" label="No KIP" placeholder="Nomor Kartu Indonesia Pintar" />
                </div>
            </article>

            <article class="form-card">
                <h2 class="section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Alamat
                </h2>

                <div class="form-grid">
                    <div class="field-full">
                        <x-ui.textarea name="address" label="Alamat Lengkap" placeholder="Nama jalan, nomor rumah, RT/RW..." rows="4" required />
                    </div>
                    <x-ui.field name="rt_rw" label="RT/RW" placeholder="000/000" />
                    <x-ui.field name="postal_code" label="Kode Pos" placeholder="25xxx" />
                    <x-ui.field name="province" label="Provinsi" value="Sumatera Barat" placeholder="Provinsi" />
                    <x-ui.field name="district" label="Kab/Kota" value="Padang" placeholder="Kabupaten/Kota" />
                    <x-ui.field name="sub_district" label="Kecamatan" placeholder="Kecamatan" />
                    <x-ui.field name="village" label="Desa / Kelurahan" placeholder="Kelurahan" />
                    <x-ui.select name="living_with" label="Tinggal Bersama" :options="$toOptions($livingWith)" />
                    <x-ui.select name="transportation" label="Alat Transportasi" :options="$toOptions($transportations)" />
                </div>
            </article>

            <article class="form-card">
                <h2 class="section-title">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Orang Tua dan Jadwal
                </h2>

                <div class="form-grid">
                    <x-ui.field name="father_name" label="Nama Ayah" placeholder="Masukkan nama ayah" required />
                    <x-ui.field name="mother_name" label="Nama Ibu" placeholder="Masukkan nama ibu" required />
                    <x-ui.select name="applying_for" label="Applying For" :options="$applyingForOptions" required />
                    <x-ui.select
                        name="program_id"
                        id="program_id"
                        label="Program Website"
                        :value="$selectedProgramId"
                        :options="$programOptions"
                        placeholder="Belum ada program aktif"
                        :disabled="$programs->isEmpty()"
                        required
                    />
                    <x-ui.select name="preferred_days" id="preferred_days" label="Days Schedule" :options="$preferredDayOptions" required />
                    <x-ui.select name="preferred_time" id="preferred_time" label="Time Schedule" :options="$preferredTimeOptions" required />
                </div>
            </article>

            <div class="action-row">
                <x-ui.button :href="route('registrations.programs.index')" color="gray" outlined size="xl" class="btn-kembali">
                    Kembali
                </x-ui.button>
                <x-ui.button type="submit" size="xl" class="btn-lanjut" :disabled="$programs->isEmpty()">
                    Lanjut ke Pembayaran
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </x-ui.button>
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

    if (!programSelect || !daySelect || !timeSelect) {
        return;
    }

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

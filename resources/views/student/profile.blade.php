<x-layouts.dashboard title="Profil Saya" area="student" active="profile" :user="$student">
    <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @if (session('status'))
            <x-ui.panel>
                <div class="flex items-start gap-3 text-sm font-semibold text-green-700">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    <span>{{ session('status') }}</span>
                </div>
            </x-ui.panel>
        @endif

        <x-ui.panel heading="Identitas Siswa" description="Data utama siswa yang dipakai untuk kelas dan rapor.">
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.field name="full_name" label="Nama Lengkap" :value="$student->full_name" />
                <x-ui.field name="place_of_birth" label="Tempat Lahir" :value="$student->place_of_birth" />
                <x-ui.field name="date_of_birth" label="Tanggal Lahir" type="date" :value="$student->date_of_birth?->format('Y-m-d')" />
                <x-ui.select
                    name="sex"
                    label="Jenis Kelamin"
                    :value="$student->sex"
                    placeholder="Pilih"
                    :options="['M' => 'Laki-laki', 'F' => 'Perempuan']"
                />
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Kontak dan Sekolah" description="Informasi yang membantu admin menghubungi siswa atau orang tua.">
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.phone-field name="mobile_phone" label="Nomor HP" :value="$student->mobile_phone" />
                <x-ui.field name="occupation_school" label="Sekolah/Pekerjaan" :value="$student->occupation_school" />
                <x-ui.field name="province" label="Provinsi" :value="$student->province" />
                <x-ui.field name="district" label="Kota/Kabupaten" :value="$student->district" />
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Data Keluarga" description="Data keluarga ditampilkan ringkas agar mudah diperiksa.">
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.field name="mother_name" label="Nama Ibu" :value="$student->mother_name" />
                <x-ui.field name="father_name" label="Nama Ayah" :value="$student->father_name" />
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Alamat" description="Alamat domisili siswa.">
            <x-ui.textarea name="address" label="Alamat Lengkap" :value="$student->address" rows="4" />
        </x-ui.panel>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <x-ui.button type="submit" icon="heroicon-m-check">
                Simpan Profil
            </x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>

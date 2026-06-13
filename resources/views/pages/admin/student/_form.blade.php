@csrf
@if ($student->exists) @method('PUT') @endif

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <x-ui.panel heading="Akun Siswa">
        <div class="grid gap-5 md:grid-cols-2">
            <x-ui.field name="name" label="Nama Akun" :value="$student->name" required />
            <x-ui.email-field name="email" label="Email" :value="$student->email" required />
            <x-ui.password-field name="password" label="Password" :required="! $student->exists" helper="{{ $student->exists ? 'Kosongkan jika tidak ingin mengganti password.' : null }}" />
            <x-ui.field name="no_induk" label="No Induk" :value="$student->no_induk" />
            <x-ui.field name="full_name" label="Nama Lengkap" :value="$student->full_name" />
            <x-ui.phone-field name="mobile_phone" label="No HP" :value="$student->mobile_phone" />
            <x-ui.select name="sex" label="Jenis Kelamin" :value="$student->sex" placeholder="Belum diisi" :options="['M' => 'Laki-laki', 'F' => 'Perempuan']" />
            <x-ui.field name="status" label="Status" :value="$student->status" />
            <x-ui.field name="place_of_birth" label="Tempat Lahir" :value="$student->place_of_birth" />
            <x-ui.date-picker name="date_of_birth" label="Tanggal Lahir" :value="$student->date_of_birth?->format('Y-m-d')" />
            <x-ui.field name="occupation_school" label="Sekolah/Pekerjaan" :value="$student->occupation_school" />
            <x-ui.checkbox name="is_active" label="Aktif" :checked="(bool) ($student->is_active ?? true)" />
            <div class="md:col-span-2">
                <x-ui.textarea name="address" label="Alamat" rows="3" :value="$student->address" />
            </div>
            <x-ui.field name="father_name" label="Nama Ayah" :value="$student->father_name" />
            <x-ui.field name="mother_name" label="Nama Ibu" :value="$student->mother_name" />
        </div>
    </x-ui.panel>

    <x-ui.panel heading="Aksi">
        <x-ui.button type="submit" icon="heroicon-m-check" class="w-full">Simpan Siswa</x-ui.button>
        <x-ui.button :href="$student->exists ? route('admin.student.show', $student) : route('admin.student.index')" outlined color="gray" class="mt-3 w-full">Batal</x-ui.button>
    </x-ui.panel>
</div>

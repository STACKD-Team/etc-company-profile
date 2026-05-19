<x-layouts.dashboard title="Profil Saya" area="student" active="profile" :user="$student">
    <form method="POST" action="{{ route('student.profile.update') }}" class="rounded-card bg-white p-6 shadow-panel">
        @csrf
        @method('PUT')

        @if (session('status'))
            <div class="mb-5 rounded-card bg-green-50 p-4 text-sm font-semibold text-green-700">{{ session('status') }}</div>
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            @foreach ([
                'full_name' => 'Nama Lengkap',
                'mobile_phone' => 'Nomor HP',
                'place_of_birth' => 'Tempat Lahir',
                'date_of_birth' => 'Tanggal Lahir',
                'occupation_school' => 'Sekolah/Pekerjaan',
                'province' => 'Provinsi',
                'district' => 'Kota/Kabupaten',
                'mother_name' => 'Nama Ibu',
                'father_name' => 'Nama Ayah',
            ] as $field => $label)
                <label class="block">
                    <span class="font-heading text-sm font-bold text-etc-on-surface">{{ $label }}</span>
                    <input name="{{ $field }}" type="{{ $field === 'date_of_birth' ? 'date' : 'text' }}" value="{{ old($field, optional($student->{$field})->format('Y-m-d') ?? $student->{$field}) }}" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm outline-none focus:border-etc-magenta">
                    @error($field)<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
                </label>
            @endforeach

            <label class="block">
                <span class="font-heading text-sm font-bold text-etc-on-surface">Jenis Kelamin</span>
                <select name="sex" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm outline-none focus:border-etc-magenta">
                    <option value="">Pilih</option>
                    <option value="M" @selected(old('sex', $student->sex) === 'M')>Laki-laki</option>
                    <option value="F" @selected(old('sex', $student->sex) === 'F')>Perempuan</option>
                </select>
            </label>

            <label class="block md:col-span-2">
                <span class="font-heading text-sm font-bold text-etc-on-surface">Alamat</span>
                <textarea name="address" rows="4" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm outline-none focus:border-etc-magenta">{{ old('address', $student->address) }}</textarea>
                @error('address')<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
        </div>

        <button class="mt-6 rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white hover:bg-etc-primary">Simpan Profil</button>
    </form>
</x-layouts.dashboard>

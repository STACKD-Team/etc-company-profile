@csrf
@if ($program->exists) @method('PUT') @endif
<div class="grid gap-5 md:grid-cols-2">
    @foreach (['name' => 'Nama', 'slug' => 'Slug', 'duration_meetings' => 'Jumlah Pertemuan', 'max_students' => 'Maks Siswa', 'price' => 'Harga Program', 'registration_fee' => 'Biaya Pendaftaran'] as $field => $label)
        <label><span class="font-heading text-sm font-bold">{{ $label }}</span><input name="{{ $field }}" value="{{ old($field, $program->{$field}) }}" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@error($field)<span class="text-xs text-red-600">{{ $message }}</span>@enderror</label>
    @endforeach
    <label><span class="font-heading text-sm font-bold">Kategori</span><select name="category" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@foreach (['english','mandarin','japanese','test_prep','soft_skills','other'] as $option)<option value="{{ $option }}" @selected(old('category', $program->category ?: 'english') === $option)>{{ $option }}</option>@endforeach</select></label>
    <label><span class="font-heading text-sm font-bold">Tipe</span><select name="type" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@foreach (['regular','private','one_on_one'] as $option)<option value="{{ $option }}" @selected(old('type', $program->type ?: 'regular') === $option)>{{ $option }}</option>@endforeach</select></label>
    <label><span class="font-heading text-sm font-bold">Target Usia</span><select name="target_age" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">@foreach (['all','kids','teen','adult','university'] as $option)<option value="{{ $option }}" @selected(old('target_age', $program->target_age ?: 'all') === $option)>{{ $option }}</option>@endforeach</select></label>
    <label class="flex items-center gap-2 pt-8"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active ?? true))><span class="font-heading text-sm font-bold">Aktif</span></label>
    <label class="md:col-span-2"><span class="font-heading text-sm font-bold">Deskripsi</span><textarea name="description" rows="4" class="mt-2 w-full rounded-xl border border-etc-outline-variant px-4 py-3 text-sm">{{ old('description', $program->description) }}</textarea></label>
</div>
<button class="mt-6 rounded-pill bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white">Simpan</button>

@csrf
@if ($program->exists) @method('PUT') @endif

@php
    $categories = ['english' => 'English', 'mandarin' => 'Mandarin', 'japanese' => 'Japanese', 'test_prep' => 'Test Prep', 'soft_skills' => 'Soft Skills', 'other' => 'Other'];
    $types = ['regular' => 'Regular', 'private' => 'Private', 'one_on_one' => 'One on One'];
    $targetAges = ['all' => 'All', 'kids' => 'Kids', 'teen' => 'Teen', 'adult' => 'Adult', 'university' => 'University'];
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-ui.field name="name" label="Nama" :value="$program->name" required />
    <x-ui.field name="slug" label="Slug" :value="$program->slug" required />
    <x-ui.number-field name="duration_meetings" label="Jumlah Pertemuan" :value="$program->duration_meetings" min="1" />
    <x-ui.number-field name="max_students" label="Maks Siswa" :value="$program->max_students" min="1" />
    <x-ui.currency-field name="price" label="Harga Program" :value="$program->price" required />
    <x-ui.currency-field name="registration_fee" label="Biaya Pendaftaran" :value="$program->registration_fee" required />
    <x-ui.select name="category" label="Kategori" :value="$program->category ?: 'english'" :options="$categories" required />
    <x-ui.select name="type" label="Tipe" :value="$program->type ?: 'regular'" :options="$types" required />
    <x-ui.select name="target_age" label="Target Usia" :value="$program->target_age ?: 'all'" :options="$targetAges" />
    <div class="md:col-span-2">
        <x-ui.checkbox name="is_active" label="Aktif" helper="Program aktif bisa ditampilkan dan dipilih calon siswa." :checked="(bool) ($program->is_active ?? true)" />
    </div>
    <div class="md:col-span-2">
        <x-ui.textarea name="description" label="Deskripsi" rows="4" :value="$program->description" />
    </div>
</div>
<div class="mt-6 flex flex-wrap gap-3">
    <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
    <x-ui.button :href="route('admin.programs.index')" outlined color="gray">Batal</x-ui.button>
</div>

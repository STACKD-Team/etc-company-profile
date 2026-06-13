@csrf
@if ($instructor->exists) @method('PUT') @endif

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <x-ui.panel heading="Akun Instructor">
        <div class="grid gap-5 md:grid-cols-2">
            <x-ui.field name="name" label="Nama Akun" :value="$instructor->name" required />
            <x-ui.email-field name="email" label="Email" :value="$instructor->email" required />
            <x-ui.password-field name="password" label="Password" :required="! $instructor->exists" helper="{{ $instructor->exists ? 'Kosongkan jika tidak ingin mengganti password.' : null }}" />
            <x-ui.field name="full_name" label="Nama Lengkap" :value="$instructor->full_name" />
            <x-ui.phone-field name="mobile_phone" label="No HP" :value="$instructor->mobile_phone" />
            <x-ui.field name="instructor_position" label="Posisi" :value="$instructor->instructor_position" />
            <x-ui.field name="instructor_specialization" label="Spesialisasi" :value="$instructor->instructor_specialization" />
            <x-ui.checkbox name="is_active" label="Aktif" :checked="(bool) ($instructor->is_active ?? true)" />
            <x-ui.checkbox name="show_on_team_page" label="Tampil di Team" :checked="(bool) ($instructor->show_on_team_page ?? true)" />
            <div class="md:col-span-2">
                <x-ui.textarea name="instructor_bio" label="Bio" rows="4" :value="$instructor->instructor_bio" />
            </div>
        </div>
    </x-ui.panel>

    <x-ui.panel heading="Aksi">
        <x-ui.button type="submit" icon="heroicon-m-check" class="w-full">Simpan Instructor</x-ui.button>
        <x-ui.button :href="$instructor->exists ? route('admin.instructor.show', $instructor) : route('admin.instructor.index')" outlined color="gray" class="mt-3 w-full">Batal</x-ui.button>
    </x-ui.panel>
</div>

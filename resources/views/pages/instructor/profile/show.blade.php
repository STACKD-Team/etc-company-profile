<x-layouts.dashboard
    title="Profil Instructor"
    description="Perbarui identitas profesional yang dipakai pada panel instructor."
    area="instructor"
    active="profile"
    :user="$instructor"
>
    <x-slot:eyebrow>Instructor Workspace</x-slot:eyebrow>

    @if (session('status'))
        <x-ui.alert status="success" title="Profil tersimpan" class="mb-6">
            {{ session('status') }}
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('instructor.profile.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <x-ui.panel
            heading="Identitas Instructor"
            description="Email dan status akun dikelola oleh admin."
            icon="heroicon-o-user-circle"
        >
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.field name="full_name" label="Nama Lengkap" :value="$instructor->full_name" maxlength="150" />
                <x-ui.field name="email" label="Email" :value="$instructor->email" disabled readonly />
                <x-ui.phone-field name="mobile_phone" label="Nomor HP" :value="$instructor->mobile_phone" maxlength="20" />
                <x-ui.field
                    name="instructor_position"
                    label="Jabatan"
                    :value="$instructor->instructor_position"
                    maxlength="100"
                    placeholder="Contoh: English Instructor"
                />
                <x-ui.field
                    name="instructor_specialization"
                    label="Spesialisasi"
                    :value="$instructor->instructor_specialization"
                    maxlength="100"
                    placeholder="Contoh: English Conversation"
                />
            </div>
        </x-ui.panel>

        <x-ui.panel
            heading="Bio Profesional"
            description="Ringkasan pengalaman dan fokus pengajaran instructor."
            icon="heroicon-o-identification"
        >
            <x-ui.textarea
                name="instructor_bio"
                label="Bio Instructor"
                :value="$instructor->instructor_bio"
                :rows="6"
                maxlength="2000"
                placeholder="Tuliskan pengalaman, pendekatan mengajar, dan bidang keahlian."
            />
        </x-ui.panel>

        <div class="flex justify-end">
            <x-ui.button type="submit" icon="heroicon-m-check">
                Simpan Profil
            </x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>

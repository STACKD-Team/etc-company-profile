<x-layouts.dashboard title="Edit Pendaftaran" area="admin" active="registrations">
    <x-ui.panel heading="Data Pendaftaran" description="Perbarui koreksi data intake tanpa mengubah workflow pembayaran manual Sprint 1.">
        <form method="POST" action="{{ route('admin.registration.update', $registration) }}" class="grid gap-5 md:grid-cols-2">
            @include('pages.admin.registration._form-fields')

            <div class="flex flex-wrap gap-3 md:col-span-2">
                <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
                <x-ui.button :href="route('admin.registration.show', $registration)" outlined color="gray">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

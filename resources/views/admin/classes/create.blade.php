<x-layouts.dashboard title="Tambah Kelas" area="admin" active="classes">
    <x-ui.panel heading="Kelas Baru" description="Buat kelas konkret yang akan dipakai di placement, enrollment, dan rapor.">
        <form method="POST" action="{{ route('admin.classes.store') }}">
            @include('admin.classes._form')
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

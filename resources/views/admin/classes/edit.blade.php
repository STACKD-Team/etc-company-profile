<x-layouts.dashboard title="Edit Kelas" area="admin" active="classes">
    <x-ui.panel heading="Edit Kelas" description="Perbarui jadwal, ruangan, instructor, dan status kelas.">
        <form method="POST" action="{{ route('admin.classes.update', $class) }}">
            @include('admin.classes._form')
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

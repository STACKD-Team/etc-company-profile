<x-layouts.dashboard title="Tambah Program" area="admin" active="programs">
    <x-ui.panel heading="Program Baru" description="Lengkapi master program untuk public discovery dan flow pendaftaran.">
        <form method="POST" action="{{ route('admin.programs.store') }}">
            @include('admin.programs._form')
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

<x-layouts.dashboard title="Edit Program" area="admin" active="programs">
    <x-ui.panel heading="Edit Program" description="Perbarui data program tanpa mengubah kontrak route atau workflow Sprint 1.">
        <form method="POST" action="{{ route('admin.program.update', $program) }}">
            @include('pages.admin.program._form')
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

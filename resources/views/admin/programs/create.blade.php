<x-layouts.dashboard title="Tambah Program" area="admin" active="programs">
    <form method="POST" action="{{ route('admin.programs.store') }}" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.programs._form')
    </form>
</x-layouts.dashboard>

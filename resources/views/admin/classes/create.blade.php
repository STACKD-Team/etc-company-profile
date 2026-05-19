<x-layouts.dashboard title="Tambah Kelas" area="admin" active="classes">
    <form method="POST" action="{{ route('admin.classes.store') }}" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.classes._form')
    </form>
</x-layouts.dashboard>

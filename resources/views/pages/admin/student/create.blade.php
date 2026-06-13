<x-layouts.dashboard title="Tambah Siswa" area="admin" active="students">
    <form method="POST" action="{{ route('admin.student.store') }}">
        @include('pages.admin.student._form')
    </form>
</x-layouts.dashboard>

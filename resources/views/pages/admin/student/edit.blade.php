<x-layouts.dashboard title="Edit Siswa" area="admin" active="students">
    <form method="POST" action="{{ route('admin.student.update', $student) }}">
        @include('pages.admin.student._form')
    </form>
</x-layouts.dashboard>

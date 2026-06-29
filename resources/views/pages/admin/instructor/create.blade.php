<x-layouts.dashboard title="Tambah Instructor" area="admin" active="instructors">
    <form method="POST" action="{{ route('admin.instructor.store') }}">
        @include('pages.admin.instructor._form')
    </form>
</x-layouts.dashboard>

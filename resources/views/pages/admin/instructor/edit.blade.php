<x-layouts.dashboard title="Edit Instructor" area="admin" active="instructors">
    <form method="POST" action="{{ route('admin.instructor.update', $instructor) }}">
        @include('pages.admin.instructor._form')
    </form>
</x-layouts.dashboard>

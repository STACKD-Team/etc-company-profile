<x-layouts.dashboard title="Edit Room" area="admin" active="rooms">
    <form method="POST" action="{{ route('admin.room.update', $room) }}" enctype="multipart/form-data">
        @include('pages.admin.room._form')
    </form>
</x-layouts.dashboard>

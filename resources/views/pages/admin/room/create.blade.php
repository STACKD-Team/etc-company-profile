<x-layouts.dashboard title="Tambah Room" area="admin" active="rooms">
    <form method="POST" action="{{ route('admin.room.store') }}" enctype="multipart/form-data">
        @include('pages.admin.room._form')
    </form>
</x-layouts.dashboard>

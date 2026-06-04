<x-layouts.dashboard title="Tambah Reel" area="admin" active="reels">
    <form method="POST" action="{{ route('admin.reels.store') }}" enctype="multipart/form-data" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.reels._form')
    </form>
</x-layouts.dashboard>

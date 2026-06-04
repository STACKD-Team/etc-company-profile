<x-layouts.dashboard title="Edit Reel" area="admin" active="reels">
    <form method="POST" action="{{ route('admin.reels.update', $reel) }}" enctype="multipart/form-data" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.reels._form')
    </form>
</x-layouts.dashboard>

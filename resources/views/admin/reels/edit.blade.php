<x-layouts.dashboard title="Edit Reel" area="admin" active="reels">
    <form method="POST" action="{{ route('admin.reels.update', $reel) }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        @include('admin.reels._form')
    </form>
</x-layouts.dashboard>

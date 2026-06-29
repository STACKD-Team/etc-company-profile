<x-layouts.dashboard title="Tambah Reel" area="admin" active="reels">
    <form method="POST" action="{{ route('admin.reel.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        @include('pages.admin.reel._form')
    </form>
</x-layouts.dashboard>

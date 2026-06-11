<x-layouts.dashboard title="Tambah Konten" area="admin" active="contents">
    <form method="POST" action="{{ route('admin.contents.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
        @include('admin.contents._form')
    </form>
</x-layouts.dashboard>

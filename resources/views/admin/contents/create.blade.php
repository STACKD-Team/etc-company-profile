<x-layouts.dashboard title="Tambah Konten" area="admin" active="contents">
    <form method="POST" action="{{ route('admin.contents.store') }}" enctype="multipart/form-data" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.contents._form')
    </form>
</x-layouts.dashboard>

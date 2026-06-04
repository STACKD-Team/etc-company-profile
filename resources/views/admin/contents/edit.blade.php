<x-layouts.dashboard title="Edit Konten" area="admin" active="contents">
    <form method="POST" action="{{ route('admin.contents.update', $content) }}" enctype="multipart/form-data" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.contents._form')
    </form>
</x-layouts.dashboard>

<x-layouts.dashboard :title="'Tambah '.$pageTitle" area="admin" :active="$contentType">
    <form method="POST" action="{{ route($routeBase.'.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
        @include($viewBase.'._form')
    </form>
</x-layouts.dashboard>

<x-layouts.dashboard title="Edit Program" area="admin" active="programs">
    <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="rounded-card bg-white p-6 shadow-panel">
        @include('admin.programs._form')
    </form>
</x-layouts.dashboard>

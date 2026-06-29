<x-layouts.dashboard title="Edit Enrollment" area="admin" active="enrollments">
    <x-ui.panel heading="Data Enrollment">
        <form method="POST" action="{{ route('admin.enrollment.update', $enrollment) }}" class="space-y-6">
            @include('pages.admin.enrollment._form')
            <x-ui.action-bar align="start">
                <x-ui.button type="submit" icon="heroicon-m-check">Simpan</x-ui.button>
                <x-ui.button :href="route('admin.enrollment.show', $enrollment)" outlined color="gray">Batal</x-ui.button>
            </x-ui.action-bar>
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

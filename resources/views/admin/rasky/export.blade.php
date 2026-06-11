<x-layouts.dashboard :title="$title" area="admin" active="reports">
    <x-ui.panel :heading="$title" :description="$description" class="max-w-4xl">
        <x-slot:actions>
            <x-ui.badge status="draft" size="sm">Template Export</x-ui.badge>
        </x-slot:actions>

        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf

            @if (($type ?? null) === 'students')
                <div class="grid gap-5 md:grid-cols-2">
                    <x-ui.number-field name="year" label="Tahun daftar" :value="now()->year" min="2020" max="2100" />
                    <x-ui.field name="status" label="Status siswa" placeholder="Pelajar, Mahasiswa, Karyawan" />
                    <x-ui.select name="program_id" label="Program" placeholder="Semua program" :options="$programs->pluck('name', 'id')->all()" />
                    <x-ui.select name="class_id" label="Kelas" placeholder="Semua kelas" :options="$classes->pluck('name', 'id')->all()" />
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2">
                    <x-ui.select
                        name="report_card_id"
                        label="Rapor"
                        placeholder="Semua rapor"
                        :options="$reportCards->mapWithKeys(fn ($reportCard) => [$reportCard->id => $reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor #'.$reportCard->id])->all()"
                    />
                    <x-ui.select name="class_id" label="Kelas" placeholder="Semua kelas" :options="$classes->pluck('name', 'id')->all()" />
                    <x-ui.select name="is_published" label="Status publish" placeholder="Semua" :options="['1' => 'Published', '0' => 'Draft']" />
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-ui.date-picker name="issued_from" label="Terbit dari" />
                        <x-ui.date-picker name="issued_to" label="Terbit sampai" />
                    </div>
                </div>
            @endif

            <x-ui.button type="submit" icon="heroicon-m-arrow-down-tray">Download</x-ui.button>
        </form>
    </x-ui.panel>
</x-layouts.dashboard>

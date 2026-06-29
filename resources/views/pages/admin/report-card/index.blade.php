<x-layouts.dashboard :title="$title" area="admin" :active="$active ?? 'reports'">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    @php
        $tableColumns = collect($columns)->mapWithKeys(fn ($column, $key) => [is_string($key) ? $key : 'column_'.$key => $column])->all();
    @endphp

    <x-ui.data-table
        :items="$items"
        :columns="$tableColumns"
        :row-view="$rowView"
        :empty="$empty"
        :empty-description="$emptyDescription ?? 'Rapor akan tampil setelah workflow akademik tersedia.'"
        :search-placeholder="$searchPlaceholder ?? 'Cari rapor'"
    >
        @if (! empty($actions ?? []))
            <x-slot:actions>
                @foreach ($actions as $action)
                    <x-ui.button :href="route($action['route'])" :icon="($action['icon'] ?? null) === 'add' ? 'heroicon-m-plus' : 'heroicon-m-arrow-down-tray'" outlined size="sm">
                        {{ $action['label'] }}
                    </x-ui.button>
                @endforeach
                <x-ui.button type="button" outlined icon="heroicon-m-arrow-down-tray" data-open-modal="report-card-export-modal" size="sm">
                    Export Rapor
                </x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.data-table>

    <x-ui.modal id="report-card-export-modal" heading="Export Rapor" description="Filter dan unduh rapor dalam format DOCX dari template ETC." icon="heroicon-o-document-arrow-down">
        <form method="POST" action="{{ route('admin.exports.report-cards.download') }}" class="space-y-4">
            @csrf
            <x-ui.select name="report_card_id" label="Rapor" placeholder="Semua rapor" :options="$reportCards->mapWithKeys(fn ($reportCard) => [
                $reportCard->id => ($reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor #'.$reportCard->id).' - '.($reportCard->enrollment?->courseClass?->name ?? 'Tanpa kelas'),
            ])->all()" />
            <x-ui.select name="class_id" label="Kelas" placeholder="Semua kelas" :options="$classes->pluck('name', 'id')->all()" />
            <x-ui.select name="is_published" label="Status" placeholder="Semua status" :options="['1' => 'Published', '0' => 'Draft']" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.date-picker name="issued_from" label="Terbit dari" />
                <x-ui.date-picker name="issued_to" label="Terbit sampai" />
            </div>
            <x-ui.button type="submit" icon="heroicon-m-arrow-down-tray">Download DOCX</x-ui.button>
        </form>
    </x-ui.modal>
</x-layouts.dashboard>

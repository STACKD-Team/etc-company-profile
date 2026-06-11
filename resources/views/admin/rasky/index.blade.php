<x-layouts.dashboard :title="$title" area="admin" :active="$active ?? null">
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
        :empty-description="$emptyDescription ?? 'Data akan tampil setelah workflow terkait tersedia.'"
        :search-placeholder="$searchPlaceholder ?? 'Cari data'"
    >
        @if (! empty($actions ?? []))
            <x-slot:actions>
                @foreach ($actions as $action)
                    <x-ui.button :href="route($action['route'])" :icon="($action['icon'] ?? null) === 'add' ? 'heroicon-m-plus' : 'heroicon-m-arrow-down-tray'" outlined size="sm">
                        {{ $action['label'] }}
                    </x-ui.button>
                @endforeach
            </x-slot:actions>
        @endif
    </x-ui.data-table>
</x-layouts.dashboard>

@props([
    'columns' => [],
    'items',
    'empty' => 'Belum ada data.',
    'emptyDescription' => 'Data akan tampil setelah tersedia.',
    'rowView' => null,
    'sort' => request('sort'),
    'direction' => request('direction', 'asc'),
    'action' => null,
    'method' => 'GET',
    'showSearch' => true,
    'searchName' => 'search',
    'searchPlaceholder' => 'Cari data',
    'searchValue' => null,
    'perPageName' => 'per_page',
    'perPageValue' => null,
    'perPageOptions' => [10 => '10', 20 => '20', 50 => '50'],
])

@php
    $isPaginator = $items instanceof \Illuminate\Contracts\Pagination\Paginator;
    $perPageOptions = collect($perPageOptions)
        ->mapWithKeys(fn ($label, $value) => [(int) $value => (string) $label])
        ->all();
    $allowedPerPage = array_keys($perPageOptions);
    $perPageValue = (int) ($perPageValue ?? request($perPageName, 10));
    $perPageValue = in_array($perPageValue, $allowedPerPage, true) ? $perPageValue : 10;
    $rows = collect($isPaginator ? $items->items() : $items)->take($perPageValue);
    $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
    $searchValue ??= request($searchName);
    $tableId = 'data-table-'.substr(md5(($action ?? request()->url()).($rowView ?? '').implode('|', array_keys($columns))), 0, 10);
    $columnFilters = collect($columns)->mapWithKeys(function ($column, $key) {
        if (! is_array($column) || empty($column['filter'])) {
            return [];
        }

        $filter = is_array($column['filter']) ? $column['filter'] : ['type' => $column['filter']];
        $filter['name'] ??= $key;

        return [$key => $filter];
    });
    $filterNames = $columnFilters->pluck('name')
        ->when($showSearch, fn ($names) => $names->push($searchName))
        ->push($perPageName)
        ->push('page')
        ->unique();
    $resetQuery = collect(request()->query())->except($filterNames->all())->all();
    $resetUrl = url()->current().(count($resetQuery) ? '?'.http_build_query($resetQuery) : '');
    $hasActivePerPage = $perPageValue !== 10;
@endphp

<form
    id="{{ $tableId }}"
    method="{{ $method }}"
    action="{{ $action }}"
    data-data-table-form
>
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center" data-data-table-toolbar>
        @if ($showSearch)
            <div class="min-w-0 flex-1">
                <x-ui.search-field
                    :name="$searchName"
                    :value="$searchValue"
                    :placeholder="$searchPlaceholder"
                    data-table-filter-debounce
                />
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-end gap-3">
            <div class="flex items-center gap-2">
                <span class="font-heading text-xs font-bold text-etc-on-muted">Tampilkan</span>
                <div class="w-24">
                    <x-ui.select
                        :name="$perPageName"
                        :value="$perPageValue"
                        :options="$perPageOptions"
                        size="sm"
                        data-table-filter-immediate
                        aria-label="Jumlah data per halaman"
                    />
                </div>
            </div>

            @isset($actions)
                {{ $actions }}
            @endisset

            @if ($showSearch || $columnFilters->isNotEmpty() || $hasActivePerPage)
                <x-ui.button
                    :href="$resetUrl"
                    outlined
                    icon="heroicon-m-arrow-path"
                >
                    Reset
                </x-ui.button>
            @endif
        </div>
    </div>

    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="direction" value="{{ $direction }}">
    <input type="hidden" name="page" value="{{ request('page', 1) }}">

    <x-filament::section {{ $attributes->class('etc-data-table') }}>
        <div class="etc-data-table-scroll overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase text-etc-on-muted">
                        @foreach ($columns as $key => $column)
                            @php
                                $label = is_array($column) ? ($column['label'] ?? $key) : $column;
                                $sortable = is_array($column) ? ($column['sortable'] ?? false) : false;
                                $columnKey = is_array($column) ? ($column['key'] ?? $key) : $key;
                                $sortQuery = collect(request()->query())
                                    ->merge([
                                        'sort' => $columnKey,
                                        'direction' => $sort === $columnKey ? $nextDirection : 'asc',
                                        'page' => 1,
                                    ])
                                    ->all();
                                $sortUrl = url()->current().'?'.http_build_query($sortQuery);
                            @endphp
                            <th class="pb-2 pt-1 font-heading font-bold">
                                @if ($sortable)
                                    <a
                                        href="{{ $sortUrl }}"
                                        class="inline-flex items-center gap-1 hover:text-etc-magenta"
                                    >
                                        {{ $label }}
                                        {{
                                            \Filament\Support\generate_icon_html(
                                                $sort === $columnKey
                                                    ? ($direction === 'asc' ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down')
                                                    : 'heroicon-m-arrows-up-down',
                                                attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'shrink-0']),
                                                size: \Filament\Support\Enums\IconSize::Small,
                                            )
                                        }}
                                    </a>
                                @else
                                    {{ $label }}
                                @endif
                            </th>
                        @endforeach
                    </tr>

                    <tr class="border-b-2 border-etc-outline-variant/60">
                        @foreach ($columns as $key => $column)
                            @php
                                $filter = $columnFilters->get($key);
                                $filterType = $filter['type'] ?? null;
                                $filterName = $filter['name'] ?? null;
                                $filterValue = $filterName ? request($filterName) : null;
                                $filterPlaceholder = $filter['placeholder'] ?? 'Semua';
                                $filterWidth = match ($filterType) {
                                    'number' => 'min-w-32',
                                    'date', 'select' => 'min-w-44',
                                    'autocomplete' => 'min-w-56',
                                    default => 'min-w-48',
                                };
                            @endphp
                            <th class="pb-4 align-top font-normal">
                                @if ($filter)
                                    <div class="{{ $filterWidth }}" data-table-column-filter="{{ $key }}">
                                        @switch($filterType)
                                            @case('number')
                                                <x-ui.number-field
                                                    :name="$filterName"
                                                    :value="$filterValue"
                                                    :placeholder="$filterPlaceholder"
                                                    :min="$filter['min'] ?? null"
                                                    :max="$filter['max'] ?? null"
                                                    :step="$filter['step'] ?? 1"
                                                    data-table-filter-debounce
                                                />
                                                @break

                                            @case('date')
                                                <x-ui.date-picker
                                                    :name="$filterName"
                                                    :value="$filterValue"
                                                    data-table-filter-immediate
                                                />
                                                @break

                                            @case('select')
                                                <x-ui.select
                                                    :name="$filterName"
                                                    :value="$filterValue"
                                                    :placeholder="$filterPlaceholder"
                                                    :options="$filter['options'] ?? []"
                                                    data-table-filter-immediate
                                                />
                                                @break

                                            @case('autocomplete')
                                                <x-ui.autocomplete
                                                    :name="$filterName"
                                                    :value="$filterValue"
                                                    :placeholder="$filterPlaceholder"
                                                    :options="$filter['options'] ?? []"
                                                />
                                                @break

                                            @default
                                                <x-ui.field
                                                    :name="$filterName"
                                                    :value="$filterValue"
                                                    :placeholder="$filterPlaceholder"
                                                    data-table-filter-debounce
                                                />
                                        @endswitch
                                    </div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y-2 divide-etc-outline-variant/60">
                    @forelse ($rows as $item)
                        @if ($rowView)
                            @include($rowView, ['item' => $item])
                        @else
                            {{ $slot }}
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="py-8">
                                <x-ui.empty-state
                                    :heading="$empty"
                                    :description="$emptyDescription"
                                    icon="heroicon-o-inbox"
                                    compact
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($isPaginator)
            <x-ui.pagination :paginator="$items" class="mt-5" />
        @endif
    </x-filament::section>
</form>

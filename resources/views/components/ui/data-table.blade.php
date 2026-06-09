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
])

@php
    $isPaginator = $items instanceof \Illuminate\Contracts\Pagination\Paginator;
    $rows = $isPaginator ? $items->items() : $items;
    $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
    $isLivewireContext = isset($this) && method_exists($this, 'getId');
    $searchValue ??= request($searchName);
@endphp

<x-filament::section {{ $attributes }}>
    <form method="{{ $method }}" action="{{ $action }}" class="mb-5 space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            @if ($showSearch)
                <div class="min-w-0 flex-1">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            :name="$searchName"
                            type="search"
                            :value="$searchValue"
                            :placeholder="$searchPlaceholder"
                        />
                    </x-filament::input.wrapper>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                @isset($actions)
                    {{ $actions }}
                @endisset

                @if ($showSearch || isset($filters))
                    <x-filament::button type="submit" icon="heroicon-m-magnifying-glass">
                        Filter
                    </x-filament::button>
                @endif
            </div>
        </div>

        @isset($filters)
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                {{ $filters }}
            </div>
        @endisset

        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
    </form>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead>
                <tr class="border-b border-etc-outline-variant/60 text-xs uppercase text-etc-on-muted">
                    @foreach ($columns as $key => $column)
                        @php
                            $label = is_array($column) ? ($column['label'] ?? $key) : $column;
                            $sortable = is_array($column) ? ($column['sortable'] ?? false) : false;
                            $columnKey = is_array($column) ? ($column['key'] ?? $key) : $key;
                        @endphp
                        <th class="py-3 pr-4 font-heading font-bold">
                            @if ($sortable)
                                <a
                                    href="{{ request()->fullUrlWithQuery(['sort' => $columnKey, 'direction' => $sort === $columnKey ? $nextDirection : 'asc']) }}"
                                    class="inline-flex items-center gap-1 hover:text-etc-magenta"
                                >
                                    {{ $label }}
                                    <span class="text-[10px]">
                                        {{ $sort === $columnKey ? ($direction === 'asc' ? '^' : 'v') : 'sort' }}
                                    </span>
                                </a>
                            @else
                                {{ $label }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-etc-outline-variant/50">
                @forelse ($rows as $item)
                    @if ($rowView)
                        @include($rowView, ['item' => $item])
                    @else
                        {{ $slot }}
                    @endif
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="py-8">
                            <x-filament::empty-state :heading="$empty" :description="$emptyDescription" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($isPaginator)
        <div class="mt-5">
            @if ($isLivewireContext)
                <x-filament::pagination :paginator="$items" />
            @else
                <x-filament::section compact>
                    {{ $items->withQueryString()->links() }}
                </x-filament::section>
            @endif
        </div>
    @endif
</x-filament::section>

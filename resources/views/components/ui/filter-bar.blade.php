@props([
    'method' => 'GET',
    'action' => null,
    'searchName' => 'search',
    'searchPlaceholder' => 'Cari data',
    'searchValue' => null,
])

<x-filament::section compact {{ $attributes }}>
    <form method="{{ $method }}" action="{{ $action }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
        <x-ui.search-field :name="$searchName" :value="$searchValue ?? request($searchName)" :placeholder="$searchPlaceholder" />

        <div class="grid gap-3 sm:grid-cols-[1fr_auto] md:flex md:items-end">
            {{ $slot }}
            <x-ui.button type="submit" icon="heroicon-m-magnifying-glass">Filter</x-ui.button>
        </div>
    </form>
</x-filament::section>

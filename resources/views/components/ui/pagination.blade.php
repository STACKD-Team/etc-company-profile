@props([
    'paginator',
    'showSummary' => true,
])

@php
    $isLivewireContext = isset($this) && method_exists($this, 'getId');
    $firstItem = method_exists($paginator, 'firstItem') ? $paginator->firstItem() : null;
    $lastItem = method_exists($paginator, 'lastItem') ? $paginator->lastItem() : null;
    $total = method_exists($paginator, 'total') ? $paginator->total() : null;
    $count = method_exists($paginator, 'count') ? $paginator->count() : 0;
    $currentPage = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : null;
    $hasPages = method_exists($paginator, 'hasPages') ? $paginator->hasPages() : false;
@endphp

<div
    {{ $attributes->class('border-t-2 border-etc-outline-variant/60 pt-4') }}
    data-pagination
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        @if ($showSummary)
            <p class="font-heading text-xs font-bold text-etc-on-muted" data-pagination-summary>
                @if ($total !== null)
                    @if ($total > 0)
                        Menampilkan {{ $firstItem }}-{{ $lastItem }} dari {{ $total }} data
                    @else
                        Belum ada data
                    @endif
                @else
                    Menampilkan {{ $count }} data{{ $currentPage ? ' di halaman '.$currentPage : '' }}
                @endif
            </p>
        @endif

        <div class="flex justify-end" data-pagination-links>
            @if ($isLivewireContext)
                <x-filament::pagination :paginator="$paginator" />
            @else
                {{ $paginator->withQueryString()->links() }}
            @endif
        </div>
    </div>

    @if (! $hasPages)
        <span class="sr-only">Pagination hanya memiliki satu halaman.</span>
    @endif
</div>

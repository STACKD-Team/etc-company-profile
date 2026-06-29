@props([
    'title',
    'subtitle' => null,
    'backUrl' => null,
    'backLabel' => 'Kembali',
])

<div {{ $attributes->class('mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between') }}>
    <div class="min-w-0 space-y-3">
        @if ($backUrl)
            <x-ui.button
                :href="$backUrl"
                outlined
                size="sm"
                icon="heroicon-m-arrow-left"
            >
                {{ $backLabel }}
            </x-ui.button>
        @endif

        <div class="min-w-0 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="font-heading text-2xl font-bold tracking-normal text-etc-charcoal sm:text-3xl">
                    {{ $title }}
                </h1>

                @isset($status)
                    {{ $status }}
                @endisset
            </div>

            @if ($subtitle)
                <p class="max-w-3xl text-sm text-etc-on-muted">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>

    @isset($actions)
        <x-ui.action-bar class="lg:pt-1">
            {{ $actions }}
        </x-ui.action-bar>
    @endisset
</div>

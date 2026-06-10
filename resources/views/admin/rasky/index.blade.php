<x-layouts.dashboard :title="$title" area="admin" :active="$active ?? null">
    @if (session('status'))
        <div class="mb-5 rounded-lg bg-etc-surface-container p-4 text-sm text-etc-on-surface">{{ session('status') }}</div>
    @endif

    <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
        @if (! empty($actions ?? []))
            <div class="mb-5 flex flex-wrap gap-3">
                @foreach ($actions as $action)
                    <x-ui.button :href="route($action['route'])" outlined size="sm">
                        {{ $action['label'] }}
                    </x-ui.button>
                @endforeach
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead>
                    <tr class="border-b border-etc-outline-variant/60 text-xs uppercase text-etc-on-muted">
                        @foreach ($columns as $column)
                            <th class="py-3 pr-4 font-heading font-bold">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-etc-outline-variant/50">
                    @forelse ($items as $item)
                        @include($rowView, ['item' => $item])
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="py-8">
                                <x-ui.empty-state :heading="$empty" description="Data akan tampil setelah workflow terkait tersedia." icon="heroicon-o-archive-box" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $items->links() }}
        </div>
    </section>
</x-layouts.dashboard>

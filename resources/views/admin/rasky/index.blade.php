<x-layouts.dashboard :title="$title" area="admin" :active="$active ?? null">
    @if (session('status'))
        <div class="mb-5 rounded-lg bg-etc-surface-container p-4 text-sm text-etc-on-surface">{{ session('status') }}</div>
    @endif

    <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
        @if (! empty($actions ?? []))
            <div class="mb-5 flex flex-wrap gap-3">
                @foreach ($actions as $action)
                    <a href="{{ route($action['route']) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-full border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-surface hover:border-etc-magenta hover:text-etc-magenta">
                        <span class="material-symbols-outlined text-lg">{{ $action['icon'] ?? 'arrow_forward' }}</span>
                        {{ $action['label'] }}
                    </a>
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
                            <td colspan="{{ count($columns) }}" class="py-8 text-center text-etc-on-muted">{{ $empty }}</td>
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

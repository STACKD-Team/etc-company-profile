<x-layouts.dashboard :title="$title" area="instructor" :active="$active ?? null">
    <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
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

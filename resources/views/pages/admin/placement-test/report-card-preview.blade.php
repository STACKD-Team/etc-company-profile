<x-layouts.dashboard :title="$title" area="admin" active="reports">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-slot:headerActions>
        <x-ui.button :href="route('admin.report-card.edit', $reportCard)" outlined color="gray" size="sm" icon="heroicon-m-pencil-square">
            Edit
        </x-ui.button>
        @unless ($reportCard->is_published)
            <form method="POST" action="{{ route('admin.report-card.publish', $reportCard) }}">
                @csrf
                <x-ui.button type="submit" size="sm" icon="heroicon-m-check">Publish</x-ui.button>
            </form>
        @endunless
        <form method="POST" action="{{ route('admin.exports.report-cards.download') }}">
            @csrf
            <input type="hidden" name="report_card_id" value="{{ $reportCard->id }}">
            <x-ui.button type="submit" outlined color="gray" size="sm" icon="heroicon-m-arrow-down-tray">DOC</x-ui.button>
        </form>
    </x-slot:headerActions>

    <x-ui.panel>
        <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Preview Template</p>
                <h2 class="mt-1 font-heading text-2xl font-black text-etc-on-surface">{{ $reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor' }}</h2>
            </div>
            <x-ui.badge :status="$reportCard->is_published ? 'published' : 'draft'">{{ $reportCard->is_published ? 'Published' : 'Draft' }}</x-ui.badge>
        </div>

        <div class="overflow-x-auto rounded-box border-2 border-etc-outline-variant/60 bg-etc-surface p-5">
            <div class="[&_table]:text-sm [&_.comments]:rounded-lg [&_.comments]:bg-etc-surface [&_.comments]:p-4 [&_.identity]:w-full [&_.scores]:w-full [&_.scores]:border-collapse [&_.scores_td]:border [&_.scores_td]:border-etc-outline-variant [&_.scores_td]:p-2 [&_.scores_th]:border [&_.scores_th]:border-etc-outline-variant [&_.scores_th]:bg-etc-surface-container [&_.scores_th]:p-2 [&_.section-title]:mt-5 [&_.section-title]:font-heading [&_.section-title]:font-black [&_.signatures]:mt-8 [&_.signatures]:w-full [&_.signatures_td]:py-3 [&_.signatures_td]:text-center [&_.title]:text-center [&_.title]:font-heading [&_.title]:text-2xl [&_.title]:font-black">
                {!! $documentHtml !!}
            </div>
        </div>
    </x-ui.panel>
</x-layouts.dashboard>

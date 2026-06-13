<x-layouts.dashboard :title="$title" area="admin" active="reports">
    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.resource-header
        :title="$reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor'"
        :subtitle="$reportCard->enrollment?->courseClass?->name ?? 'Preview template rapor'"
        :back-url="route('admin.report-card.index')"
    >
        <x-slot:status>
            <x-ui.badge :status="$reportCard->is_published ? 'published' : 'draft'">{{ $reportCard->is_published ? 'Published' : 'Draft' }}</x-ui.badge>
        </x-slot:status>
        <x-slot:actions>
            <x-ui.button :href="route('admin.report-card.edit', $reportCard)" outlined color="gray" icon="heroicon-m-pencil-square">Edit</x-ui.button>
            @unless ($reportCard->is_published)
                <form method="POST" action="{{ route('admin.report-card.publish', $reportCard) }}">
                    @csrf
                    <x-ui.button type="submit" icon="heroicon-m-check">Publish</x-ui.button>
                </form>
            @endunless
            <form method="POST" action="{{ route('admin.exports.report-cards.download') }}">
                @csrf
                <input type="hidden" name="report_card_id" value="{{ $reportCard->id }}">
                <x-ui.button type="submit" outlined color="gray" icon="heroicon-m-arrow-down-tray">DOC</x-ui.button>
            </form>
            <x-ui.delete-action :action="route('admin.report-card.destroy', $reportCard)" heading="Hapus rapor?" />
        </x-slot:actions>
    </x-ui.resource-header>

    <x-ui.detail-card heading="Identitas Rapor">
        <x-ui.description-list>
            <x-ui.description-item label="Siswa" :value="$reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? '-'" />
            <x-ui.description-item label="Kelas" :value="$reportCard->enrollment?->courseClass?->name ?? '-'" />
            <x-ui.description-item label="Total Score" :value="$reportCard->total_score ?? '-'" />
            <x-ui.description-item label="Terbit" :value="$reportCard->issued_at?->format('d M Y') ?? '-'" />
        </x-ui.description-list>
    </x-ui.detail-card>

    <x-ui.detail-card heading="Preview Template" class="mt-6">
        <div class="overflow-x-auto rounded-box border-2 border-etc-outline-variant/60 bg-etc-surface p-5">
            <div class="[&_table]:text-sm [&_.comments]:rounded-lg [&_.comments]:bg-etc-surface [&_.comments]:p-4 [&_.identity]:w-full [&_.scores]:w-full [&_.scores]:border-collapse [&_.scores_td]:border [&_.scores_td]:border-etc-outline-variant [&_.scores_td]:p-2 [&_.scores_th]:border [&_.scores_th]:border-etc-outline-variant [&_.scores_th]:bg-etc-surface-container [&_.scores_th]:p-2 [&_.section-title]:mt-5 [&_.section-title]:font-heading [&_.section-title]:font-black [&_.signatures]:mt-8 [&_.signatures]:w-full [&_.signatures_td]:py-3 [&_.signatures_td]:text-center [&_.title]:text-center [&_.title]:font-heading [&_.title]:text-2xl [&_.title]:font-black">
                {!! $documentHtml !!}
            </div>
        </div>
    </x-ui.detail-card>
</x-layouts.dashboard>

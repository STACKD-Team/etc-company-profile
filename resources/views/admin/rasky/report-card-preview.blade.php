<x-layouts.dashboard :title="$title" area="admin" active="reports">
    @if (session('status'))
        <div class="mb-5 rounded-lg bg-etc-surface-container p-4 text-sm text-etc-on-surface">{{ session('status') }}</div>
    @endif

    <x-slot:headerActions>
        <a href="{{ route('admin.report-cards.edit', $reportCard) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-full border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-surface hover:border-etc-magenta hover:text-etc-magenta">
            <span class="material-symbols-outlined text-lg">edit</span>
            Edit
        </a>
        @unless ($reportCard->is_published)
            <form method="POST" action="{{ route('admin.report-cards.publish', $reportCard) }}">
                @csrf
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-full bg-etc-magenta px-4 py-2 font-heading text-sm font-bold text-white hover:bg-etc-primary">
                    <span class="material-symbols-outlined text-lg">publish</span>
                    Publish
                </button>
            </form>
        @endunless
        <form method="POST" action="{{ route('admin.exports.report-cards.download') }}">
            @csrf
            <input type="hidden" name="report_card_id" value="{{ $reportCard->id }}">
            <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-full border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-surface hover:border-etc-magenta hover:text-etc-magenta">
                <span class="material-symbols-outlined text-lg">download</span>
                DOC
            </button>
        </form>
    </x-slot:headerActions>

    <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
        <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Preview Template</p>
                <h2 class="mt-1 font-heading text-2xl font-black text-etc-on-surface">{{ $reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor' }}</h2>
            </div>
            <span class="inline-flex rounded-full bg-etc-surface-container px-3 py-1 font-heading text-xs font-bold text-etc-magenta">
                {{ $reportCard->is_published ? 'Published' : 'Draft' }}
            </span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-etc-outline-variant/60 bg-white p-5">
            <div class="[&_table]:text-sm [&_.comments]:rounded-lg [&_.comments]:bg-etc-surface [&_.comments]:p-4 [&_.identity]:w-full [&_.scores]:w-full [&_.scores]:border-collapse [&_.scores_td]:border [&_.scores_td]:border-etc-outline-variant [&_.scores_td]:p-2 [&_.scores_th]:border [&_.scores_th]:border-etc-outline-variant [&_.scores_th]:bg-etc-surface-container [&_.scores_th]:p-2 [&_.section-title]:mt-5 [&_.section-title]:font-heading [&_.section-title]:font-black [&_.signatures]:mt-8 [&_.signatures]:w-full [&_.signatures_td]:py-3 [&_.signatures_td]:text-center [&_.title]:text-center [&_.title]:font-heading [&_.title]:text-2xl [&_.title]:font-black">
                {!! $documentHtml !!}
            </div>
        </div>
    </section>
</x-layouts.dashboard>

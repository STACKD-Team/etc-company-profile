@php
    $enrollment = $reportCard->enrollment;
    $studentName = $enrollment?->user?->full_name ?? $enrollment?->user?->name ?? '-';
@endphp

<x-layouts.dashboard title="Review Assessment" area="instructor" active="reports">
    <x-slot:headerActions>
        <x-ui.button :href="route('instructor.report-cards.index')" outlined icon="heroicon-m-arrow-left">
            Kembali
        </x-ui.button>
        @unless ($reportCard->is_published)
            <x-ui.button :href="route('instructor.report-cards.edit', $reportCard)" icon="heroicon-m-pencil-square">
                Edit Draft
            </x-ui.button>
        @endunless
    </x-slot:headerActions>

    @if (session('status'))
        <x-ui.alert status="success" class="mb-5">{{ session('status') }}</x-ui.alert>
    @endif

    <div class="space-y-6">
        <x-ui.panel :heading="$studentName" :description="$enrollment?->courseClass?->name ?? '-'" icon="heroicon-o-document-text">
            <div class="flex flex-wrap gap-2">
                <x-ui.badge :status="$reportCard->is_published ? 'published' : 'draft'" />
                <x-ui.badge :status="$isComplete ? 'complete' : 'pending'">
                    {{ $isComplete ? 'Assessment lengkap' : 'Assessment belum lengkap' }}
                </x-ui.badge>
            </div>
            @if ($reportCard->is_published)
                <p class="mt-4 rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4 text-sm text-etc-on-muted">
                    Rapor ini sudah dipublish admin dan hanya dapat ditinjau oleh instructor.
                </p>
            @endif
        </x-ui.panel>

        <section class="grid gap-6 xl:grid-cols-2">
            <x-ui.panel heading="Written Test" icon="heroicon-o-pencil-square">
                <dl class="divide-y divide-etc-outline-variant/50">
                    @foreach ([
                        'Listening' => $reportCard->score_listening,
                        'Vocabulary' => $reportCard->score_vocabulary,
                        'Structure' => $reportCard->score_structure,
                        'Reading' => $reportCard->score_reading,
                        'Writing' => $reportCard->score_writing,
                    ] as $label => $value)
                        <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <dt class="text-sm text-etc-on-muted">{{ $label }}</dt>
                            <dd class="font-heading font-bold text-etc-on-surface">{{ $value ?? '-' }}</dd>
                        </div>
                    @endforeach
                </dl>
                <x-slot:footer>
                    <div class="flex items-center justify-between">
                        <span class="font-heading text-sm font-bold">Total Score</span>
                        <span class="font-heading text-2xl font-black text-etc-magenta">{{ $reportCard->total_score ?? '-' }}</span>
                    </div>
                </x-slot:footer>
            </x-ui.panel>

            <x-ui.panel heading="Class Assessment" icon="heroicon-o-chart-bar-square">
                <dl class="divide-y divide-etc-outline-variant/50">
                    @foreach ([
                        'Pronunciation' => $reportCard->grade_pronunciation,
                        'Sentence Arrangement' => $reportCard->grade_sentence_arrangement,
                        'Class Participation' => $reportCard->grade_class_participation,
                        'Questioning Skill' => $reportCard->grade_questioning_skill,
                        'Analyzing Skill' => $reportCard->grade_analyzing_skill,
                    ] as $label => $value)
                        <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <dt class="text-sm text-etc-on-muted">{{ $label }}</dt>
                            <dd><x-ui.badge :status="$value">{{ $value ?? '-' }}</x-ui.badge></dd>
                        </div>
                    @endforeach
                </dl>
                <x-slot:footer>
                    <div class="flex items-center justify-between">
                        <span class="font-heading text-sm font-bold">Final Grade</span>
                        <span class="font-heading text-2xl font-black text-etc-magenta">{{ $reportCard->final_grade ?? '-' }}</span>
                    </div>
                </x-slot:footer>
            </x-ui.panel>
        </section>

        <x-ui.panel heading="Rekomendasi Instructor" icon="heroicon-o-chat-bubble-left-right">
            <div class="grid gap-5 md:grid-cols-[240px_minmax(0,1fr)]">
                <div class="rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4">
                    <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Next Class</p>
                    <p class="mt-2 font-heading font-bold text-etc-on-surface">{{ $reportCard->next_class ?? '-' }}</p>
                </div>
                <div class="rounded-box border-2 border-etc-outline-variant bg-etc-surface p-4">
                    <p class="font-heading text-xs font-bold uppercase text-etc-on-muted">Comments and Suggestions</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-etc-on-surface">{{ $reportCard->comments ?? '-' }}</p>
                </div>
            </div>
        </x-ui.panel>
    </div>
</x-layouts.dashboard>

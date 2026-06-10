@php
    $class = $reportCard->enrollment?->courseClass;
    $writtenTests = [
        'score_listening' => 'Listening',
        'score_vocabulary' => 'Vocabulary',
        'score_structure' => 'Structure',
        'score_reading' => 'Reading',
        'score_writing' => 'Writing',
    ];
    $classAssessments = [
        'grade_pronunciation' => 'Pronunciation Fluency',
        'grade_sentence_arrangement' => 'Sentence and Word Arrangement',
        'grade_class_participation' => 'Class Participation',
        'grade_questioning_skill' => 'Questioning Skill',
        'grade_analyzing_skill' => 'Analyzing Skill',
    ];
@endphp

<x-layouts.dashboard title="Detail Rapor" area="student" active="reports" :user="$student">
    <div class="space-y-6">
        <x-ui.panel heading="Detail Rapor" description="Rapor ini sudah dipublikasikan dan dapat dilihat oleh siswa.">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_280px]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.badge status="published">Published</x-ui.badge>
                        <x-ui.badge status="success">Final Grade {{ $reportCard->final_grade ?? '-' }}</x-ui.badge>
                    </div>
                    <h2 class="mt-4 font-heading text-2xl font-black text-etc-on-surface">{{ $class?->program?->name ?? 'Rapor ETC Planet' }}</h2>
                    <p class="mt-2 text-sm text-etc-on-muted">{{ $class?->name ?? 'Kelas ETC' }} - diterbitkan {{ $reportCard->issued_at?->format('d M Y') ?? '-' }}</p>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <p class="text-xs font-bold uppercase text-etc-on-muted">Total Score</p>
                    <p class="mt-2 font-heading text-4xl font-black text-etc-magenta">{{ $reportCard->total_score ?? '-' }}</p>
                    <p class="mt-2 text-sm text-etc-on-muted">Next Class: <span class="font-semibold text-etc-on-surface">{{ $reportCard->next_class ?? '-' }}</span></p>
                </div>
            </div>
        </x-ui.panel>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.panel heading="Written Test" description="Nilai tes tertulis berdasarkan komponen rapor ETC.">
                <dl class="grid gap-3 sm:grid-cols-2">
                    @foreach ($writtenTests as $field => $label)
                        <div class="rounded-card bg-etc-surface-low p-4">
                            <dt class="text-sm font-bold text-etc-on-muted">{{ $label }}</dt>
                            <dd class="mt-1 font-heading text-2xl font-black text-etc-magenta">{{ $reportCard->{$field} ?? '-' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.panel>

            <x-ui.panel heading="Overall Class Assessment" description="Penilaian sikap dan partisipasi selama kelas.">
                <dl class="grid gap-3 sm:grid-cols-2">
                    @foreach ($classAssessments as $field => $label)
                        <div class="rounded-card bg-etc-surface-low p-4">
                            <dt class="text-sm font-bold text-etc-on-muted">{{ $label }}</dt>
                            <dd class="mt-1 font-heading text-2xl font-black text-etc-magenta">{{ $reportCard->{$field} ?? '-' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.panel>
        </div>

        <x-ui.panel heading="Comments and Suggestions" description="Catatan dari instructor untuk perkembangan belajar berikutnya.">
            <p class="text-sm leading-6 text-etc-on-surface">{{ $reportCard->comments ?? 'Belum ada komentar.' }}</p>
        </x-ui.panel>

        <x-ui.panel heading="Tanda Tangan" description="Nama penanggung jawab akademik yang terhubung dengan rapor.">
            <dl class="grid gap-4 md:grid-cols-3">
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Managing Director</dt>
                    <dd class="mt-2 font-heading text-sm font-bold text-etc-on-surface">{{ $reportCard->managingDirector?->full_name ?? $reportCard->managingDirector?->name ?? '-' }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Academic Director</dt>
                    <dd class="mt-2 font-heading text-sm font-bold text-etc-on-surface">{{ $reportCard->academicDirector?->full_name ?? $reportCard->academicDirector?->name ?? '-' }}</dd>
                </div>
                <div class="rounded-card bg-etc-surface-low p-4">
                    <dt class="text-xs font-bold uppercase text-etc-on-muted">Talent</dt>
                    <dd class="mt-2 font-heading text-sm font-bold text-etc-on-surface">{{ $reportCard->instructor?->full_name ?? $reportCard->instructor?->name ?? $class?->instructor?->full_name ?? $class?->instructor?->name ?? '-' }}</dd>
                </div>
            </dl>
        </x-ui.panel>

        <div class="flex flex-wrap gap-3">
            <x-ui.button :href="route('student.report-cards.index')" outlined icon="heroicon-m-arrow-left">Kembali</x-ui.button>
            @if ($reportCard->pdf_path)
                <x-ui.button :href="route('student.report-cards.download', $reportCard)" icon="heroicon-m-arrow-down-tray">Unduh Rapor</x-ui.button>
            @endif
        </div>
    </div>
</x-layouts.dashboard>

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
    <x-ui.resource-header
        title="Detail Rapor"
        :subtitle="($class?->program?->name ?? 'Rapor ETC Planet').' - '.($class?->name ?? 'Kelas ETC')"
        :back-url="route('student.report-cards.index')"
    >
        <x-slot name="status">
            <x-ui.badge status="published">Published</x-ui.badge>
            <x-ui.badge status="success">Final Grade {{ $reportCard->final_grade ?? '-' }}</x-ui.badge>
        </x-slot>

        @if ($reportCard->pdf_path)
            <x-slot name="actions">
                <x-ui.button :href="route('student.report-cards.download', $reportCard)" icon="heroicon-m-arrow-down-tray">
                    Unduh Rapor
                </x-ui.button>
            </x-slot>
        @endif
    </x-ui.resource-header>

    <div class="space-y-6">
        <x-ui.detail-card heading="Ringkasan Rapor" description="Rapor ini sudah dipublikasikan dan dapat dilihat oleh siswa.">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_280px]">
                <x-ui.description-list columns="2">
                    <x-ui.description-item label="Program" :value="$class?->program?->name ?? 'Rapor ETC Planet'" />
                    <x-ui.description-item label="Kelas" :value="$class?->name ?? 'Kelas ETC'" />
                    <x-ui.description-item label="Ruangan" :value="$class?->room_label ?? 'Ruangan belum ditentukan'" />
                    <x-ui.description-item label="Tanggal Terbit" :value="$reportCard->issued_at?->format('d M Y')" />
                    <x-ui.description-item label="Next Class" :value="$reportCard->next_class" />
                    <x-ui.description-item label="Status">
                        <x-ui.badge status="published">Published</x-ui.badge>
                    </x-ui.description-item>
                </x-ui.description-list>

                <div class="rounded-box bg-etc-surface-container p-4 shadow-soft">
                    <p class="text-xs font-bold uppercase text-etc-on-muted">Total Score</p>
                    <p class="mt-2 font-heading text-4xl font-bold text-etc-magenta">{{ $reportCard->total_score ?? '-' }}</p>
                    <p class="mt-2 text-sm text-etc-on-muted">Final Grade: <span class="font-semibold text-etc-on-surface">{{ $reportCard->final_grade ?? '-' }}</span></p>
                </div>
            </div>
        </x-ui.detail-card>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.detail-card heading="Written Test" description="Nilai tes tertulis berdasarkan komponen rapor ETC.">
                <x-ui.description-list columns="2">
                    @foreach ($writtenTests as $field => $label)
                        <x-ui.description-item :label="$label">
                            <span class="font-heading text-2xl font-bold text-etc-magenta">{{ $reportCard->{$field} ?? '-' }}</span>
                        </x-ui.description-item>
                    @endforeach
                </x-ui.description-list>
            </x-ui.detail-card>

            <x-ui.detail-card heading="Overall Class Assessment" description="Penilaian sikap dan partisipasi selama kelas.">
                <x-ui.description-list columns="2">
                    @foreach ($classAssessments as $field => $label)
                        <x-ui.description-item :label="$label">
                            <span class="font-heading text-2xl font-bold text-etc-magenta">{{ $reportCard->{$field} ?? '-' }}</span>
                        </x-ui.description-item>
                    @endforeach
                </x-ui.description-list>
            </x-ui.detail-card>
        </div>

        <x-ui.detail-card heading="Comments and Suggestions" description="Catatan dari instructor untuk perkembangan belajar berikutnya.">
            <p class="text-sm leading-6 text-etc-on-surface">{{ $reportCard->comments ?? 'Belum ada komentar.' }}</p>
        </x-ui.detail-card>

        <x-ui.detail-card heading="Tanda Tangan" description="Nama penanggung jawab akademik yang terhubung dengan rapor.">
            <x-ui.description-list columns="3">
                <x-ui.description-item
                    label="Managing Director"
                    :value="$reportCard->managingDirector?->full_name ?? $reportCard->managingDirector?->name"
                />
                <x-ui.description-item
                    label="Academic Director"
                    :value="$reportCard->academicDirector?->full_name ?? $reportCard->academicDirector?->name"
                />
                <x-ui.description-item
                    label="Talent"
                    :value="$reportCard->instructor?->full_name ?? $reportCard->instructor?->name ?? $class?->instructor?->full_name ?? $class?->instructor?->name"
                />
            </x-ui.description-list>
        </x-ui.detail-card>
    </div>
</x-layouts.dashboard>

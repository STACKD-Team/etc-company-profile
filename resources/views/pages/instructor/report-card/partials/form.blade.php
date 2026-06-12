<form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <x-ui.panel heading="Identitas Assessment" description="Siswa dan kelas dikunci berdasarkan enrollment milikmu." icon="heroicon-o-user-circle">
            <dl class="grid gap-x-6 gap-y-5 sm:grid-cols-2 xl:grid-cols-4" data-instructor-assessment-identity>
                @foreach ([
                    ['label' => 'Siswa', 'value' => $studentName],
                    ['label' => 'Kelas', 'value' => $enrollment->courseClass?->name ?? '-'],
                    ['label' => 'Program', 'value' => $enrollment->courseClass?->program?->name ?? '-'],
                    ['label' => 'Status', 'value' => str($enrollment->status)->headline()],
                ] as $detail)
                    <div class="border-l-2 border-etc-outline-variant pl-4 first:border-etc-magenta">
                        <dt class="font-heading text-xs font-bold uppercase tracking-wide text-etc-on-muted">{{ $detail['label'] }}</dt>
                        <dd class="mt-2 text-sm font-semibold text-etc-on-surface">{{ $detail['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.panel>

        <x-ui.panel
            heading="Written Test"
            description="Setiap komponen bernilai 0-20. Total dihitung otomatis oleh sistem."
            icon="heroicon-o-pencil-square"
            class="motion-safe:transition motion-safe:duration-200 motion-safe:hover:shadow-panel"
        >
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ([
                    'score_listening' => 'Listening',
                    'score_vocabulary' => 'Vocabulary',
                    'score_structure' => 'Structure',
                    'score_reading' => 'Reading',
                    'score_writing' => 'Writing',
                ] as $field => $label)
                    <x-ui.number-field
                        :name="$field"
                        :label="$label"
                        :value="$reportCard->{$field}"
                        min="0"
                        max="20"
                        helper="Maksimal 20"
                    />
                @endforeach
            </div>
        </x-ui.panel>

        <x-ui.panel
            heading="Overall Class Assessment"
            description="Nilai observasi selama proses pembelajaran."
            icon="heroicon-o-chart-bar-square"
            class="motion-safe:transition motion-safe:duration-200 motion-safe:hover:shadow-panel"
        >
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ([
                    'grade_pronunciation' => 'Pronunciation',
                    'grade_sentence_arrangement' => 'Sentence Arrangement',
                    'grade_class_participation' => 'Class Participation',
                    'grade_questioning_skill' => 'Questioning Skill',
                    'grade_analyzing_skill' => 'Analyzing Skill',
                ] as $field => $label)
                    <x-ui.select
                        :name="$field"
                        :label="$label"
                        :value="$reportCard->{$field}"
                        placeholder="Pilih grade"
                        :options="$gradeOptions"
                    />
                @endforeach
            </div>
        </x-ui.panel>

        <x-ui.panel
            heading="Hasil Akhir"
            description="Lengkapi rekomendasi kelas dan catatan untuk siswa."
            icon="heroicon-o-trophy"
            class="motion-safe:transition motion-safe:duration-200 motion-safe:hover:shadow-panel"
        >
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.select
                    name="final_grade"
                    label="Final Grade"
                    :value="$reportCard->final_grade"
                    placeholder="Pilih grade"
                    :options="$gradeOptions"
                />
                <x-ui.field
                    name="next_class"
                    label="Next Class"
                    :value="$reportCard->next_class"
                    placeholder="Contoh: Teen 5"
                    maxlength="100"
                />
            </div>
            <div class="mt-5">
                <x-ui.textarea
                    name="comments"
                    label="Comments and Suggestions"
                    :value="$reportCard->comments"
                    placeholder="Tuliskan perkembangan, kekuatan, dan saran berikutnya."
                    :rows="6"
                    maxlength="5000"
                />
            </div>
        </x-ui.panel>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <x-ui.button :href="route('instructor.report-cards.index')" outlined>
                Batal
            </x-ui.button>
            <x-ui.button type="submit" icon="heroicon-m-check">
                Simpan Draft Assessment
            </x-ui.button>
        </div>
</form>

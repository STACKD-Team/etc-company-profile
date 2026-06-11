<x-layouts.dashboard :title="$title" area="admin" active="reports">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        @php
            $enrollmentOptions = $enrollments->mapWithKeys(fn ($enrollment) => [
                $enrollment->id => trim(($enrollment->user?->full_name ?? $enrollment->user?->name ?? 'Siswa').' - '.($enrollment->courseClass?->name ?? 'Kelas')),
            ])->all();
            $gradeOptions = ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'];
            $directorOptions = $directors->pluck('name', 'id')->all();
            $instructorOptions = $instructors->pluck('name', 'id')->all();
        @endphp

        <x-ui.panel>
            <x-slot:actions>
                <x-ui.button :href="route('admin.report-cards.index')" outlined color="gray" size="sm" icon="heroicon-m-arrow-left">Kembali</x-ui.button>
            </x-slot:actions>

            <div>
                <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Student Evaluation</p>
                <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $title }}</h2>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <x-ui.select name="enrollment_id" label="Enrollment" :value="$reportCard->enrollment_id" placeholder="Pilih enrollment" :options="$enrollmentOptions" required />
                <x-ui.date-picker name="issued_at" label="Tanggal Terbit" :value="$reportCard->issued_at?->format('Y-m-d')" />
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Written Test">
            <div class="grid gap-5 md:grid-cols-5">
                @foreach ([
                    'score_listening' => 'Listening',
                    'score_vocabulary' => 'Vocabulary',
                    'score_structure' => 'Structure',
                    'score_reading' => 'Reading',
                    'score_writing' => 'Writing',
                ] as $field => $label)
                    <x-ui.number-field :name="$field" :label="$label" :value="$reportCard->{$field}" min="0" max="20" />
                @endforeach
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Overall Class Assessment">
            <div class="grid gap-5 md:grid-cols-5">
                @foreach ([
                    'grade_pronunciation' => 'Pronunciation',
                    'grade_sentence_arrangement' => 'Sentence',
                    'grade_class_participation' => 'Participation',
                    'grade_questioning_skill' => 'Questioning',
                    'grade_analyzing_skill' => 'Analyzing',
                ] as $field => $label)
                    <x-ui.select :name="$field" :label="$label" :value="$reportCard->{$field}" placeholder="-" :options="$gradeOptions" />
                @endforeach
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Final Result">
            <div class="grid gap-5 md:grid-cols-3">
                <x-ui.number-field name="total_score" label="Total Score" :value="$reportCard->total_score" min="0" max="100" />
                <x-ui.select name="final_grade" label="Final Grade" :value="$reportCard->final_grade" placeholder="Pilih grade" :options="$gradeOptions" />
                <x-ui.field name="next_class" label="Next Class" :value="$reportCard->next_class" />
            </div>

            <div class="mt-5">
                <x-ui.textarea name="comments" label="Comments and Suggestions" rows="5" :value="$reportCard->comments" />
            </div>
        </x-ui.panel>

        <x-ui.panel heading="Signatures">
            <div class="grid gap-5 md:grid-cols-3">
                <x-ui.select name="managing_director_id" label="Managing Director" :value="$reportCard->managing_director_id" placeholder="Pilih director" :options="$directorOptions" />
                <x-ui.select name="academic_director_id" label="Academic Director" :value="$reportCard->academic_director_id" placeholder="Pilih director" :options="$directorOptions" />
                <x-ui.select name="instructor_id" label="Talent" :value="$reportCard->instructor_id" placeholder="Pilih instructor" :options="$instructorOptions" />
            </div>
        </x-ui.panel>

        <x-ui.button type="submit" icon="heroicon-m-check">Simpan Rapor</x-ui.button>
    </form>
</x-layouts.dashboard>

<x-layouts.dashboard :title="$title" area="admin" active="reports">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="font-heading text-xs font-bold uppercase text-etc-magenta">Student Evaluation</p>
                    <h2 class="mt-2 font-heading text-2xl font-black text-etc-on-surface">{{ $title }}</h2>
                </div>
                <a href="{{ route('admin.report-cards.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-full border border-etc-outline-variant px-4 py-2 font-heading text-sm font-bold text-etc-on-surface hover:border-etc-magenta hover:text-etc-magenta">Kembali</a>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="enrollment_id">Enrollment</label>
                    <select id="enrollment_id" name="enrollment_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        <option value="">Pilih enrollment</option>
                        @foreach ($enrollments as $enrollment)
                            <option value="{{ $enrollment->id }}" @selected(old('enrollment_id', $reportCard->enrollment_id) == $enrollment->id)>
                                {{ $enrollment->user?->full_name ?? $enrollment->user?->name }} - {{ $enrollment->courseClass?->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('enrollment_id') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="issued_at">Tanggal Terbit</label>
                    <input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at', $reportCard->issued_at?->format('Y-m-d')) }}" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    @error('issued_at') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
            <h3 class="font-heading text-lg font-black text-etc-on-surface">Written Test</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-5">
                @foreach ([
                    'score_listening' => 'Listening',
                    'score_vocabulary' => 'Vocabulary',
                    'score_structure' => 'Structure',
                    'score_reading' => 'Reading',
                    'score_writing' => 'Writing',
                ] as $field => $label)
                    <div>
                        <label class="font-heading text-sm font-bold text-etc-on-surface" for="{{ $field }}">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="number" min="0" max="20" value="{{ old($field, $reportCard->{$field}) }}" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        @error($field) <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
            <h3 class="font-heading text-lg font-black text-etc-on-surface">Overall Class Assessment</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-5">
                @foreach ([
                    'grade_pronunciation' => 'Pronunciation',
                    'grade_sentence_arrangement' => 'Sentence',
                    'grade_class_participation' => 'Participation',
                    'grade_questioning_skill' => 'Questioning',
                    'grade_analyzing_skill' => 'Analyzing',
                ] as $field => $label)
                    <div>
                        <label class="font-heading text-sm font-bold text-etc-on-surface" for="{{ $field }}">{{ $label }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                            <option value="">-</option>
                            @foreach (['A', 'B', 'C', 'D'] as $grade)
                                <option value="{{ $grade }}" @selected(old($field, $reportCard->{$field}) === $grade)>{{ $grade }}</option>
                            @endforeach
                        </select>
                        @error($field) <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
            <h3 class="font-heading text-lg font-black text-etc-on-surface">Final Result</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="total_score">Total Score</label>
                    <input id="total_score" name="total_score" type="number" min="0" max="100" value="{{ old('total_score', $reportCard->total_score) }}" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    @error('total_score') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="final_grade">Final Grade</label>
                    <select id="final_grade" name="final_grade" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        <option value="">Pilih grade</option>
                        @foreach (['A', 'B', 'C', 'D'] as $grade)
                            <option value="{{ $grade }}" @selected(old('final_grade', $reportCard->final_grade) === $grade)>{{ $grade }}</option>
                        @endforeach
                    </select>
                    @error('final_grade') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="next_class">Next Class</label>
                    <input id="next_class" name="next_class" value="{{ old('next_class', $reportCard->next_class) }}" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    @error('next_class') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-5">
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="comments">Comments and Suggestions</label>
                <textarea id="comments" name="comments" rows="5" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">{{ old('comments', $reportCard->comments) }}</textarea>
                @error('comments') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="rounded-card border border-etc-outline-variant/60 bg-white p-6 shadow-soft">
            <h3 class="font-heading text-lg font-black text-etc-on-surface">Signatures</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="managing_director_id">Managing Director</label>
                    <select id="managing_director_id" name="managing_director_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        <option value="">Pilih director</option>
                        @foreach ($directors as $director)
                            <option value="{{ $director->id }}" @selected(old('managing_director_id', $reportCard->managing_director_id) == $director->id)>{{ $director->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="academic_director_id">Academic Director</label>
                    <select id="academic_director_id" name="academic_director_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        <option value="">Pilih director</option>
                        @foreach ($directors as $director)
                            <option value="{{ $director->id }}" @selected(old('academic_director_id', $reportCard->academic_director_id) == $director->id)>{{ $director->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-heading text-sm font-bold text-etc-on-surface" for="instructor_id">Talent</label>
                    <select id="instructor_id" name="instructor_id" class="mt-2 min-h-12 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                        <option value="">Pilih instructor</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}" @selected(old('instructor_id', $reportCard->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
            Simpan Rapor
        </button>
    </form>
</x-layouts.dashboard>

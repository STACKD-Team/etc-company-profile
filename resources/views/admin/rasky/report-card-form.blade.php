<x-layouts.dashboard :title="$title" area="admin" active="reports">
    <form method="POST" action="{{ $action }}" class="max-w-4xl rounded-card bg-white p-6 shadow-soft">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="enrollment_id">Enrollment</label>
                <select id="enrollment_id" name="enrollment_id" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
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
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="total_score">Total Score</label>
                <input id="total_score" name="total_score" type="number" min="0" max="100" value="{{ old('total_score', $reportCard->total_score) }}" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                @error('total_score') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="final_grade">Final Grade</label>
                <select id="final_grade" name="final_grade" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    <option value="">Pilih grade</option>
                    @foreach (['A', 'B', 'C', 'D'] as $grade)
                        <option value="{{ $grade }}" @selected(old('final_grade', $reportCard->final_grade) === $grade)>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="issued_at">Tanggal Terbit</label>
                <input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at', $reportCard->issued_at?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
            </div>

            <div>
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="instructor_id">Talent</label>
                <select id="instructor_id" name="instructor_id" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
                    <option value="">Pilih instructor</option>
                    @foreach ($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id', $reportCard->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-heading text-sm font-bold text-etc-on-surface" for="next_class">Next Class</label>
                <input id="next_class" name="next_class" value="{{ old('next_class', $reportCard->next_class) }}" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">
            </div>
        </div>

        <div class="mt-5">
            <label class="font-heading text-sm font-bold text-etc-on-surface" for="comments">Comments and Suggestions</label>
            <textarea id="comments" name="comments" rows="5" class="mt-2 w-full rounded-lg border border-etc-outline-variant px-4 py-3">{{ old('comments', $reportCard->comments) }}</textarea>
        </div>

        <button type="submit" class="mt-6 inline-flex min-h-12 items-center justify-center rounded-full bg-etc-magenta px-5 py-3 font-heading text-sm font-bold text-white transition hover:bg-etc-primary">
            Simpan Rapor
        </button>
    </form>
</x-layouts.dashboard>

<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class InstructorPanelService
{
    private const SCORE_FIELDS = [
        'score_listening',
        'score_vocabulary',
        'score_structure',
        'score_reading',
        'score_writing',
    ];

    private const ASSESSMENT_FIELDS = [
        ...self::SCORE_FIELDS,
        'grade_pronunciation',
        'grade_sentence_arrangement',
        'grade_class_participation',
        'grade_questioning_skill',
        'grade_analyzing_skill',
        'final_grade',
    ];

    public function dashboard(int $instructorId): array
    {
        $classes = $this->classesQuery($instructorId);

        return [
            'stats' => [
                'students' => $this->enrollmentsQuery($instructorId)
                    ->distinct()
                    ->count('user_id'),
                'upcoming' => (clone $classes)->where('status', 'upcoming')->count(),
                'ongoing' => (clone $classes)->where('status', 'ongoing')->count(),
                'completed' => (clone $classes)->where('status', 'completed')->count(),
                'incomplete_assessments' => $this->incompleteAssessmentsQuery($instructorId)->count(),
            ],
            'classes' => $this->classesQuery($instructorId)
                ->with('program')
                ->withCount('enrollments')
                ->orderByRaw("case status when 'ongoing' then 0 when 'upcoming' then 1 when 'completed' then 2 else 3 end")
                ->orderByRaw('case when start_date is null then 1 else 0 end')
                ->orderBy('start_date')
                ->take(5)
                ->get(),
            'assessments' => $this->incompleteAssessmentsQuery($instructorId)
                ->with(['user', 'courseClass', 'reportCard'])
                ->latest('enrolled_at')
                ->take(5)
                ->get()
                ->each(fn (Enrollment $enrollment) => $this->decorateAssessment($enrollment)),
        ];
    }

    public function paginateClasses(int $instructorId, array $filters): LengthAwarePaginator
    {
        $query = $this->classesQuery($instructorId)
            ->with('program')
            ->withCount('enrollments')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('room', 'like', '%'.$search.'%')
                        ->orWhereHas('program', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['name'] ?? null, fn (Builder $query, string $name) => $query->where('name', 'like', '%'.$name.'%'))
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->where('program_id', $programId))
            ->when($filters['schedule'] ?? null, function (Builder $query, string $schedule): void {
                $query->where(function (Builder $query) use ($schedule): void {
                    $query
                        ->where('schedule_days', 'like', '%'.$schedule.'%')
                        ->orWhere('schedule_time', 'like', '%'.$schedule.'%')
                        ->orWhere('room', 'like', '%'.$schedule.'%');
                });
            })
            ->when(
                array_key_exists('students_count', $filters) && $filters['students_count'] !== null,
                fn (Builder $query) => $query->has('enrollments', '=', (int) $filters['students_count']),
            )
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        return $this->applySort($query, $filters, [
            'name' => 'name',
            'program' => fn (Builder $query, string $direction) => $query->orderBy(
                Program::query()
                    ->select('name')
                    ->whereColumn('programs.id', 'classes.program_id')
                    ->limit(1),
                $direction,
            ),
            'schedule' => fn (Builder $query, string $direction) => $query
                ->orderBy('schedule_days', $direction)
                ->orderBy('schedule_time', $direction)
                ->orderBy('room', $direction),
            'students' => 'enrollments_count',
            'status' => 'status',
            'start_date' => 'start_date',
            'created_at' => 'created_at',
        ], 'start_date')->paginate($this->perPage($filters))->withQueryString();
    }

    public function paginateStudents(int $instructorId, array $filters): LengthAwarePaginator
    {
        $query = $this->enrollmentsQuery($instructorId)
            ->with(['user', 'courseClass', 'reportCard'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->whereHas('user', function (Builder $query) use ($search): void {
                            $query
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('full_name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('courseClass', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['student_id'] ?? null, fn (Builder $query, int|string $studentId) => $query->where('user_id', $studentId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId))
            ->when($filters['enrolled_at'] ?? null, fn (Builder $query, string $date) => $query->whereDate('enrolled_at', $date))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['assessment_status'] ?? null, fn (Builder $query, string $status) => $this->applyAssessmentStatusConstraint($query, $status));

        return $this->applySort($query, $filters, [
            'student' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->studentNameSubquery(),
                $direction,
            ),
            'class' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->classNameSubquery(),
                $direction,
            ),
            'status' => 'status',
            'assessment' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->assessmentStateExpression(),
                $direction,
            ),
            'enrolled_at' => 'enrolled_at',
            'created_at' => 'created_at',
        ], 'enrolled_at')
            ->paginate($this->perPage($filters))
            ->withQueryString()
            ->through(fn (Enrollment $enrollment) => $this->decorateAssessment($enrollment));
    }

    public function paginateAssessments(int $instructorId, array $filters): LengthAwarePaginator
    {
        $query = $this->enrollmentsQuery($instructorId)
            ->with(['user', 'courseClass', 'reportCard'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->whereHas('user', function (Builder $query) use ($search): void {
                            $query
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('full_name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('courseClass', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['student_id'] ?? null, fn (Builder $query, int|string $studentId) => $query->where('user_id', $studentId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId))
            ->when(
                array_key_exists('total_score', $filters) && $filters['total_score'] !== null,
                fn (Builder $query) => $query->whereHas(
                    'reportCard',
                    fn (Builder $query) => $query->where('total_score', (int) $filters['total_score']),
                ),
            )
            ->when($filters['assessment_status'] ?? null, fn (Builder $query, string $status) => $this->applyAssessmentStatusConstraint($query, $status));

        return $this->applySort($query, $filters, [
            'student' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->studentNameSubquery(),
                $direction,
            ),
            'class' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->classNameSubquery(),
                $direction,
            ),
            'score' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->reportCardValueSubquery('total_score'),
                $direction,
            ),
            'assessment' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->assessmentStateExpression(),
                $direction,
            ),
            'enrolled_at' => 'enrolled_at',
            'created_at' => 'created_at',
        ], 'enrolled_at')
            ->paginate($this->perPage($filters))
            ->withQueryString()
            ->through(fn (Enrollment $enrollment) => $this->decorateAssessment($enrollment));
    }

    public function classOptions(int $instructorId): array
    {
        return $this->classesQuery($instructorId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function programOptions(int $instructorId): array
    {
        return Program::query()
            ->whereHas('classes', fn (Builder $query) => $query->where('instructor_id', $instructorId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function studentOptions(int $instructorId, ?int $classId = null): array
    {
        return User::query()
            ->students()
            ->whereHas('enrollments', function (Builder $query) use ($instructorId, $classId): void {
                $query
                    ->when($classId, fn (Builder $query, int $classId) => $query->where('class_id', $classId))
                    ->whereHas('courseClass', fn (Builder $query) => $query->where('instructor_id', $instructorId));
            })
            ->orderByRaw('coalesce(full_name, name)')
            ->get(['id', 'name', 'full_name', 'email'])
            ->mapWithKeys(fn (User $student) => [
                $student->id => ($student->full_name ?: $student->name).' - '.$student->email,
            ])
            ->all();
    }

    public function ownedClass(int $instructorId, CourseClass $class): CourseClass
    {
        abort_unless((int) $class->instructor_id === $instructorId, 403);

        $class->load('program')->loadCount('enrollments');

        return $class;
    }

    public function paginateClassStudents(
        int $instructorId,
        CourseClass $class,
        array $filters,
    ): LengthAwarePaginator {
        $class = $this->ownedClass($instructorId, $class);

        $query = Enrollment::query()
            ->where('class_id', $class->id)
            ->with(['user', 'reportCard'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('user', function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('full_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['student_id'] ?? null, fn (Builder $query, int|string $studentId) => $query->where('user_id', $studentId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['assessment_status'] ?? null, fn (Builder $query, string $status) => $this->applyAssessmentStatusConstraint($query, $status));

        return $this->applySort($query, $filters, [
            'student' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->studentNameSubquery(),
                $direction,
            ),
            'status' => 'status',
            'assessment' => fn (Builder $query, string $direction) => $query->orderBy(
                $this->assessmentStateExpression(),
                $direction,
            ),
            'enrolled_at' => 'enrolled_at',
            'created_at' => 'created_at',
        ], 'enrolled_at')
            ->paginate($this->perPage($filters))
            ->withQueryString()
            ->through(fn (Enrollment $enrollment) => $this->decorateAssessment($enrollment));
    }

    public function ownedEnrollment(int $instructorId, Enrollment $enrollment): Enrollment
    {
        $enrollment->loadMissing(['user', 'courseClass.program', 'reportCard']);
        abort_unless((int) $enrollment->courseClass?->instructor_id === $instructorId, 403);

        return $enrollment;
    }

    public function ownedReportCard(int $instructorId, ReportCard $reportCard): ReportCard
    {
        $reportCard->loadMissing(['enrollment.user', 'enrollment.courseClass.program']);
        abort_unless((int) $reportCard->enrollment?->courseClass?->instructor_id === $instructorId, 403);

        return $reportCard;
    }

    public function createDraft(int $instructorId, Enrollment $enrollment, array $data): ReportCard
    {
        $enrollment = $this->ownedEnrollment($instructorId, $enrollment);
        abort_if($enrollment->reportCard, 409, 'Assessment untuk siswa ini sudah tersedia.');

        return DB::transaction(fn () => ReportCard::query()->create([
            ...$this->assessmentData($data),
            'enrollment_id' => $enrollment->id,
            'instructor_id' => $instructorId,
            'is_published' => false,
        ]));
    }

    public function updateDraft(int $instructorId, ReportCard $reportCard, array $data): ReportCard
    {
        $reportCard = $this->ownedReportCard($instructorId, $reportCard);
        abort_if($reportCard->is_published, 403, 'Rapor yang sudah dipublish tidak dapat diubah instructor.');

        return DB::transaction(function () use ($reportCard, $data): ReportCard {
            $reportCard->update($this->assessmentData($data));

            return $reportCard->refresh()->load(['enrollment.user', 'enrollment.courseClass.program']);
        });
    }

    public function isAssessmentComplete(?ReportCard $reportCard): bool
    {
        if (! $reportCard) {
            return false;
        }

        foreach (self::ASSESSMENT_FIELDS as $field) {
            if ($reportCard->{$field} === null || $reportCard->{$field} === '') {
                return false;
            }
        }

        return true;
    }

    public function assessmentState(?ReportCard $reportCard): string
    {
        if (! $reportCard) {
            return 'not_started';
        }

        if ($reportCard->is_published) {
            return 'published';
        }

        return $this->isAssessmentComplete($reportCard) ? 'complete' : 'incomplete';
    }

    private function assessmentData(array $data): array
    {
        $assessment = collect($data)->only([
            ...self::SCORE_FIELDS,
            'grade_pronunciation',
            'grade_sentence_arrangement',
            'grade_class_participation',
            'grade_questioning_skill',
            'grade_analyzing_skill',
            'final_grade',
            'next_class',
            'comments',
        ])->all();

        $scores = collect(self::SCORE_FIELDS)->map(fn (string $field) => $assessment[$field] ?? null);
        $assessment['total_score'] = $scores->every(fn ($score) => $score !== null && $score !== '')
            ? $scores->sum(fn ($score) => (int) $score)
            : null;

        return $assessment;
    }

    private function decorateAssessment(Enrollment $enrollment): Enrollment
    {
        $enrollment->setAttribute('assessment_state', $this->assessmentState($enrollment->reportCard));

        return $enrollment;
    }

    private function classesQuery(int $instructorId): Builder
    {
        return CourseClass::query()->where('instructor_id', $instructorId);
    }

    private function enrollmentsQuery(int $instructorId): Builder
    {
        return Enrollment::query()
            ->whereHas('courseClass', fn (Builder $query) => $query->where('instructor_id', $instructorId));
    }

    private function incompleteAssessmentsQuery(int $instructorId): Builder
    {
        return $this->applyIncompleteAssessmentConstraint($this->enrollmentsQuery($instructorId));
    }

    private function applyIncompleteAssessmentConstraint(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query
                ->doesntHave('reportCard')
                ->orWhereHas('reportCard', fn (Builder $query) => $this->applyMissingAssessmentFieldConstraint($query));
        });
    }

    private function applyCompleteAssessmentConstraint(Builder $query): Builder
    {
        return $query->whereHas('reportCard', function (Builder $query): void {
            foreach (self::ASSESSMENT_FIELDS as $field) {
                $query->whereNotNull($field);
            }
        });
    }

    private function applyAssessmentStatusConstraint(Builder $query, string $status): Builder
    {
        return match ($status) {
            'not_started' => $query->doesntHave('reportCard'),
            'incomplete' => $query->whereHas(
                'reportCard',
                fn (Builder $query) => $this->applyMissingAssessmentFieldConstraint($query),
            ),
            'draft' => $query->whereHas('reportCard', fn (Builder $query) => $query->where('is_published', false)),
            'published' => $query->whereHas('reportCard', fn (Builder $query) => $query->where('is_published', true)),
            'complete' => $this->applyCompleteAssessmentConstraint($query),
            default => $query,
        };
    }

    private function applyMissingAssessmentFieldConstraint(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            foreach (self::ASSESSMENT_FIELDS as $index => $field) {
                $index === 0
                    ? $query->whereNull($field)
                    : $query->orWhereNull($field);
            }
        });
    }

    private function applySort(
        Builder $query,
        array $filters,
        array $sortable,
        string $defaultSort,
    ): Builder {
        $sort = $sortable[$filters['sort'] ?? ''] ?? $sortable[$defaultSort];
        $direction = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if (is_callable($sort)) {
            $sort($query, $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        return $query->orderBy($query->getModel()->qualifyColumn('id'), $direction);
    }

    private function perPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 10);

        return in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
    }

    private function studentNameSubquery(): Builder
    {
        return User::query()
            ->selectRaw('coalesce(full_name, name)')
            ->whereColumn('users.id', 'enrollments.user_id')
            ->limit(1);
    }

    private function classNameSubquery(): Builder
    {
        return CourseClass::query()
            ->select('name')
            ->whereColumn('classes.id', 'enrollments.class_id')
            ->limit(1);
    }

    private function reportCardValueSubquery(string $column): Builder
    {
        return ReportCard::query()
            ->select($column)
            ->whereColumn('report_cards.enrollment_id', 'enrollments.id')
            ->limit(1);
    }

    private function assessmentStateExpression(): Expression
    {
        $completeConditions = collect(self::ASSESSMENT_FIELDS)
            ->map(fn (string $field) => "report_cards.{$field} is not null")
            ->implode(' and ');

        return DB::raw(
            "(select case
                when report_cards.is_published = 1 then 3
                when {$completeConditions} then 2
                else 1
            end
            from report_cards
            where report_cards.enrollment_id = enrollments.id
            limit 1)",
        );
    }
}

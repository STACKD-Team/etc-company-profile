<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('registers the instructor assessment routes with instructor middleware', function () {
    foreach ([
        'instructor.report-cards.create',
        'instructor.report-cards.store',
        'instructor.report-cards.show',
        'instructor.report-cards.edit',
        'instructor.report-cards.update',
    ] as $routeName) {
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull()
            ->and($route->gatherMiddleware())->toContain('auth')
            ->and($route->gatherMiddleware())->toContain('role:instructor');
    }
});

it('shows scoped dashboard metrics and pending assessments', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);

    $ongoing = createSprintOneClass($program, $instructor, 'Ongoing Alpha', 'ongoing');
    createSprintOneClass($program, $instructor, 'Upcoming Alpha', 'upcoming');
    createSprintOneClass($program, $instructor, 'Completed Alpha', 'completed');
    createSprintOneClass($program, $otherInstructor, 'Hidden Ongoing', 'ongoing');

    $firstStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Pending Student']);
    $secondStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Complete Student']);
    $pendingEnrollment = createSprintOneEnrollment($firstStudent, $ongoing);
    $completeEnrollment = createSprintOneEnrollment($secondStudent, $ongoing);
    createSprintOneAssessment($completeEnrollment, $instructor, completeSprintOneAssessmentData());

    $this->actingAs($instructor)
        ->get(route('instructor.dashboard'))
        ->assertOk()
        ->assertViewHas('stats', fn (array $stats) => $stats === [
            'students' => 2,
            'upcoming' => 1,
            'ongoing' => 1,
            'completed' => 1,
            'incomplete_assessments' => 1,
        ])
        ->assertSee('Pending Student')
        ->assertSee(route('instructor.report-cards.create', $pendingEnrollment), false)
        ->assertDontSee('Hidden Ongoing');
});

it('filters instructor classes students and assessments without leaking other instructors data', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);

    $ongoing = createSprintOneClass($program, $instructor, 'Conversation Active', 'ongoing');
    $upcoming = createSprintOneClass($program, $instructor, 'Grammar Later', 'upcoming');
    $hidden = createSprintOneClass($program, $otherInstructor, 'Hidden Class', 'ongoing');

    $visibleStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Visible Learner']);
    $otherStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Upcoming Learner']);
    $hiddenStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Hidden Learner']);

    $visibleEnrollment = createSprintOneEnrollment($visibleStudent, $ongoing);
    createSprintOneEnrollment($otherStudent, $upcoming);
    createSprintOneEnrollment($hiddenStudent, $hidden);

    $this->actingAs($instructor)
        ->get(route('instructor.classes.index', ['status' => 'ongoing', 'search' => 'Conversation']))
        ->assertOk()
        ->assertSee('Conversation Active')
        ->assertDontSee('Grammar Later')
        ->assertDontSee('Hidden Class');

    $this->actingAs($instructor)
        ->get(route('instructor.students.index', ['class_id' => $ongoing->id, 'search' => 'Visible']))
        ->assertOk()
        ->assertSee('Visible Learner')
        ->assertDontSee('Hidden Learner')
        ->assertViewHas(
            'students',
            fn ($students) => collect($students->items())->pluck('id')->all() === [$visibleEnrollment->id],
        );

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.index', ['assessment_status' => 'not_started']))
        ->assertOk()
        ->assertSee('Visible Learner')
        ->assertSee('Upcoming Learner')
        ->assertDontSee('Hidden Learner')
        ->assertSee(route('instructor.report-cards.create', $visibleEnrollment), false);
});

it('filters every instructor class column with exact entity and number matching', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherProgram = Program::query()->create([
        'name' => 'Other Instructor Program',
        'slug' => 'other-instructor-program-'.str()->random(6),
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'adult',
        'price' => 900000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $target = createSprintOneClass($program, $instructor, 'Target Conversation', 'ongoing');
    $target->update(['schedule_days' => 'Monday', 'schedule_time' => '10:00', 'room' => 'Studio 4']);
    createSprintOneEnrollment(User::factory()->create(['role' => 'student']), $target);
    createSprintOneEnrollment(User::factory()->create(['role' => 'student']), $target);

    $other = createSprintOneClass($otherProgram, $instructor, 'Other Grammar', 'upcoming');
    createSprintOneEnrollment(User::factory()->create(['role' => 'student']), $other);

    foreach ([
        ['name' => 'Conversation'],
        ['program_id' => $program->id],
        ['schedule' => 'Studio 4'],
        ['students_count' => 2],
        ['status' => 'ongoing'],
    ] as $query) {
        $this->actingAs($instructor)
            ->get(route('instructor.classes.index', $query))
            ->assertOk()
            ->assertViewHas(
                'classes',
                fn ($classes) => collect($classes->items())->pluck('id')->all() === [$target->id],
            );
    }
});

it('filters every instructor student column and rejects foreign autocomplete ids', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);
    $targetClass = createSprintOneClass($program, $instructor, 'Target Student Class', 'ongoing');
    $otherClass = createSprintOneClass($program, $instructor, 'Other Student Class', 'ongoing');
    $foreignClass = createSprintOneClass($program, $otherInstructor, 'Foreign Student Class', 'ongoing');

    $targetStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Exact Student']);
    $target = createSprintOneEnrollment($targetStudent, $targetClass);
    $target->update(['enrolled_at' => '2026-05-20', 'status' => 'completed']);
    createSprintOneAssessment($target, $instructor, completeSprintOneAssessmentData());

    $other = createSprintOneEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Other Student']),
        $otherClass,
    );
    createSprintOneAssessment($other, $instructor, ['comments' => 'Still incomplete']);

    $foreignStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Foreign Student']);
    createSprintOneEnrollment($foreignStudent, $foreignClass);

    foreach ([
        ['student_id' => $targetStudent->id],
        ['class_id' => $targetClass->id],
        ['enrolled_at' => '2026-05-20'],
        ['status' => 'completed'],
        ['assessment_status' => 'complete'],
    ] as $query) {
        $this->actingAs($instructor)
            ->get(route('instructor.students.index', $query))
            ->assertOk()
            ->assertViewHas(
                'students',
                fn ($students) => collect($students->items())->pluck('id')->all() === [$target->id],
            );
    }

    $this->actingAs($instructor)
        ->get(route('instructor.students.index', ['student_id' => $foreignStudent->id]))
        ->assertOk()
        ->assertDontSee('Foreign Student')
        ->assertViewHas('students', fn ($students) => $students->isEmpty());

    $this->actingAs($instructor)
        ->get(route('instructor.students.index', ['class_id' => $foreignClass->id]))
        ->assertOk()
        ->assertDontSee('Foreign Student')
        ->assertViewHas('students', fn ($students) => $students->isEmpty());
});

it('filters assessment columns and distinguishes not started incomplete and complete states', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $class = createSprintOneClass($program, $instructor, 'Assessment Filter Class', 'ongoing');
    $completeStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Complete Filter Student']);
    $complete = createSprintOneEnrollment($completeStudent, $class);
    createSprintOneAssessment($complete, $instructor, [
        ...completeSprintOneAssessmentData(),
        'total_score' => 75,
    ]);

    $incomplete = createSprintOneEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Incomplete Filter Student']),
        $class,
    );
    createSprintOneAssessment($incomplete, $instructor, ['score_listening' => 10]);
    $notStarted = createSprintOneEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Not Started Filter Student']),
        $class,
    );

    foreach ([
        [['student_id' => $completeStudent->id], $complete->id],
        [['class_id' => $class->id, 'total_score' => 75], $complete->id],
        [['assessment_status' => 'complete'], $complete->id],
        [['assessment_status' => 'incomplete'], $incomplete->id],
        [['assessment_status' => 'not_started'], $notStarted->id],
    ] as [$query, $expectedId]) {
        $this->actingAs($instructor)
            ->get(route('instructor.report-cards.index', $query))
            ->assertOk()
            ->assertViewHas(
                'assessments',
                fn ($assessments) => collect($assessments->items())->pluck('id')->all() === [$expectedId],
            );
    }
});

it('paginates and filters class detail students while preserving table query state', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $class = createSprintOneClass($program, $instructor, 'Paginated Detail Class', 'ongoing');
    $targetStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Detail Filter Target']);
    $target = createSprintOneEnrollment($targetStudent, $class);

    foreach (range(1, 11) as $index) {
        createSprintOneEnrollment(
            User::factory()->create(['role' => 'student', 'full_name' => "Detail Student {$index}"]),
            $class,
        );
    }

    $this->actingAs($instructor)
        ->get(route('instructor.classes.show', [
            'class' => $class,
            'student_id' => $targetStudent->id,
            'status' => 'active',
            'assessment_status' => 'not_started',
        ]))
        ->assertOk()
        ->assertViewHas(
            'students',
            fn ($students) => collect($students->items())->pluck('id')->all() === [$target->id],
        );

    $response = $this->actingAs($instructor)
        ->get(route('instructor.classes.show', [
            'class' => $class,
            'sort' => 'enrolled_at',
            'direction' => 'asc',
            'search' => 'Detail',
            'status' => 'active',
            'page' => 1,
        ]))
        ->assertOk()
        ->assertSee('data-table-column-filter="student"', false)
        ->assertSee('data-table-column-filter="status"', false)
        ->assertSee('data-table-column-filter="assessment"', false)
        ->assertDontSee('data-open-filter-drawer', false);

    expect($response->viewData('students')->hasMorePages())->toBeTrue();
    $html = html_entity_decode($response->getContent());
    $resetUrl = route('instructor.classes.show', [
        'class' => $class,
        'sort' => 'enrolled_at',
        'direction' => 'asc',
    ]);

    expect($html)
        ->toContain('sort=enrolled_at')
        ->toContain('direction=asc')
        ->toContain('search=Detail')
        ->toContain('status=active')
        ->toContain('page=2')
        ->toContain($resetUrl)
        ->not->toContain('filter_drawer');
});

it('shows class details with students and rejects another instructors class', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);
    $ownedClass = createSprintOneClass($program, $instructor, 'Owned Detail Class', 'ongoing');
    $foreignClass = createSprintOneClass($program, $otherInstructor, 'Foreign Detail Class', 'ongoing');
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Detail Student']);
    $enrollment = createSprintOneEnrollment($student, $ownedClass);

    $this->actingAs($instructor)
        ->get(route('instructor.classes.show', $ownedClass))
        ->assertOk()
        ->assertSee('Detail Student')
        ->assertSee(route('instructor.report-cards.create', $enrollment), false);

    $this->actingAs($instructor)
        ->get(route('instructor.classes.show', $foreignClass))
        ->assertForbidden();
});

it('creates an instructor draft with automatic total and ignores protected fields', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);
    $director = User::factory()->create(['role' => 'admin']);
    $class = createSprintOneClass($program, $instructor, 'Assessment Class', 'ongoing');
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Assessment Student']);
    $enrollment = createSprintOneEnrollment($student, $class);

    $payload = [
        ...completeSprintOneAssessmentData(),
        'total_score' => 1,
        'enrollment_id' => 999999,
        'instructor_id' => $otherInstructor->id,
        'academic_director_id' => $director->id,
        'managing_director_id' => $director->id,
        'pdf_path' => 'hijacked.pdf',
        'issued_at' => '2026-06-10',
        'is_published' => true,
    ];

    $response = $this->actingAs($instructor)
        ->post(route('instructor.report-cards.store', $enrollment), $payload)
        ->assertSessionHasNoErrors();

    $reportCard = ReportCard::query()->sole();
    $response->assertRedirect(route('instructor.report-cards.show', $reportCard));

    expect($reportCard->enrollment_id)->toBe($enrollment->id)
        ->and($reportCard->instructor_id)->toBe($instructor->id)
        ->and($reportCard->total_score)->toBe(75)
        ->and($reportCard->academic_director_id)->toBeNull()
        ->and($reportCard->managing_director_id)->toBeNull()
        ->and($reportCard->pdf_path)->toBeNull()
        ->and($reportCard->issued_at)->toBeNull()
        ->and($reportCard->is_published)->toBeFalse();
});

it('keeps total null until all written scores are filled', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $class = createSprintOneClass($program, $instructor, 'Partial Assessment Class', 'ongoing');
    $student = User::factory()->create(['role' => 'student']);
    $enrollment = createSprintOneEnrollment($student, $class);

    $this->actingAs($instructor)
        ->post(route('instructor.report-cards.store', $enrollment), [
            'score_listening' => 15,
            'score_vocabulary' => 14,
        ])
        ->assertSessionHasNoErrors();

    expect(ReportCard::query()->sole()->total_score)->toBeNull();
});

it('updates owned drafts but cannot edit published or foreign assessments', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);
    $ownedClass = createSprintOneClass($program, $instructor, 'Owned Assessment Class', 'ongoing');
    $foreignClass = createSprintOneClass($program, $otherInstructor, 'Foreign Assessment Class', 'ongoing');
    $ownedEnrollment = createSprintOneEnrollment(User::factory()->create(['role' => 'student']), $ownedClass);
    $foreignEnrollment = createSprintOneEnrollment(User::factory()->create(['role' => 'student']), $foreignClass);
    $ownedReport = createSprintOneAssessment($ownedEnrollment, $instructor, ['comments' => 'Old']);
    $foreignReport = createSprintOneAssessment($foreignEnrollment, $otherInstructor, ['comments' => 'Hidden']);

    $this->actingAs($instructor)
        ->put(route('instructor.report-cards.update', $ownedReport), [
            ...completeSprintOneAssessmentData(),
            'comments' => 'Updated by instructor',
        ])
        ->assertRedirect(route('instructor.report-cards.show', $ownedReport));

    expect($ownedReport->refresh()->comments)->toBe('Updated by instructor')
        ->and($ownedReport->total_score)->toBe(75);

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.show', $foreignReport))
        ->assertForbidden();

    $this->actingAs($instructor)
        ->put(route('instructor.report-cards.update', $foreignReport), completeSprintOneAssessmentData())
        ->assertForbidden();

    $ownedReport->update(['is_published' => true]);

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.edit', $ownedReport))
        ->assertForbidden();

    $this->actingAs($instructor)
        ->put(route('instructor.report-cards.update', $ownedReport), completeSprintOneAssessmentData())
        ->assertForbidden();
});

it('validates instructor assessment scores grades and text length', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $class = createSprintOneClass($program, $instructor, 'Validation Class', 'ongoing');
    $enrollment = createSprintOneEnrollment(User::factory()->create(['role' => 'student']), $class);

    $this->actingAs($instructor)
        ->from(route('instructor.report-cards.create', $enrollment))
        ->post(route('instructor.report-cards.store', $enrollment), [
            'score_listening' => 21,
            'grade_pronunciation' => 'E',
            'next_class' => str_repeat('x', 101),
            'comments' => str_repeat('x', 5001),
        ])
        ->assertRedirect(route('instructor.report-cards.create', $enrollment))
        ->assertSessionHasErrors([
            'score_listening',
            'grade_pronunciation',
            'next_class',
            'comments',
        ]);
});

it('renders the compact shared instructor workspace across sprint one pages', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $class = createSprintOneClass($program, $instructor, 'Compact Workspace Class', 'ongoing');
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Compact Workspace Student']);
    $enrollment = createSprintOneEnrollment($student, $class);

    $dashboard = $this->actingAs($instructor)
        ->get(route('instructor.dashboard'))
        ->assertOk()
        ->assertSee('Instructor Workspace')
        ->assertSee('Pantau kelas yang kamu ajar')
        ->assertSee('data-instructor-dashboard', false)
        ->assertSee('data-instructor-priority-classes', false)
        ->assertSee('data-instructor-assessments', false)
        ->assertSee('Buka Assessment')
        ->assertDontSee('Ruang Mengajar');

    foreach (range(1, 5) as $statIndex) {
        $dashboard->assertSee('data-instructor-stat="'.$statIndex.'"', false);
    }

    $this->actingAs($instructor)
        ->get(route('instructor.classes.index'))
        ->assertOk()
        ->assertSee('data-instructor-classes-table', false)
        ->assertSee('Temukan kelas berdasarkan program');

    $this->actingAs($instructor)
        ->get(route('instructor.students.index'))
        ->assertOk()
        ->assertSee('data-instructor-students-table', false)
        ->assertSee('hanya memuat siswa dari kelas');

    $this->actingAs($instructor)
        ->get(route('instructor.classes.show', $class))
        ->assertOk()
        ->assertSee('data-instructor-class-summary', false)
        ->assertSee('data-instructor-class-students-table', false)
        ->assertSee('<dl', false);

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.index'))
        ->assertOk()
        ->assertSee('data-instructor-assessments-table', false);

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.create', $enrollment))
        ->assertOk()
        ->assertSee('data-instructor-assessment-identity', false)
        ->assertSee('name="score_listening"', false)
        ->assertSee('Simpan Draft Assessment');
});

it('keeps draft actions editable and published assessments read only in the redesigned review', function () {
    [$instructor, $program] = createSprintOneInstructorContext();
    $class = createSprintOneClass($program, $instructor, 'Review State Class', 'completed');
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Review State Student']);
    $enrollment = createSprintOneEnrollment($student, $class);
    $reportCard = createSprintOneAssessment($enrollment, $instructor, completeSprintOneAssessmentData());

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('Edit Draft')
        ->assertSee('data-instructor-recommendation', false)
        ->assertDontSee('Rapor sudah dipublish');

    $reportCard->update(['is_published' => true]);

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('Rapor sudah dipublish')
        ->assertSee('publish tetap menjadi kewenangan admin')
        ->assertDontSee('Edit Draft');
});

function createSprintOneInstructorContext(): array
{
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Rasky Instructor',
    ]);

    $program = Program::query()->create([
        'name' => 'Sprint One English',
        'slug' => 'sprint-one-english-'.str()->random(6),
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 750000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    return [$instructor, $program];
}

function createSprintOneClass(
    Program $program,
    User $instructor,
    string $name,
    string $status,
): CourseClass {
    return CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => $name,
        'schedule_days' => 'Tuesday and Thursday',
        'schedule_time' => '17:30 - 19:00',
        'room' => 'Room A',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->toDateString(),
        'status' => $status,
    ]);
}

function createSprintOneEnrollment(User $student, CourseClass $class): Enrollment
{
    return Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => now()->toDateString(),
        'status' => 'active',
    ]);
}

function createSprintOneAssessment(
    Enrollment $enrollment,
    User $instructor,
    array $attributes = [],
): ReportCard {
    return ReportCard::query()->create([
        'enrollment_id' => $enrollment->id,
        'instructor_id' => $instructor->id,
        'is_published' => false,
        ...$attributes,
    ]);
}

function completeSprintOneAssessmentData(): array
{
    return [
        'score_listening' => 15,
        'score_vocabulary' => 14,
        'score_structure' => 13,
        'score_reading' => 16,
        'score_writing' => 17,
        'grade_pronunciation' => 'A',
        'grade_sentence_arrangement' => 'B',
        'grade_class_participation' => 'A',
        'grade_questioning_skill' => 'B',
        'grade_analyzing_skill' => 'A',
        'final_grade' => 'A',
        'next_class' => 'Teen 5',
        'comments' => 'Strong progress.',
    ];
}

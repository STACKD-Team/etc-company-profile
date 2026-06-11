<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use App\Services\InstructorPanelService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('applies validated per page values to every instructor table and keeps query state', function () {
    [$instructor, $program] = sprintTwoContext();
    $class = sprintTwoClass($program, $instructor, 'Main Paginated Class');

    foreach (range(1, 25) as $index) {
        sprintTwoEnrollment(
            User::factory()->create([
                'role' => 'student',
                'full_name' => sprintf('Paginated Student %02d', $index),
            ]),
            $class,
        );

        sprintTwoClass($program, $instructor, sprintf('Paginated Class %02d', $index));
    }

    $foreignInstructor = User::factory()->create(['role' => 'instructor']);
    sprintTwoClass($program, $foreignInstructor, 'Foreign Paginated Class');

    foreach ([
        [route('instructor.classes.index', ['per_page' => 20, 'search' => 'Paginated']), 'classes'],
        [route('instructor.students.index', ['per_page' => 20, 'search' => 'Paginated']), 'students'],
        [route('instructor.report-cards.index', ['per_page' => 20, 'search' => 'Paginated']), 'assessments'],
        [route('instructor.classes.show', ['class' => $class, 'per_page' => 20, 'search' => 'Paginated']), 'students'],
    ] as [$url, $viewKey]) {
        $response = $this->actingAs($instructor)->get($url)->assertOk();
        $paginator = $response->viewData($viewKey);

        expect($paginator->perPage())->toBe(20)
            ->and($paginator->count())->toBe(20)
            ->and(html_entity_decode((string) $paginator->nextPageUrl()))
            ->toContain('per_page=20')
            ->toContain('search=Paginated');
    }

    $invalid = $this->actingAs($instructor)
        ->get(route('instructor.classes.index', ['per_page' => 999]))
        ->assertOk()
        ->viewData('classes');

    expect($invalid->perPage())->toBe(10)
        ->and($invalid->count())->toBe(10);
});

it('sorts every safe class column and falls back from unknown sort keys', function () {
    [$instructor, $programZulu] = sprintTwoContext('Zulu Program');
    $programAlpha = sprintTwoProgram('Alpha Program');
    $panel = app(InstructorPanelService::class);

    $alpha = sprintTwoClass($programZulu, $instructor, 'Alpha Class', [
        'schedule_days' => 'Wednesday',
        'status' => 'upcoming',
    ]);
    $beta = sprintTwoClass($programAlpha, $instructor, 'Beta Class', [
        'schedule_days' => 'Monday',
        'status' => 'completed',
    ]);
    $gamma = sprintTwoClass($programZulu, $instructor, 'Gamma Class', [
        'schedule_days' => 'Tuesday',
        'status' => 'ongoing',
    ]);

    sprintTwoEnrollment(User::factory()->create(['role' => 'student']), $beta);
    sprintTwoEnrollment(User::factory()->create(['role' => 'student']), $gamma);
    sprintTwoEnrollment(User::factory()->create(['role' => 'student']), $gamma);

    $expected = [
        'name' => [$alpha->id, $beta->id, $gamma->id],
        'program' => [$beta->id, $alpha->id, $gamma->id],
        'schedule' => [$beta->id, $gamma->id, $alpha->id],
        'students' => [$alpha->id, $beta->id, $gamma->id],
        'status' => [$beta->id, $gamma->id, $alpha->id],
    ];

    foreach ($expected as $sort => $ids) {
        $actual = collect($panel->paginateClasses($instructor->id, [
            'sort' => $sort,
            'direction' => 'asc',
            'per_page' => 50,
        ])->items())->pluck('id')->all();

        expect($actual)->toBe($ids);
    }

    $fallback = collect($panel->paginateClasses($instructor->id, [
        'sort' => 'name desc; drop table users',
        'direction' => 'asc',
        'per_page' => 50,
    ])->items())->pluck('id')->all();

    expect($fallback)->toBe([$alpha->id, $beta->id, $gamma->id]);
});

it('sorts instructor students class detail and assessments with stable scoped computed values', function () {
    [$instructor, $program] = sprintTwoContext();
    $otherInstructor = User::factory()->create(['role' => 'instructor']);
    $classZulu = sprintTwoClass($program, $instructor, 'Zulu Class');
    $classAlpha = sprintTwoClass($program, $instructor, 'Alpha Class');
    $foreignClass = sprintTwoClass($program, $otherInstructor, 'Foreign Class');
    $panel = app(InstructorPanelService::class);

    $notStarted = sprintTwoEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Alpha Student']),
        $classZulu,
        ['enrolled_at' => '2026-01-01', 'status' => 'active'],
    );
    $incomplete = sprintTwoEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Beta Student']),
        $classAlpha,
        ['enrolled_at' => '2026-01-02', 'status' => 'completed'],
    );
    $complete = sprintTwoEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Gamma Student']),
        $classZulu,
        ['enrolled_at' => '2026-01-03', 'status' => 'dropped'],
    );
    $published = sprintTwoEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Delta Student']),
        $classAlpha,
        ['enrolled_at' => '2026-01-04', 'status' => 'active'],
    );
    $foreign = sprintTwoEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Foreign Student']),
        $foreignClass,
    );

    sprintTwoReportCard($incomplete, $instructor, ['score_listening' => 10, 'total_score' => 10]);
    sprintTwoReportCard($complete, $instructor, [...sprintTwoCompleteAssessment(), 'total_score' => 70]);
    sprintTwoReportCard($published, $instructor, [
        ...sprintTwoCompleteAssessment(),
        'total_score' => 90,
        'is_published' => true,
    ]);
    sprintTwoReportCard($foreign, $otherInstructor, ['total_score' => 100]);

    $studentSorts = [
        'student' => [$notStarted->id, $incomplete->id, $published->id, $complete->id],
        'class' => [$incomplete->id, $published->id, $notStarted->id, $complete->id],
        'enrolled_at' => [$notStarted->id, $incomplete->id, $complete->id, $published->id],
        'status' => [$notStarted->id, $published->id, $incomplete->id, $complete->id],
        'assessment' => [$notStarted->id, $incomplete->id, $complete->id, $published->id],
    ];

    foreach ($studentSorts as $sort => $ids) {
        $actual = collect($panel->paginateStudents($instructor->id, [
            'sort' => $sort,
            'direction' => 'asc',
            'per_page' => 50,
        ])->items())->pluck('id')->all();

        expect($actual)->toBe($ids)->not->toContain($foreign->id);
    }

    $assessmentSorts = [
        'student' => [$notStarted->id, $incomplete->id, $published->id, $complete->id],
        'class' => [$incomplete->id, $published->id, $notStarted->id, $complete->id],
        'score' => [$notStarted->id, $incomplete->id, $complete->id, $published->id],
        'assessment' => [$notStarted->id, $incomplete->id, $complete->id, $published->id],
    ];

    foreach ($assessmentSorts as $sort => $ids) {
        $actual = collect($panel->paginateAssessments($instructor->id, [
            'sort' => $sort,
            'direction' => 'asc',
            'per_page' => 50,
        ])->items())->pluck('id')->all();

        expect($actual)->toBe($ids)->not->toContain($foreign->id);
    }

    $detail = collect($panel->paginateClassStudents($instructor->id, $classZulu, [
        'sort' => 'student',
        'direction' => 'asc',
        'per_page' => 50,
    ])->items())->pluck('id')->all();

    expect($detail)->toBe([$notStarted->id, $complete->id]);
});

it('renders sortable instructor tables and keeps assessment forms on project field wrappers', function () {
    [$instructor, $program] = sprintTwoContext();
    $class = sprintTwoClass($program, $instructor, 'Form Contract Class');
    $enrollment = sprintTwoEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Form Contract Student']),
        $class,
    );

    foreach ([
        route('instructor.classes.index') => ['sort=program', 'sort=schedule', 'sort=students'],
        route('instructor.students.index') => ['sort=student', 'sort=class', 'sort=assessment'],
        route('instructor.classes.show', $class) => ['sort=student', 'sort=enrolled_at', 'sort=assessment'],
        route('instructor.report-cards.index') => ['sort=student', 'sort=class', 'sort=score', 'sort=assessment'],
    ] as $url => $sortLinks) {
        $response = $this->actingAs($instructor)->get($url)->assertOk();
        $html = html_entity_decode($response->getContent());

        foreach ($sortLinks as $sortLink) {
            expect($html)->toContain($sortLink);
        }
    }

    $response = $this->actingAs($instructor)
        ->get(route('instructor.report-cards.create', $enrollment))
        ->assertOk()
        ->assertSee('name="score_listening"', false)
        ->assertSee('name="grade_pronunciation"', false)
        ->assertSee('name="final_grade"', false)
        ->assertDontSee('name="enrollment_id"', false)
        ->assertDontSee('name="instructor_id"', false)
        ->assertDontSee('name="academic_director_id"', false)
        ->assertDontSee('name="managing_director_id"', false)
        ->assertDontSee('name="pdf_path"', false)
        ->assertDontSee('name="issued_at"', false)
        ->assertDontSee('name="is_published"', false);

    $source = file_get_contents(resource_path('views/instructor/report-cards/form.blade.php'));

    expect($response->getContent())->toContain('fi-input')
        ->and($source)
        ->toContain('<x-ui.number-field')
        ->toContain('<x-ui.select')
        ->toContain('<x-ui.field')
        ->toContain('<x-ui.textarea')
        ->not->toContain('<input')
        ->not->toContain('<select')
        ->not->toContain('<textarea');
});

function sprintTwoContext(string $programName = 'Sprint Two Program'): array
{
    return [
        User::factory()->create(['role' => 'instructor', 'full_name' => 'Sprint Two Instructor']),
        sprintTwoProgram($programName),
    ];
}

function sprintTwoProgram(string $name): Program
{
    return Program::query()->create([
        'name' => $name,
        'slug' => str($name)->slug().'-'.str()->random(6),
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 750000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
}

function sprintTwoClass(
    Program $program,
    User $instructor,
    string $name,
    array $attributes = [],
): CourseClass {
    return CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => $name,
        'schedule_days' => 'Tuesday',
        'schedule_time' => '17:00',
        'room' => 'Room A',
        'start_date' => '2026-01-01',
        'end_date' => '2026-02-01',
        'status' => 'ongoing',
        ...$attributes,
    ]);
}

function sprintTwoEnrollment(
    User $student,
    CourseClass $class,
    array $attributes = [],
): Enrollment {
    return Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => '2026-01-01',
        'status' => 'active',
        ...$attributes,
    ]);
}

function sprintTwoReportCard(
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

function sprintTwoCompleteAssessment(): array
{
    return [
        'score_listening' => 14,
        'score_vocabulary' => 14,
        'score_structure' => 14,
        'score_reading' => 14,
        'score_writing' => 14,
        'grade_pronunciation' => 'A',
        'grade_sentence_arrangement' => 'A',
        'grade_class_participation' => 'A',
        'grade_questioning_skill' => 'A',
        'grade_analyzing_skill' => 'A',
        'final_grade' => 'A',
    ];
}

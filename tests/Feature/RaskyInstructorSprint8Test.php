<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('keeps every instructor route behind the instructor role middleware', function () {
    foreach ([
        'instructor.dashboard',
        'instructor.profile.show',
        'instructor.profile.update',
        'instructor.classes.index',
        'instructor.classes.show',
        'instructor.students.index',
        'instructor.report-cards.index',
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

it('protects all instructor read pages from guests and other roles', function () {
    [$instructor, $class, $enrollment, $reportCard] = sprintEightInstructorContext();
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student']);

    $urls = [
        route('instructor.dashboard'),
        route('instructor.profile.show'),
        route('instructor.classes.index'),
        route('instructor.classes.show', $class),
        route('instructor.students.index'),
        route('instructor.report-cards.index'),
        route('instructor.report-cards.show', $reportCard),
        route('instructor.report-cards.edit', $reportCard),
    ];

    foreach ($urls as $url) {
        $this->get($url)->assertRedirect(route('auth.login'));
    }

    foreach ($urls as $url) {
        $this->actingAs($admin)->get($url)->assertForbidden();
    }

    foreach ($urls as $url) {
        $this->actingAs($student)->get($url)->assertForbidden();
    }

    $reportCard->delete();
    $createUrl = route('instructor.report-cards.create', $enrollment);

    auth()->logout();
    $this->get($createUrl)->assertRedirect(route('auth.login'));
    $this->actingAs($admin)->get($createUrl)->assertForbidden();
    $this->actingAs($student)->get($createUrl)->assertForbidden();

    $this->actingAs($instructor)->get($createUrl)->assertOk();
});

it('keeps assessment states exclusive and removes published cards from pending work', function () {
    [$instructor, $class, $notStarted] = sprintEightInstructorContext(withAssessment: false);

    $notStarted->user->update(['full_name' => 'Not Started Student']);
    $incomplete = sprintEightEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Incomplete Draft Student']),
        $class,
    );
    $complete = sprintEightEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Complete Draft Student']),
        $class,
    );
    $publishedIncomplete = sprintEightEnrollment(
        User::factory()->create(['role' => 'student', 'full_name' => 'Published Incomplete Student']),
        $class,
    );

    sprintEightReportCard($incomplete, $instructor, ['score_listening' => 10]);
    sprintEightReportCard($complete, $instructor, sprintEightCompleteAssessment());
    sprintEightReportCard($publishedIncomplete, $instructor, [
        'score_listening' => 10,
        'is_published' => true,
    ]);

    $dashboard = $this->actingAs($instructor)
        ->get(route('instructor.dashboard'))
        ->assertOk()
        ->assertSee('Not Started Student')
        ->assertSee('Incomplete Draft Student')
        ->assertDontSee('Complete Draft Student')
        ->assertDontSee('Published Incomplete Student');

    expect($dashboard->viewData('stats')['incomplete_assessments'])->toBe(2);

    foreach ([
        'not_started' => [$notStarted->id],
        'incomplete' => [$incomplete->id],
        'complete' => [$complete->id],
        'published' => [$publishedIncomplete->id],
        'draft' => [$incomplete->id, $complete->id],
    ] as $status => $expectedIds) {
        $response = $this->actingAs($instructor)
            ->get(route('instructor.report-cards.index', ['assessment_status' => $status]))
            ->assertOk();

        expect(collect($response->viewData('assessments')->items())->pluck('id')->sort()->values()->all())
            ->toBe(collect($expectedIds)->sort()->values()->all());
    }
});

it('validates assessment boundaries calculates totals and ignores protected fields', function () {
    [$instructor, , $enrollment] = sprintEightInstructorContext(withAssessment: false);
    $otherInstructor = User::factory()->create(['role' => 'instructor']);
    $director = User::factory()->create(['role' => 'admin']);

    $this->actingAs($instructor)
        ->post(route('instructor.report-cards.store', $enrollment), [
            'score_listening' => -1,
            'score_vocabulary' => 21,
            'grade_pronunciation' => 'E',
            'final_grade' => 'Z',
        ])
        ->assertSessionHasErrors([
            'score_listening',
            'score_vocabulary',
            'grade_pronunciation',
            'final_grade',
        ]);

    $payload = [
        ...sprintEightCompleteAssessment(),
        'enrollment_id' => 999999,
        'instructor_id' => $otherInstructor->id,
        'academic_director_id' => $director->id,
        'managing_director_id' => $director->id,
        'total_score' => 1,
        'issued_at' => '2026-06-15',
        'pdf_path' => 'private/hijacked.pdf',
        'is_published' => true,
    ];

    $this->actingAs($instructor)
        ->post(route('instructor.report-cards.store', $enrollment), $payload)
        ->assertSessionHasNoErrors();

    $reportCard = ReportCard::query()->sole();

    expect($reportCard->enrollment_id)->toBe($enrollment->id)
        ->and($reportCard->instructor_id)->toBe($instructor->id)
        ->and($reportCard->total_score)->toBe(75)
        ->and($reportCard->academic_director_id)->toBeNull()
        ->and($reportCard->managing_director_id)->toBeNull()
        ->and($reportCard->issued_at)->toBeNull()
        ->and($reportCard->pdf_path)->toBeNull()
        ->and($reportCard->is_published)->toBeFalse();

    $this->actingAs($instructor)
        ->post(route('instructor.report-cards.store', $enrollment), sprintEightCompleteAssessment())
        ->assertStatus(409);
});

it('keeps reassigned and published assessments read only for the appropriate instructor', function () {
    [$originalInstructor, $class, , $reportCard] = sprintEightInstructorContext();
    $currentInstructor = User::factory()->create(['role' => 'instructor']);
    $unrelatedInstructor = User::factory()->create(['role' => 'instructor']);
    $class->update(['instructor_id' => $currentInstructor->id]);

    $this->actingAs($originalInstructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('Assessment hanya dapat ditinjau')
        ->assertDontSee('Edit Draft');

    $this->actingAs($originalInstructor)
        ->put(route('instructor.report-cards.update', $reportCard), sprintEightCompleteAssessment())
        ->assertForbidden();

    $this->actingAs($currentInstructor)
        ->get(route('instructor.report-cards.edit', $reportCard))
        ->assertOk();

    $this->actingAs($unrelatedInstructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertForbidden();

    $reportCard->update(['is_published' => true]);

    foreach ([$originalInstructor, $currentInstructor] as $viewer) {
        $this->actingAs($viewer)
            ->get(route('instructor.report-cards.show', $reportCard))
            ->assertOk()
            ->assertSee('Rapor sudah dipublish')
            ->assertDontSee('Edit Draft');

        $this->actingAs($viewer)
            ->put(route('instructor.report-cards.update', $reportCard), sprintEightCompleteAssessment())
            ->assertForbidden();
    }
});

it('renders responsive instructor markup and the source report card labels without sensitive data', function () {
    [$instructor, $class, , $reportCard] = sprintEightInstructorContext();
    $reportCard->update(sprintEightCompleteAssessment([
        'total_score' => 75,
        'next_class' => 'Teen 5',
        'comments' => 'Private academic feedback.',
    ]));
    $reportCard->enrollment->user->update([
        'nik' => '1371000000000001',
        'nisn' => '0011223344',
        'address' => 'Sensitive home address',
    ]);

    $dashboard = $this->actingAs($instructor)
        ->get(route('instructor.dashboard'))
        ->assertOk()
        ->assertSee('sm:grid-cols-2', false)
        ->assertSee('lg:grid-cols-3', false)
        ->assertSee('focus-visible:ring-2', false)
        ->assertSee('Profil Saya');

    $form = $this->actingAs($instructor)
        ->get(route('instructor.report-cards.edit', $reportCard))
        ->assertOk()
        ->assertSee('data-instructor-assessment-form', false)
        ->assertSee('Pronunciation Fluency')
        ->assertSee('Sentence and Word Arrangement')
        ->assertSee('w-full justify-center sm:w-auto', false);

    $review = $this->actingAs($instructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('15/20')
        ->assertSee('75/100')
        ->assertSee('Pronunciation Fluency')
        ->assertSee('Sentence and Word Arrangement');

    foreach ([
        route('instructor.students.index'),
        route('instructor.classes.show', $class),
        route('instructor.report-cards.index'),
        route('instructor.report-cards.show', $reportCard),
    ] as $url) {
        $this->actingAs($instructor)
            ->get($url)
            ->assertOk()
            ->assertDontSee('1371000000000001')
            ->assertDontSee('0011223344')
            ->assertDontSee('Sensitive home address')
            ->assertDontSee('payment_proof')
            ->assertDontSee('payment_gateway_id');
    }

    expect($dashboard->getContent())->toContain('data-dashboard-shell')
        ->and($form->getContent())->toContain('fi-input')
        ->and($review->getContent())->toContain('lg:grid-cols-2');
});

function sprintEightInstructorContext(bool $withAssessment = true): array
{
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Sprint Eight Instructor '.str()->random(5),
    ]);
    $program = Program::query()->create([
        'name' => 'Sprint Eight Program '.str()->random(5),
        'slug' => 'sprint-eight-program-'.str()->random(8),
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 750000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Sprint Eight Class '.str()->random(5),
        'schedule_days' => 'Tuesday and Thursday',
        'schedule_time' => '17:30',
        'status' => 'ongoing',
    ]);
    $student = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Sprint Eight Student '.str()->random(5),
    ]);
    $enrollment = sprintEightEnrollment($student, $class);
    $reportCard = $withAssessment
        ? sprintEightReportCard($enrollment, $instructor, sprintEightCompleteAssessment())
        : null;

    return [$instructor, $class, $enrollment, $reportCard];
}

function sprintEightEnrollment(User $student, CourseClass $class): Enrollment
{
    return Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => '2026-06-01',
        'status' => 'active',
    ]);
}

function sprintEightReportCard(
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

function sprintEightCompleteAssessment(array $overrides = []): array
{
    return [
        'score_listening' => 15,
        'score_vocabulary' => 15,
        'score_structure' => 15,
        'score_reading' => 15,
        'score_writing' => 15,
        'grade_pronunciation' => 'A',
        'grade_sentence_arrangement' => 'B',
        'grade_class_participation' => 'A',
        'grade_questioning_skill' => 'B',
        'grade_analyzing_skill' => 'A',
        'final_grade' => 'A',
        'next_class' => 'Teen 5',
        'comments' => 'Sprint eight assessment.',
        ...$overrides,
    ];
}

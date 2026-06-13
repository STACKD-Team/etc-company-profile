<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps classes and students scoped to the current instructor', function () {
    [$originalInstructor, $currentInstructor, $class, $enrollment, $reportCard] = sprintSixReassignedAssessment();

    $this->actingAs($originalInstructor)
        ->get(route('instructor.classes.index'))
        ->assertOk()
        ->assertDontSee($class->name);

    $this->actingAs($originalInstructor)
        ->get(route('instructor.students.index'))
        ->assertOk()
        ->assertDontSee($enrollment->user->full_name);

    $this->actingAs($currentInstructor)
        ->get(route('instructor.classes.index'))
        ->assertOk()
        ->assertSee($class->name);

    $this->actingAs($currentInstructor)
        ->get(route('instructor.students.index'))
        ->assertOk()
        ->assertSee($enrollment->user->full_name);
});

it('lets the creator review a reassigned assessment but only the current instructor edit it', function () {
    [$originalInstructor, $currentInstructor, , , $reportCard] = sprintSixReassignedAssessment();

    $this->actingAs($originalInstructor)
        ->get(route('instructor.report-cards.index'))
        ->assertOk()
        ->assertSee($reportCard->enrollment->user->full_name)
        ->assertSee(route('instructor.report-cards.show', $reportCard), false)
        ->assertDontSee(route('instructor.report-cards.edit', $reportCard), false);

    $this->actingAs($originalInstructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('Assessment hanya dapat ditinjau')
        ->assertDontSee('Edit Draft');

    $this->actingAs($originalInstructor)
        ->get(route('instructor.report-cards.edit', $reportCard))
        ->assertForbidden();

    $this->actingAs($currentInstructor)
        ->get(route('instructor.report-cards.index'))
        ->assertOk()
        ->assertSee(route('instructor.report-cards.edit', $reportCard), false);

    $this->actingAs($currentInstructor)
        ->put(route('instructor.report-cards.update', $reportCard), sprintSixAssessmentPayload([
            'comments' => 'Updated by the current instructor.',
        ]))
        ->assertRedirect(route('instructor.report-cards.show', $reportCard));

    expect($reportCard->refresh()->comments)->toBe('Updated by the current instructor.')
        ->and($reportCard->instructor_id)->toBe($originalInstructor->id);
});

it('shows assessments from a current class even when another instructor created them', function () {
    [$originalInstructor, $currentInstructor, , , $reportCard] = sprintSixReassignedAssessment();

    $this->actingAs($currentInstructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('Edit Draft');

    expect($reportCard->instructor_id)->toBe($originalInstructor->id);
});

it('keeps published assessments read only for every instructor', function () {
    [$originalInstructor, $currentInstructor, , , $reportCard] = sprintSixReassignedAssessment();
    $reportCard->update(['is_published' => true]);

    foreach ([$originalInstructor, $currentInstructor] as $instructor) {
        $this->actingAs($instructor)
            ->get(route('instructor.report-cards.show', $reportCard))
            ->assertOk()
            ->assertSee('Rapor sudah dipublish')
            ->assertDontSee('Edit Draft');

        $this->actingAs($instructor)
            ->get(route('instructor.report-cards.edit', $reportCard))
            ->assertForbidden();

        $this->actingAs($instructor)
            ->put(route('instructor.report-cards.update', $reportCard), sprintSixAssessmentPayload())
            ->assertForbidden();
    }
});

it('rejects unrelated instructors and protects assessment ownership fields', function () {
    [$originalInstructor, $currentInstructor, , $enrollment, $reportCard] = sprintSixReassignedAssessment();
    $unrelatedInstructor = User::factory()->create(['role' => 'instructor']);
    $director = User::factory()->create(['role' => 'admin']);

    $this->actingAs($unrelatedInstructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertForbidden();

    $this->actingAs($unrelatedInstructor)
        ->put(route('instructor.report-cards.update', $reportCard), sprintSixAssessmentPayload())
        ->assertForbidden();

    $this->actingAs($currentInstructor)
        ->put(route('instructor.report-cards.update', $reportCard), [
            ...sprintSixAssessmentPayload(),
            'enrollment_id' => 999999,
            'instructor_id' => $unrelatedInstructor->id,
            'academic_director_id' => $director->id,
            'managing_director_id' => $director->id,
            'issued_at' => '2026-06-13',
            'pdf_path' => 'private/hijacked.pdf',
            'is_published' => true,
        ])
        ->assertRedirect(route('instructor.report-cards.show', $reportCard));

    $reportCard->refresh();

    expect($reportCard->enrollment_id)->toBe($enrollment->id)
        ->and($reportCard->instructor_id)->toBe($originalInstructor->id)
        ->and($reportCard->academic_director_id)->toBeNull()
        ->and($reportCard->managing_director_id)->toBeNull()
        ->and($reportCard->issued_at)->toBeNull()
        ->and($reportCard->pdf_path)->toBeNull()
        ->and($reportCard->is_published)->toBeFalse();
});

it('includes reassigned creator records in assessment filters without leaking unrelated data', function () {
    [$originalInstructor, , $class, $enrollment] = sprintSixReassignedAssessment();
    [$unrelatedInstructor, , $unrelatedClass, $unrelatedEnrollment] = sprintSixReassignedAssessment();

    $response = $this->actingAs($originalInstructor)
        ->get(route('instructor.report-cards.index', [
            'class_id' => $class->id,
            'student_id' => $enrollment->user_id,
        ]))
        ->assertOk()
        ->assertSee($class->name)
        ->assertSee($enrollment->user->full_name)
        ->assertDontSee($unrelatedClass->name)
        ->assertDontSee($unrelatedEnrollment->user->full_name);

    expect($response->viewData('classOptions'))->toHaveKey($class->id)
        ->not->toHaveKey($unrelatedClass->id)
        ->and($response->viewData('studentOptions'))->toHaveKey($enrollment->user_id)
        ->not->toHaveKey($unrelatedEnrollment->user_id)
        ->and($unrelatedInstructor->id)->not->toBe($originalInstructor->id);
});

function sprintSixReassignedAssessment(): array
{
    $originalInstructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Original Instructor '.str()->random(5),
    ]);
    $currentInstructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Current Instructor '.str()->random(5),
    ]);
    $program = Program::query()->create([
        'name' => 'Sprint Six Program '.str()->random(5),
        'slug' => 'sprint-six-program-'.str()->random(8),
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 750000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $currentInstructor->id,
        'name' => 'Reassigned Class '.str()->random(5),
        'status' => 'ongoing',
    ]);
    $student = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Sprint Six Student '.str()->random(5),
    ]);
    $enrollment = Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => now()->toDateString(),
        'status' => 'active',
    ]);
    $reportCard = ReportCard::query()->create([
        ...sprintSixAssessmentPayload(),
        'enrollment_id' => $enrollment->id,
        'instructor_id' => $originalInstructor->id,
        'total_score' => 75,
        'is_published' => false,
    ]);

    return [$originalInstructor, $currentInstructor, $class, $enrollment, $reportCard];
}

function sprintSixAssessmentPayload(array $overrides = []): array
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
        'comments' => 'Sprint six assessment.',
        ...$overrides,
    ];
}

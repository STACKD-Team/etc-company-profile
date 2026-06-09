<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('registers instructor routes with instructor middleware', function () {
    foreach ([
        'instructor.dashboard',
        'instructor.classes.index',
        'instructor.classes.show',
        'instructor.students.index',
        'instructor.report-cards.index',
    ] as $routeName) {
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull()
            ->and($route->gatherMiddleware())->toContain('auth')
            ->and($route->gatherMiddleware())->toContain('role:instructor');
    }
});

it('protects instructor pages from guests and other roles', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->get(route('instructor.dashboard'))
        ->assertRedirect(route('auth.login'));

    $this->actingAs($student)
        ->get(route('instructor.dashboard'))
        ->assertForbidden();
});

it('isolates classes students and report cards between instructors', function () {
    $firstInstructor = User::factory()->create(['role' => 'instructor']);
    $secondInstructor = User::factory()->create(['role' => 'instructor']);
    $program = createInstructorProgram();

    $firstClass = createInstructorClass($program, $firstInstructor, 'Rasky Class Alpha');
    $secondClass = createInstructorClass($program, $secondInstructor, 'Hidden Class Beta');

    $firstStudent = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Visible Student Alpha',
    ]);
    $secondStudent = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Hidden Student Beta',
    ]);

    $firstEnrollment = createInstructorEnrollment($firstStudent, $firstClass);
    $secondEnrollment = createInstructorEnrollment($secondStudent, $secondClass);

    createInstructorReportCard($firstEnrollment, $firstInstructor, 'Visible assessment');
    createInstructorReportCard($secondEnrollment, $secondInstructor, 'Hidden assessment');

    $this->actingAs($firstInstructor)
        ->get(route('instructor.classes.index'))
        ->assertOk()
        ->assertSee('Rasky Class Alpha')
        ->assertDontSee('Hidden Class Beta');

    $this->actingAs($firstInstructor)
        ->get(route('instructor.students.index'))
        ->assertOk()
        ->assertSee('Visible Student Alpha')
        ->assertDontSee('Hidden Student Beta');

    $this->actingAs($firstInstructor)
        ->get(route('instructor.report-cards.index'))
        ->assertOk()
        ->assertSee('Visible Student Alpha')
        ->assertDontSee('Hidden Student Beta');

    $this->actingAs($firstInstructor)
        ->get(route('instructor.classes.show', $secondClass))
        ->assertForbidden();
});

it('renders all instructor pages for the assigned instructor', function () {
    $instructor = User::factory()->create(['role' => 'instructor']);
    $program = createInstructorProgram();
    $class = createInstructorClass($program, $instructor, 'Foundation Class');

    foreach ([
        route('instructor.dashboard'),
        route('instructor.classes.index'),
        route('instructor.classes.show', $class),
        route('instructor.students.index'),
        route('instructor.report-cards.index'),
    ] as $url) {
        $this->actingAs($instructor)->get($url)->assertOk();
    }
});

function createInstructorProgram(): Program
{
    return Program::query()->create([
        'name' => 'Instructor Foundation Program',
        'slug' => 'instructor-foundation-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 750000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
}

function createInstructorClass(
    Program $program,
    User $instructor,
    string $name,
): CourseClass {
    return CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => $name,
        'status' => 'ongoing',
    ]);
}

function createInstructorEnrollment(
    User $student,
    CourseClass $class,
): Enrollment {
    return Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => now()->toDateString(),
        'status' => 'active',
    ]);
}

function createInstructorReportCard(
    Enrollment $enrollment,
    User $instructor,
    string $comments,
): ReportCard {
    return ReportCard::query()->create([
        'enrollment_id' => $enrollment->id,
        'instructor_id' => $instructor->id,
        'comments' => $comments,
        'is_published' => false,
    ]);
}

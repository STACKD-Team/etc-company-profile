<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('registers the singular instructor routes with stable names and middleware', function () {
    $routes = [
        'instructor.dashboard' => ['GET', 'instructor/dashboard'],
        'instructor.profile.show' => ['GET', 'instructor/profile'],
        'instructor.profile.update' => ['PUT', 'instructor/profile'],
        'instructor.classes.index' => ['GET', 'instructor/class'],
        'instructor.classes.show' => ['GET', 'instructor/class/{class}'],
        'instructor.students.index' => ['GET', 'instructor/student'],
        'instructor.report-cards.index' => ['GET', 'instructor/report-card'],
        'instructor.report-cards.create' => ['GET', 'instructor/enrollment/{enrollment}/report-card/create'],
        'instructor.report-cards.store' => ['POST', 'instructor/enrollment/{enrollment}/report-card'],
        'instructor.report-cards.show' => ['GET', 'instructor/report-card/{reportCard}'],
        'instructor.report-cards.edit' => ['GET', 'instructor/report-card/{reportCard}/edit'],
        'instructor.report-cards.update' => ['PUT', 'instructor/report-card/{reportCard}'],
    ];

    foreach ($routes as $name => [$method, $uri]) {
        $route = Route::getRoutes()->getByName($name);

        expect($route)->not->toBeNull()
            ->and($route->uri())->toBe($uri)
            ->and($route->methods())->toContain($method)
            ->and($route->gatherMiddleware())->toContain('auth')
            ->and($route->gatherMiddleware())->toContain('role:instructor');
    }
});

it('renders every instructor controller from role-specific page views', function () {
    [$instructor, $class, $enrollment, $reportCard] = sprintThreeInstructorContext();

    $pages = [
        [route('instructor.dashboard'), 'pages.instructor.dashboard.index'],
        [route('instructor.profile.show'), 'pages.instructor.profile.show'],
        [route('instructor.classes.index'), 'pages.instructor.class.index'],
        [route('instructor.classes.show', $class), 'pages.instructor.class.show'],
        [route('instructor.students.index'), 'pages.instructor.student.index'],
        [route('instructor.report-cards.index'), 'pages.instructor.report-card.index'],
        [route('instructor.report-cards.show', $reportCard), 'pages.instructor.report-card.show'],
        [route('instructor.report-cards.edit', $reportCard), 'pages.instructor.report-card.edit'],
    ];

    foreach ($pages as [$url, $view]) {
        $this->actingAs($instructor)
            ->get($url)
            ->assertOk()
            ->assertViewIs($view);
    }

    $reportCard->delete();

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.create', $enrollment))
        ->assertOk()
        ->assertViewIs('pages.instructor.report-card.create');
});

it('allows instructors to update only their editable profile fields', function () {
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'is_active' => true,
        'show_on_team_page' => false,
        'avatar' => 'avatars/original.jpg',
    ]);

    $this->actingAs($instructor)
        ->put(route('instructor.profile.update'), [
            'full_name' => 'Rasky Instructor',
            'mobile_phone' => '081234567890',
            'instructor_position' => 'Senior Instructor',
            'instructor_specialization' => 'English Conversation',
            'instructor_bio' => 'Instructor profile updated through the instructor panel.',
            'email' => 'hijacked@example.com',
            'role' => 'admin',
            'is_active' => false,
            'avatar' => 'avatars/hijacked.jpg',
            'show_on_team_page' => true,
        ])
        ->assertRedirect(route('instructor.profile.show'))
        ->assertSessionHasNoErrors();

    $instructor->refresh();

    expect($instructor->full_name)->toBe('Rasky Instructor')
        ->and($instructor->mobile_phone)->toBe('081234567890')
        ->and($instructor->instructor_position)->toBe('Senior Instructor')
        ->and($instructor->instructor_specialization)->toBe('English Conversation')
        ->and($instructor->role)->toBe('instructor')
        ->and($instructor->is_active)->toBeTrue()
        ->and($instructor->avatar)->toBe('avatars/original.jpg')
        ->and($instructor->show_on_team_page)->toBeFalse()
        ->and($instructor->email)->not->toBe('hijacked@example.com');
});

it('validates instructor profile fields and protects profile routes by role', function () {
    $instructor = User::factory()->create(['role' => 'instructor']);
    $student = User::factory()->create(['role' => 'student']);

    $this->get(route('instructor.profile.show'))
        ->assertRedirect(route('auth.login'));

    $this->actingAs($student)
        ->get(route('instructor.profile.show'))
        ->assertForbidden();

    $this->actingAs($instructor)
        ->from(route('instructor.profile.show'))
        ->put(route('instructor.profile.update'), [
            'full_name' => str_repeat('a', 151),
            'mobile_phone' => str_repeat('1', 21),
            'instructor_position' => str_repeat('a', 101),
            'instructor_specialization' => str_repeat('a', 101),
            'instructor_bio' => str_repeat('a', 2001),
        ])
        ->assertRedirect(route('instructor.profile.show'))
        ->assertSessionHasErrors([
            'full_name',
            'mobile_phone',
            'instructor_position',
            'instructor_specialization',
            'instructor_bio',
        ]);
});

it('keeps instructor data isolated after the singular route migration', function () {
    [$instructor, $class, $enrollment, $reportCard] = sprintThreeInstructorContext();
    [$otherInstructor, $otherClass, $otherEnrollment, $otherReportCard] = sprintThreeInstructorContext();

    $this->actingAs($instructor)
        ->get(route('instructor.classes.show', $otherClass))
        ->assertForbidden();

    foreach ([
        route('instructor.report-cards.create', $otherEnrollment),
        route('instructor.report-cards.show', $otherReportCard),
        route('instructor.report-cards.edit', $otherReportCard),
    ] as $url) {
        $this->actingAs($instructor)->get($url)->assertForbidden();
    }

    $this->actingAs($instructor)
        ->get(route('instructor.classes.show', $class))
        ->assertOk();

    $this->actingAs($instructor)
        ->get(route('instructor.report-cards.show', $reportCard))
        ->assertOk();

    expect($enrollment->courseClass->instructor_id)->toBe($instructor->id)
        ->and($otherEnrollment->courseClass->instructor_id)->toBe($otherInstructor->id);
});

function sprintThreeInstructorContext(): array
{
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'full_name' => 'Sprint Three Instructor '.str()->random(5),
    ]);

    $program = Program::query()->create([
        'name' => 'Sprint Three Program '.str()->random(5),
        'slug' => 'sprint-three-program-'.str()->random(8),
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
        'name' => 'Sprint Three Class '.str()->random(5),
        'status' => 'ongoing',
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Sprint Three Student '.str()->random(5),
    ]);

    $enrollment = Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => now()->toDateString(),
        'status' => 'active',
    ]);

    $reportCard = ReportCard::query()->create([
        'enrollment_id' => $enrollment->id,
        'instructor_id' => $instructor->id,
        'is_published' => false,
    ]);

    return [$instructor, $class, $enrollment, $reportCard];
}

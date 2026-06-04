<?php

use App\Models\Program;
use App\Models\CourseClass;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('registers the implemented Rasky web route names after sprint merge', function () {
    $expectedRoutes = [
        'public.programs.show',
        'auth.login',
        'auth.login.store',
        'auth.logout',
        'auth.password.request',
        'auth.password.email',
        'auth.password.reset',
        'auth.password.update',
        'admin.dashboard',
        'admin.placement-tests.index',
        'admin.placement-tests.show',
        'admin.placement-tests.schedule',
        'admin.placement-tests.result.store',
        'admin.report-cards.index',
        'admin.report-cards.create',
        'admin.report-cards.store',
        'admin.report-cards.show',
        'admin.report-cards.edit',
        'admin.report-cards.update',
        'admin.report-cards.publish',
        'admin.exports.students',
        'admin.exports.students.download',
        'admin.exports.report-cards',
        'admin.exports.report-cards.download',
        'instructor.dashboard',
        'instructor.classes.index',
        'instructor.classes.show',
        'instructor.students.index',
        'instructor.report-cards.index',
    ];

    foreach ($expectedRoutes as $routeName) {
        expect(Route::has($routeName))->toBeTrue($routeName);
    }

    expect(Route::has('public.home'))->toBeTrue()
        ->and(Route::has('public.programs.index'))->toBeTrue();
});

it('resolves public program detail by slug route model binding', function () {
    $this->seed();

    $this->get('/programs/english-conversation')
        ->assertOk()
        ->assertSee('English Conversation')
        ->assertSee('Yang Akan Kamu Pelajari')
        ->assertSee('Jadwal Kelas')
        ->assertSee('Sarah Amalia, M.Pd.')
        ->assertSee('Investasi belajar');
});

it('returns not found for inactive program details', function () {
    Program::query()->create([
        'name' => 'Private Draft',
        'slug' => 'private-draft',
        'category' => 'english',
        'type' => 'private',
        'target_age' => 'all',
        'price' => 500000,
        'registration_fee' => 200000,
        'is_active' => false,
    ]);

    $this->get('/programs/private-draft')->assertNotFound();
});

it('renders the auth login page', function () {
    $this->get(route('auth.login'))
        ->assertOk()
        ->assertSee('Masuk ke dashboard ETC Planet');
});

it('renders the Rasky password screens', function () {
    $this->get(route('auth.password.request'))
        ->assertOk()
        ->assertSee('Lupa password');

    $this->get(route('auth.password.reset', ['token' => 'test-token']))
        ->assertOk()
        ->assertSee('Reset password');
});

it('redirects guests away from the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('auth.login'));
});

it('forbids non admin users from the admin dashboard', function () {
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $this->actingAs($student)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('allows an admin to login and reach the admin dashboard', function () {
    $this->seed();

    $this->post(route('auth.login.store'), [
        'email' => 'admin@etcplanet.test',
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticated();

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Admin');
});

it('redirects student users to the student dashboard after login', function () {
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $this->post(route('auth.login.store'), [
        'email' => $student->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('student.dashboard'));

    $this->assertAuthenticatedAs($student);
});

it('redirects instructor users to the instructor dashboard after login', function () {
    $instructor = User::factory()->create([
        'role' => 'instructor',
    ]);

    $this->post(route('auth.login.store'), [
        'email' => $instructor->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('instructor.dashboard'));

    $this->assertAuthenticatedAs($instructor);
});

it('logs users out and redirects to the public home page', function () {
    $user = User::factory()->create([
        'role' => 'student',
    ]);

    $this->actingAs($user)
        ->post(route('auth.logout'))
        ->assertRedirect(route('public.home'));

    $this->assertGuest();
});

it('allows admin users to open Rasky admin GET pages', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    foreach ([
        route('admin.dashboard'),
        route('admin.placement-tests.index'),
        route('admin.report-cards.index'),
        route('admin.report-cards.create'),
        route('admin.exports.students'),
        route('admin.exports.report-cards'),
    ] as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }
});

it('renders the Rasky placement detail workflow forms for admin users', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    [$registration, $courseClass] = createRaskyPlacementFixture();

    $this->actingAs($admin)
        ->get(route('admin.placement-tests.show', $registration))
        ->assertOk()
        ->assertSee('Jadwal Placement Test')
        ->assertSee('Hasil dan Rekomendasi Kelas')
        ->assertSee($courseClass->name)
        ->assertSee(route('admin.placement-tests.schedule', $registration), false)
        ->assertSee(route('admin.placement-tests.result.store', $registration), false);
});

it('protects the Rasky placement workflow from guests and non admin users', function () {
    [$registration] = createRaskyPlacementFixture();
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $this->get(route('admin.placement-tests.show', $registration))
        ->assertRedirect(route('auth.login'));

    $this->post(route('admin.placement-tests.schedule', $registration), [
        'placement_test_at' => '2026-05-20 10:00:00',
    ])->assertRedirect(route('auth.login'));

    $this->actingAs($student)
        ->get(route('admin.placement-tests.show', $registration))
        ->assertForbidden();

    $this->actingAs($student)
        ->post(route('admin.placement-tests.result.store', $registration), [
            'placement_test_result' => 'Ready for Teen 4.',
        ])
        ->assertForbidden();
});

it('allows admin users to schedule a placement test', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    [$registration] = createRaskyPlacementFixture();

    $this->actingAs($admin)
        ->post(route('admin.placement-tests.schedule', $registration), [
            'placement_test_at' => '2026-05-20 10:00:00',
        ])
        ->assertRedirect(route('admin.placement-tests.show', $registration))
        ->assertSessionHasNoErrors();

    $registration->refresh();

    expect($registration->status)->toBe('placement_test')
        ->and($registration->placement_test_at?->format('Y-m-d H:i:s'))->toBe('2026-05-20 10:00:00');
});

it('validates placement test schedule input', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    [$registration] = createRaskyPlacementFixture();

    $this->actingAs($admin)
        ->from(route('admin.placement-tests.show', $registration))
        ->post(route('admin.placement-tests.schedule', $registration), [])
        ->assertRedirect(route('admin.placement-tests.show', $registration))
        ->assertSessionHasErrors(['placement_test_at']);
});

it('allows admin users to store placement result and assign a class without creating enrollment', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    [$registration, $courseClass] = createRaskyPlacementFixture();

    $this->actingAs($admin)
        ->post(route('admin.placement-tests.result.store', $registration), [
            'placement_test_result' => 'Student is ready for Teen 4.',
            'class_id' => $courseClass->id,
        ])
        ->assertRedirect(route('admin.placement-tests.show', $registration))
        ->assertSessionHasNoErrors();

    $registration->refresh();

    expect($registration->placement_test_result)->toBe('Student is ready for Teen 4.')
        ->and($registration->class_id)->toBe($courseClass->id)
        ->and($registration->status)->toBe('enrolled')
        ->and(\App\Models\Enrollment::query()->where('user_id', $registration->user_id)->where('class_id', $courseClass->id)->exists())->toBeFalse();
});

it('validates placement test result input', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    [$registration] = createRaskyPlacementFixture();

    $this->actingAs($admin)
        ->from(route('admin.placement-tests.show', $registration))
        ->post(route('admin.placement-tests.result.store', $registration), [
            'placement_test_result' => '',
            'class_id' => 999999,
        ])
        ->assertRedirect(route('admin.placement-tests.show', $registration))
        ->assertSessionHasErrors(['placement_test_result', 'class_id']);
});

it('allows instructor users to open Rasky instructor GET pages', function () {
    $instructor = User::factory()->create([
        'role' => 'instructor',
    ]);

    foreach ([
        route('instructor.dashboard'),
        route('instructor.classes.index'),
        route('instructor.students.index'),
        route('instructor.report-cards.index'),
    ] as $url) {
        $this->actingAs($instructor)->get($url)->assertOk();
    }
});

function createRaskyPlacementFixture(): array
{
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $program = Program::query()->create([
        'name' => 'English Conversation Teen',
        'slug' => 'english-conversation-teen',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 350000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $courseClass = CourseClass::query()->create([
        'program_id' => $program->id,
        'name' => 'Teen 4',
        'schedule_days' => 'Tuesday and Thursday',
        'schedule_time' => '17:30 - 19:00',
        'status' => 'upcoming',
    ]);

    $registration = Registration::query()->create([
        'registration_code' => 'REG-TEST-PLACEMENT',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'applicant_name' => 'Budi Placement',
        'applicant_email' => 'budi.placement@example.test',
        'applicant_phone' => '08123456789',
        'payment_amount' => 550000,
        'paid_at' => now(),
        'status' => 'paid',
    ]);

    return [$registration, $courseClass, $program, $student];
}

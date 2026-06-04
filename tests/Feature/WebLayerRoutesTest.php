<?php

use App\Models\Program;
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

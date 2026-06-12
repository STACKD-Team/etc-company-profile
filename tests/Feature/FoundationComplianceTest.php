<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('uses the documented public home route', function () {
    $route = Route::getRoutes()->getByName('public.home');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('/')
        ->and($route->getActionName())->toBe('App\Http\Controllers\Public\HomeController@index');
});

it('registers merged sprint route names from all teammates', function () {
    foreach ([
        'auth.login',
        'student.dashboard',
        'instructor.dashboard',
        'admin.dashboard',
        'public.programs.index',
    ] as $routeName) {
        expect(Route::has($routeName))->toBeTrue($routeName);
    }

    expect(Route::has('registrations.start'))->toBeTrue();
});

it('keeps admin content routes protected for admins', function () {
    $route = Route::getRoutes()->getByName('admin.reels.index');

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toContain('auth')
        ->and($route->gatherMiddleware())->toContain('role:admin');

    $this->get(route('admin.reels.index', absolute: false))
        ->assertRedirect('/login');

    $this->get('/admin/reels')
        ->assertRedirect('/admin/login');
});

it('rejects authenticated users with the wrong role', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->actingAs($student)
        ->get(route('admin.reels.index', absolute: false))
        ->assertForbidden();
});

it('keeps public post routes behind validation and throttle middleware', function () {
    $route = Route::getRoutes()->getByName('public.contact.store');

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toContain('throttle:contact');

    $this->post('/contact', [])
        ->assertSessionHasErrors(['name', 'email', 'message']);
});

<?php

use App\Models\Reel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('keeps the complete Miftah public route contract unchanged', function () {
    $routes = [
        'public.home' => ['GET', '/'],
        'public.about' => ['GET', 'about'],
        'public.team.index' => ['GET', 'team'],
        'public.facilities.index' => ['GET', 'facilities'],
        'public.gallery.index' => ['GET', 'gallery'],
        'public.contact.index' => ['GET', 'contact'],
        'public.contact.store' => ['POST', 'contact'],
        'public.faq.index' => ['GET', 'faq'],
        'public.chatbot.messages.store' => ['POST', 'chatbot/messages'],
        'public.reels.index' => ['GET', 'reels'],
        'public.reels.show' => ['GET', 'reels/{reel}'],
        'public.reels.views.store' => ['POST', 'reels/{reel}/views'],
        'public.reels.likes.store' => ['POST', 'reels/{reel}/likes'],
        'public.programs.index' => ['GET', 'programs'],
        'public.programs.show' => ['GET', 'programs/{program}'],
    ];

    foreach ($routes as $name => [$method, $uri]) {
        $route = Route::getRoutes()->getByName($name);

        expect($route)->not->toBeNull($name)
            ->and($route->methods())->toContain($method)
            ->and($route->uri())->toBe($uri);
    }
});

it('maps Miftah page controllers to the pages public namespace', function () {
    $controllers = [
        'HomeController.php' => "view('pages.public.home'",
        'AboutController.php' => "view('pages.public.about'",
        'TeamController.php' => "view('pages.public.team.index'",
        'FacilityController.php' => "view('pages.public.facilities.index'",
        'GalleryController.php' => "view('pages.public.gallery.index'",
        'ContactController.php' => "view('pages.public.contact.index'",
        'FaqController.php' => "view('pages.public.faq.index'",
        'ProgramController.php' => [
            "view('pages.public.programs.index'",
            "view('pages.public.programs.show'",
        ],
        'ReelController.php' => "view('pages.public.reels.index'",
    ];

    foreach ($controllers as $controller => $expectedViews) {
        $source = file_get_contents(app_path("Http/Controllers/Public/{$controller}"));

        foreach ((array) $expectedViews as $expectedView) {
            expect($source)->toContain($expectedView);
        }
    }

    expect(file_get_contents(app_path('Http/Controllers/Controller.php')))
        ->toContain("view('pages.public.home')");
});

it('stores every Miftah page under pages public and removes the old entry points', function () {
    $views = [
        'home.blade.php',
        'about.blade.php',
        'team/index.blade.php',
        'facilities/index.blade.php',
        'gallery/index.blade.php',
        'contact/index.blade.php',
        'faq/index.blade.php',
        'programs/index.blade.php',
        'programs/show.blade.php',
        'reels/index.blade.php',
    ];

    foreach ($views as $view) {
        expect(resource_path("views/pages/public/{$view}"))->toBeFile()
            ->and(resource_path("views/public/{$view}"))->not->toBeFile()
            ->and(file_get_contents(resource_path("views/pages/public/{$view}")))
            ->toContain('<x-layouts.public');
    }

    expect(resource_path('views/public/registration/create.blade.php'))->toBeFile();
});

it('opens a published reel in the selected feed and rejects a draft reel', function () {
    $published = Reel::query()->create([
        'title' => 'Sprint 3 Selected Reel',
        'video_path' => 'videos/sprint-3-selected.mp4',
        'is_published' => true,
        'published_at' => now(),
    ]);
    $draft = Reel::query()->create([
        'title' => 'Sprint 3 Draft Reel',
        'video_path' => 'videos/sprint-3-draft.mp4',
        'is_published' => false,
    ]);

    $this->get(route('public.reels.show', $published))
        ->assertRedirect(route('public.reels.index', ['reel' => $published->getKey()]));

    $this->get(route('public.reels.index', ['reel' => $published->getKey()]))
        ->assertOk()
        ->assertSee('data-reel-id="'.$published->getKey().'"', false)
        ->assertSee($published->title);

    $this->get(route('public.reels.show', $draft))->assertNotFound();
});

it('keeps public mutations throttled after source alignment', function () {
    foreach ([
        'public.contact.store' => 'throttle:contact',
        'public.chatbot.messages.store' => 'throttle:chatbot',
        'public.reels.views.store' => 'throttle:reels',
        'public.reels.likes.store' => 'throttle:reels',
    ] as $routeName => $middleware) {
        expect(Route::getRoutes()->getByName($routeName)?->gatherMiddleware())
            ->toContain($middleware);
    }
});

it('keeps both payment snapshots without duplicate shared columns', function () {
    expect(Schema::hasColumns('registrations', [
        'payment_status',
        'payment_expires_at',
        'payment_original_amount',
        'payment_final_amount',
        'midtrans_order_id',
        'midtrans_snap_token',
        'original_amount',
        'final_amount',
    ]))->toBeTrue();
});

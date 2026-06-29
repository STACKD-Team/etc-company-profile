<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ChatbotController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\FacilityController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProgramController;
use App\Http\Controllers\Public\ReelController;
use App\Http\Controllers\Public\ReelLikeController;
use App\Http\Controllers\Public\ReelViewController;
use App\Http\Controllers\Public\TeamController;
use App\Models\Program;
use App\Models\Reel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('keeps Sprint 4 admin operations outside the Miftah ownership boundary', function () {
    $source = file_get_contents(base_path('context/PEMBAGIAN_TUGAS_DEVELOPER_ETC.md'));
    $sprintFour = Str::between(
        $source,
        '### Sprint 4 - Admin Operational CRUD/RD Flow',
        '### Sprint 5 - Rooms, CMS Simplification, dan Schema Cleanup',
    );
    $miftahScope = Str::between(
        $source,
        '## 7. Detail Tugas Miftah - Public Discovery',
        '## 8. Detail Tugas Mia - Admin Panel, Integrasi, dan Operasional',
    );
    $miaScope = Str::between(
        $source,
        '## 8. Detail Tugas Mia - Admin Panel, Integrasi, dan Operasional',
        '## 9. Detail Tugas Mecca - Student Panel',
    );

    expect($source)
        ->toContain('| Miftah | Public Discovery |')
        ->toContain('| Mia | Admin Panel |')
        ->and($sprintFour)
        ->toContain('Scope admin:')
        ->toContain('CRUD: instructor, student, registration')
        ->toContain('RD: payment, contact message, chatbot log')
        ->and($miftahScope)
        ->toContain('- `public.home`')
        ->toContain('- `public.programs.show`')
        ->not->toContain('- `admin.')
        ->and($miaScope)
        ->toContain('- Admin panel')
        ->toContain('- `admin.reels.*`')
        ->toContain('- `admin.contact-messages.*`')
        ->toContain('- `admin.chatbot-logs.*`')
        ->toContain('- `admin.settings.*`');
});

it('keeps all 15 Miftah public routes on their documented contract', function () {
    $routes = [
        'public.home' => ['GET', '/', HomeController::class.'@index'],
        'public.about' => ['GET', 'about', AboutController::class.'@index'],
        'public.team.index' => ['GET', 'team', TeamController::class.'@index'],
        'public.facilities.index' => ['GET', 'facilities', FacilityController::class.'@index'],
        'public.gallery.index' => ['GET', 'gallery', GalleryController::class.'@index'],
        'public.contact.index' => ['GET', 'contact', ContactController::class.'@index'],
        'public.contact.store' => ['POST', 'contact', ContactController::class.'@store'],
        'public.faq.index' => ['GET', 'faq', FaqController::class.'@index'],
        'public.chatbot.messages.store' => ['POST', 'chatbot/messages', ChatbotController::class.'@store'],
        'public.programs.index' => ['GET', 'programs', ProgramController::class.'@index'],
        'public.programs.show' => ['GET', 'programs/{program}', ProgramController::class.'@show'],
        'public.reels.index' => ['GET', 'reels', ReelController::class.'@index'],
        'public.reels.show' => ['GET', 'reels/{reel}', ReelController::class.'@show'],
        'public.reels.views.store' => ['POST', 'reels/{reel}/views', ReelViewController::class.'@store'],
        'public.reels.likes.store' => ['POST', 'reels/{reel}/likes', ReelLikeController::class.'@store'],
    ];

    expect(collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route): bool => Str::startsWith((string) $route->getName(), 'public.'))
        ->count())->toBe(15);

    foreach ($routes as $name => [$method, $uri, $action]) {
        $route = Route::getRoutes()->getByName($name);

        expect($route)->not->toBeNull($name)
            ->and($route->methods())->toContain($method)
            ->and($route->uri())->toBe($uri)
            ->and($route->getActionName())->toBe($action);
    }
});

it('keeps every public mutation throttled at the ownership boundary', function () {
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

it('renders the complete Miftah public discovery surface for Sprint 4', function () {
    $program = Program::query()->create([
        'name' => 'Sprint 4 Public Program',
        'slug' => 'sprint-4-public-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Program untuk regression test batas ownership Sprint 4.',
        'duration_meetings' => 12,
        'max_students' => 10,
        'price' => 1500000,
        'registration_fee' => 150000,
        'is_active' => true,
    ]);
    $reel = Reel::query()->create([
        'title' => 'Sprint 4 Public Reel',
        'description' => 'Reel untuk regression test batas ownership Sprint 4.',
        'video_path' => 'videos/sprint-4.mp4',
        'thumbnail_path' => 'images/sprint-4.jpg',
        'category' => 'edukasi',
        'is_published' => true,
        'published_at' => now(),
    ]);

    foreach ([
        route('public.home'),
        route('public.about'),
        route('public.team.index'),
        route('public.facilities.index'),
        route('public.gallery.index'),
        route('public.contact.index'),
        route('public.faq.index'),
        route('public.programs.index'),
        route('public.programs.show', $program),
        route('public.reels.index'),
    ] as $url) {
        $this->get($url)->assertOk();
    }

    $this->get(route('public.reels.show', $reel))
        ->assertRedirect(route('public.reels.index', ['reel' => $reel->getKey()]));
});

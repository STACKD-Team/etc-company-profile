<?php

use App\Models\ChatbotLog;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Program;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

function miftahSprint0Program(array $overrides = []): Program
{
    return Program::query()->create(array_merge([
        'name' => 'General English Sprint 0',
        'slug' => 'general-english-sprint-0',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Program komunikasi bahasa Inggris untuk remaja.',
        'duration_meetings' => 20,
        'max_students' => 12,
        'price' => 1500000,
        'registration_fee' => 200000,
        'is_active' => true,
    ], $overrides));
}

function miftahSprint0Reel(array $overrides = []): Reel
{
    return Reel::query()->create(array_merge([
        'title' => 'Sprint 0 Published Reel',
        'description' => 'Kegiatan kelas ETC Planet.',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'category' => 'edukasi',
        'views_count' => 0,
        'likes_count' => 0,
        'is_published' => true,
        'published_at' => now(),
    ], $overrides));
}

it('registers the complete Miftah public discovery route contract', function () {
    $routes = [
        'public.home' => ['GET', '/', 'App\Http\Controllers\Public\HomeController@index'],
        'public.about' => ['GET', 'about', 'App\Http\Controllers\Public\AboutController@index'],
        'public.team.index' => ['GET', 'team', 'App\Http\Controllers\Public\TeamController@index'],
        'public.facilities.index' => ['GET', 'facilities', 'App\Http\Controllers\Public\FacilityController@index'],
        'public.gallery.index' => ['GET', 'gallery', 'App\Http\Controllers\Public\GalleryController@index'],
        'public.contact.index' => ['GET', 'contact', 'App\Http\Controllers\Public\ContactController@index'],
        'public.contact.store' => ['POST', 'contact', 'App\Http\Controllers\Public\ContactController@store'],
        'public.faq.index' => ['GET', 'faq', 'App\Http\Controllers\Public\FaqController@index'],
        'public.chatbot.messages.store' => ['POST', 'chatbot/messages', 'App\Http\Controllers\Public\ChatbotController@store'],
        'public.reels.index' => ['GET', 'reels', 'App\Http\Controllers\Public\ReelController@index'],
        'public.reels.show' => ['GET', 'reels/{reel}', 'App\Http\Controllers\Public\ReelController@show'],
        'public.reels.views.store' => ['POST', 'reels/{reel}/views', 'App\Http\Controllers\Public\ReelViewController@store'],
        'public.reels.likes.store' => ['POST', 'reels/{reel}/likes', 'App\Http\Controllers\Public\ReelLikeController@store'],
        'public.programs.index' => ['GET', 'programs', 'App\Http\Controllers\Public\ProgramController@index'],
        'public.programs.show' => ['GET', 'programs/{program}', 'App\Http\Controllers\Public\ProgramController@show'],
    ];

    foreach ($routes as $routeName => [$method, $uri, $action]) {
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull($routeName)
            ->and($route->uri())->toBe($uri)
            ->and($route->methods())->toContain($method)
            ->and($route->getActionName())->toBe($action);
    }
});

it('keeps public mutating routes validated and throttled', function () {
    foreach ([
        'public.contact.store' => 'throttle:contact',
        'public.chatbot.messages.store' => 'throttle:chatbot',
        'public.reels.views.store' => 'throttle:reels',
        'public.reels.likes.store' => 'throttle:reels',
    ] as $routeName => $middleware) {
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull($routeName)
            ->and($route->gatherMiddleware())->toContain($middleware);
    }

    $this->post(route('public.contact.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'message']);

    $this->postJson(route('public.chatbot.messages.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['message']);
});

it('renders all Miftah pages through the shared public layout', function () {
    $program = miftahSprint0Program();
    $reel = miftahSprint0Reel();

    Content::query()->create([
        'type' => 'page',
        'title' => 'Tentang ETC Planet',
        'slug' => 'about',
        'body' => 'Profil ETC Planet.',
        'meta' => ['vision' => 'Belajar bahasa yang menyenangkan.', 'mission' => ['Kelas aktif'], 'values' => ['Friendly']],
        'is_published' => true,
    ]);

    Content::query()->create([
        'type' => 'page',
        'title' => 'FAQ ETC Planet',
        'slug' => 'faq',
        'body' => 'Pertanyaan umum.',
        'meta' => ['items' => [['question' => 'Bagaimana cara daftar?', 'answer' => 'Pilih program lalu isi form.']]],
        'is_published' => true,
    ]);

    Content::query()->create([
        'type' => 'room',
        'title' => 'Ruang Belajar',
        'slug' => 'ruang-belajar',
        'body' => 'Ruang belajar nyaman.',
        'meta' => ['facilities' => ['AC', 'Projector']],
        'is_published' => true,
    ]);

    Content::query()->create([
        'type' => 'gallery',
        'title' => 'Kegiatan Speaking',
        'slug' => 'kegiatan-speaking',
        'body' => 'Dokumentasi kegiatan.',
        'image' => 'images/pu1-img.jpg',
        'is_published' => true,
    ]);

    User::factory()->create([
        'role' => 'instructor',
        'is_active' => true,
        'show_on_team_page' => true,
        'instructor_position' => 'English Instructor',
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
        route('public.reels.show', $reel),
    ] as $url) {
        $this->get($url)
            ->assertOk()
            ->assertSee('ETC Planet', false)
            ->assertSee('data-chatbot-widget', false)
            ->assertDontSee('Fondasi halaman')
            ->assertDontSee('Implementasi penuh');
    }
});

it('keeps program discovery using active database data and registration CTAs', function () {
    $active = miftahSprint0Program([
        'name' => 'Active Conversation',
        'slug' => 'active-conversation',
        'category' => 'english',
    ]);

    $inactive = miftahSprint0Program([
        'name' => 'Inactive Draft',
        'slug' => 'inactive-draft',
        'category' => 'mandarin',
        'is_active' => false,
    ]);

    $this->get(route('public.programs.index'))
        ->assertOk()
        ->assertSee($active->name)
        ->assertSee('Rp 1.500.000')
        ->assertSee('Rp 200.000')
        ->assertSee(route('public.programs.show', $active), false)
        ->assertSee(route('registrations.programs.index'), false)
        ->assertDontSee($inactive->name);

    $this->get(route('public.programs.index', ['category' => 'mandarin']))
        ->assertOk()
        ->assertDontSee($active->name)
        ->assertDontSee($inactive->name);

    $this->get(route('public.programs.show', $active))
        ->assertOk()
        ->assertSee($active->name)
        ->assertSee(route('registrations.programs.index', ['program' => $active->id]), false);

    $this->get(route('public.programs.show', $inactive))
        ->assertNotFound();
});

it('stores contact messages through validation and the contact service path', function () {
    $this->post(route('public.contact.store'), [
        'name' => 'Miftah',
        'email' => 'miftah@example.test',
        'phone' => '081234567890',
        'subject' => 'Info Program',
        'message' => 'Saya ingin bertanya tentang kelas English.',
    ])
        ->assertRedirect(route('public.contact.index'))
        ->assertSessionHas('status');

    expect(ContactMessage::query()->where('email', 'miftah@example.test')->exists())->toBeTrue();
});

it('returns public chatbot JSON and logs the interaction', function () {
    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-session',
        'message' => 'Saya mau daftar program English.',
    ])
        ->assertOk()
        ->assertJsonStructure(['status', 'session_id', 'intent', 'reply'])
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('session_id', 'miftah-session')
        ->assertJsonPath('intent', 'registration');

    expect(ChatbotLog::query()->where('session_id', 'miftah-session')->exists())->toBeTrue();
});

it('shows only published reels and keeps view and like endpoints controlled', function () {
    $published = miftahSprint0Reel([
        'title' => 'Published Sprint 0 Reel',
        'views_count' => 7,
        'likes_count' => 0,
    ]);

    $draft = miftahSprint0Reel([
        'title' => 'Draft Sprint 0 Reel',
        'video_path' => 'videos/video2.mp4',
        'is_published' => false,
        'published_at' => null,
    ]);

    $this->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee($published->title)
        ->assertDontSee($draft->title);

    $this->get(route('public.reels.show', $published))
        ->assertOk()
        ->assertSee('data-view-endpoint', false)
        ->assertSee('data-like-endpoint', false);

    $this->get(route('public.reels.show', $draft))->assertNotFound();

    $this->postJson(route('public.reels.views.store', $published))
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('views_count', 8);

    $this->postJson(route('public.reels.views.store', $draft))->assertNotFound();

    $this->postJson(route('public.reels.likes.store', $published))
        ->assertOk()
        ->assertJsonPath('liked', true)
        ->assertJsonPath('likes_count', 1);

    $this->postJson(route('public.reels.likes.store', $published))
        ->assertOk()
        ->assertJsonPath('liked', false)
        ->assertJsonPath('likes_count', 0);
});

it('keeps Miftah public views on one shared layout and public controllers away from request all', function () {
    foreach ([
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
        'reels/show.blade.php',
    ] as $view) {
        expect(file_get_contents(resource_path("views/public/{$view}")))->toContain('<x-layouts.public');
    }

    foreach (glob(app_path('Http/Controllers/Public/*Controller.php')) as $controller) {
        expect(file_get_contents($controller))->not->toContain('$request->all()');
    }
});

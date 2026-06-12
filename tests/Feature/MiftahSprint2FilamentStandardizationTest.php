<?php

use App\Models\Program;
use App\Models\Reel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('keeps the complete Miftah public route contract registered', function () {
    foreach ([
        'public.home',
        'public.about',
        'public.team.index',
        'public.facilities.index',
        'public.gallery.index',
        'public.contact.index',
        'public.contact.store',
        'public.faq.index',
        'public.chatbot.messages.store',
        'public.reels.index',
        'public.reels.show',
        'public.reels.views.store',
        'public.reels.likes.store',
        'public.programs.index',
        'public.programs.show',
    ] as $routeName) {
        expect(Route::has($routeName))->toBeTrue();
    }
});

it('keeps every Miftah public page on the shared public layout', function () {
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
    ] as $view) {
        expect(file_get_contents(resource_path("views/pages/public/{$view}")))
            ->toContain('<x-layouts.public');
    }
});

it('standardizes Miftah public controls on shared UI components', function () {
    foreach ([
        'views/components/site/navbar.blade.php',
        'views/components/site/chatbot.blade.php',
        'views/components/site/footer.blade.php',
        'views/pages/public/contact/index.blade.php',
        'views/pages/public/faq/index.blade.php',
        'views/pages/public/programs/index.blade.php',
        'views/pages/public/programs/show.blade.php',
        'views/pages/public/reels/index.blade.php',
    ] as $file) {
        $source = file_get_contents(resource_path($file));

        expect($source)
            ->not->toMatch('/<(button|input|select|textarea|summary|details|table)\b/i')
            ->and($source)->toContain('<x-ui.');
    }
});

it('uses Filament as the base of shared components used by Miftah', function () {
    $componentContracts = [
        'button.blade.php' => '<x-filament::button',
        'icon-button.blade.php' => '<x-filament::icon-button',
        'badge.blade.php' => '<x-filament::badge',
        'empty-state.blade.php' => '<x-filament::empty-state',
        'field.blade.php' => '<x-filament::input',
        'email-field.blade.php' => '<x-filament::input',
        'phone-field.blade.php' => '<x-filament::input',
        'textarea.blade.php' => '<x-filament::input.wrapper',
    ];

    foreach ($componentContracts as $component => $filamentComponent) {
        expect(file_get_contents(resource_path("views/components/ui/{$component}")))
            ->toContain($filamentComponent);
    }
});

it('renders the complete Miftah public discovery surface after standardization', function () {
    $program = Program::query()->create([
        'name' => 'Sprint 2 Public Program',
        'slug' => 'sprint-2-public-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Program untuk verifikasi standardisasi komponen public.',
        'duration_meetings' => 12,
        'max_students' => 10,
        'price' => 1500000,
        'registration_fee' => 150000,
        'is_active' => true,
    ]);

    $reel = Reel::query()->create([
        'title' => 'Sprint 2 Public Reel',
        'description' => 'Reel untuk verifikasi standardisasi komponen public.',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'category' => 'edukasi',
        'views_count' => 0,
        'likes_count' => 0,
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

it('keeps Miftah reels vertical, sound ready, and free from view or like controls', function () {
    $reel = Reel::query()->create([
        'title' => 'Sprint 2 Sound Reel',
        'description' => 'Reel vertikal dengan audio aktif.',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'category' => 'edukasi',
        'views_count' => 10,
        'likes_count' => 5,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee('data-autoplay-reel="true"', false)
        ->assertSee('data-view-endpoint', false)
        ->assertSee('data-reel-player', false)
        ->assertSee('data-reel-playback-indicator', false)
        ->assertSee('public-reel-caption-panel', false)
        ->assertDontSee('Klik video untuk pause atau lanjutkan.')
        ->assertDontSee('data-like-endpoint', false)
        ->assertDontSee('public-reel-actions', false)
        ->assertDontSee('muted', false)
        ->assertDontSee('visibility')
        ->assertDontSee('favorite');

    $this->get(route('public.reels.show', $reel))
        ->assertRedirect(route('public.reels.index', ['reel' => $reel->getKey()]));

    $reelsCss = file_get_contents(resource_path('css/app.css'));

    expect($reelsCss)
        ->toContain('.public-reel-stage')
        ->toContain('right: calc(100% + 2rem)')
        ->toContain('.public-reel-slide.is-active .public-reel-stage')
        ->toContain('.public-reel-video-frame')
        ->toContain('width: 100%;');

    $reelsJavascript = file_get_contents(resource_path('js/app.js'));

    expect($reelsJavascript)
        ->toContain("feed.addEventListener('wheel'")
        ->toContain("feed.addEventListener('touchstart'")
        ->toContain("feed.addEventListener('touchend'")
        ->toContain("feed.addEventListener('click'")
        ->toContain('feed.scrollTo({');
});

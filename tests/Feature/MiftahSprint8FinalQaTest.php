<?php

use App\Models\Content;
use App\Models\Program;
use App\Models\ProgramPromotion;
use App\Models\Reel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('keeps every Miftah public discovery route available for the final demo', function () {
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

it('renders every Miftah public page with one reusable public chrome', function () {
    $program = Program::query()->create([
        'name' => 'Sprint 8 Public Program',
        'slug' => 'sprint-8-public-program',
        'category' => 'english',
        'is_active' => true,
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
    ] as $url) {
        $content = $this->get($url)
            ->assertOk()
            ->assertSee('data-public-discovery-navbar', false)
            ->assertSee('data-public-discovery-footer', false)
            ->assertSee('data-chatbot-widget', false)
            ->getContent();

        expect(substr_count($content, 'data-public-discovery-navbar'))->toBe(1)
            ->and(substr_count($content, 'data-public-discovery-footer'))->toBe(1)
            ->and(substr_count($content, 'data-chatbot-widget'))->toBe(1);
    }
});

it('shows published discovery content and active promotion without leaking drafts', function () {
    $program = Program::query()->create([
        'name' => 'Sprint 8 Promo Program',
        'slug' => 'sprint-8-promo-program',
        'category' => 'english',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'price' => 2000000,
        'registration_fee' => 200000,
        'thumbnail' => 'images/sprint-8-program.jpg',
        'is_active' => true,
    ]);
    ProgramPromotion::query()->create([
        'program_id' => $program->id,
        'title' => 'Sprint 8 Deal',
        'discount_type' => 'fixed',
        'discount_value' => 250000,
        'badge_label' => 'Hemat 250K',
        'terms' => 'Berlaku selama kuota tersedia.',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);

    foreach ([
        [Content::TYPE_GALLERY, 'Sprint 8 Published Gallery', true],
        [Content::TYPE_GALLERY, 'Sprint 8 Draft Gallery', false],
        [Content::TYPE_PARTNER, 'Sprint 8 Published Partner', true],
        [Content::TYPE_PARTNER, 'Sprint 8 Draft Partner', false],
    ] as [$type, $title, $published]) {
        Content::query()->create([
            'type' => $type,
            'title' => $title,
            'slug' => str($title)->slug(),
            'body' => 'Konten final QA Sprint 8.',
            'image' => 'images/sprint-8-content.jpg',
            'meta' => $type === Content::TYPE_PARTNER
                ? ['category' => 'Sekolah', 'website' => 'https://partner.example.test']
                : [],
            'is_published' => $published,
        ]);
    }

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('Sprint 8 Published Partner')
        ->assertDontSee('Sprint 8 Draft Partner')
        ->assertSee('Hemat 250K')
        ->assertSee('images/sprint-8-program.jpg');

    $this->get(route('public.gallery.index'))
        ->assertOk()
        ->assertSee('Sprint 8 Published Gallery')
        ->assertDontSee('Sprint 8 Draft Gallery');

    $this->get(route('public.programs.show', $program))
        ->assertOk()
        ->assertSee('Sprint 8 Deal')
        ->assertSee('Rp 1.750.000')
        ->assertSee('Berlaku selama kuota tersedia.')
        ->assertSee(route('registrations.create', ['program' => $program->id]), false);
});

it('provides responsive accessible reel actions and preserves liked session state', function () {
    $reel = Reel::query()->create([
        'title' => 'Sprint 8 Social Reel',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'views_count' => 8,
        'likes_count' => 2,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->withSession(['liked_reels' => [$reel->id]])
        ->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee('data-vertical-reels-feed', false)
        ->assertSee('public-reel-actions', false)
        ->assertSee('data-reel-view-count', false)
        ->assertSee('data-like-endpoint="'.route('public.reels.likes.store', $reel).'"', false)
        ->assertSee('data-liked="true"', false)
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('favorite');

    $this->withSession(['liked_reels' => [$reel->id]])
        ->postJson(route('public.reels.likes.store', $reel))
        ->assertOk()
        ->assertJsonPath('liked', false)
        ->assertJsonPath('likes_count', 1);

    $this->postJson(route('public.reels.views.store', $reel))
        ->assertOk()
        ->assertJsonPath('counted', true)
        ->assertJsonPath('views_count', 9);
});

it('keeps final responsive and keyboard interaction contracts in public assets', function () {
    $css = file_get_contents(resource_path('css/app.css'));
    $javascript = file_get_contents(resource_path('js/app.js'));
    $chatbot = file_get_contents(resource_path('views/components/public-discovery/chatbot.blade.php'));

    expect($css)
        ->toContain('@media (max-width: 1100px)')
        ->toContain('@media (max-width: 640px)')
        ->toContain('.public-reel-actions')
        ->toContain('bottom: max(7rem, calc(env(safe-area-inset-bottom) + 6rem))')
        ->toContain('.public-discovery-chatbot__panel')
        ->and($javascript)
        ->toContain("document.querySelectorAll('[data-reel-like]')")
        ->toContain("button.setAttribute('aria-pressed'")
        ->toContain("event.key === 'Escape'")
        ->toContain("player.addEventListener('keydown'")
        ->toContain("feed.addEventListener('touchend'")
        ->and($chatbot)
        ->toContain('role="dialog"')
        ->toContain('role="log"')
        ->toContain('aria-live="polite"');
});

it('validates final public contact and chatbot actions', function () {
    $this->post(route('public.contact.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'message']);

    $this->post(route('public.contact.store'), [
        'name' => 'Miftah Sprint 8',
        'email' => 'miftah.sprint8@example.test',
        'phone' => '081234567890',
        'subject' => 'Final QA',
        'message' => 'Saya ingin mengetahui program ETC Planet.',
    ])
        ->assertRedirect(route('public.contact.index'))
        ->assertSessionHas('status');

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-8',
        'message' => 'Bagaimana cara daftar?',
    ])
        ->assertOk()
        ->assertJsonStructure(['status', 'session_id', 'intent', 'reply'])
        ->assertJsonPath('status', 'ok');
});

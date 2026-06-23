<?php

use App\Models\Content;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\Reel;
use App\Services\PublicDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps reel analytics controlled with accessible like and view controls', function () {
    $reel = Reel::query()->create([
        'title' => 'Sprint 6 Interactive Reel',
        'description' => 'Reel dengan action yang mudah dijangkau.',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'category' => 'edukasi',
        'views_count' => 12,
        'likes_count' => 4,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee('data-view-endpoint="'.route('public.reels.views.store', $reel).'"', false)
        ->assertSee('public-reel-actions', false)
        ->assertSee('data-reel-view-count', false)
        ->assertSee('data-reel-like', false)
        ->assertSee('data-like-endpoint="'.route('public.reels.likes.store', $reel).'"', false)
        ->assertSee('aria-pressed="false"', false)
        ->assertSee('favorite_border');
});

it('exposes clear enrollment, program, and consultation actions in the first viewport', function () {
    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('Daftar Sekarang')
        ->assertSee('Lihat Program')
        ->assertSee('Tanya ETC')
        ->assertSee(route('public.contact.index'), false);
});

it('turns published contact settings into direct contact actions', function () {
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'ETC Contact Profile',
        'slug' => 'etc-profile',
        'meta' => [
            'address' => 'Jl. Sprint 6 Contact',
            'whatsapp' => '0812-3456-7890',
            'email' => 'contact-sprint6@example.test',
            'instagram' => 'https://instagram.com/etc-sprint6',
            'map_url' => 'https://maps.example.test/etc',
        ],
        'is_published' => true,
    ]);

    $this->get(route('public.contact.index'))
        ->assertOk()
        ->assertSee('https://wa.me/6281234567890', false)
        ->assertSee('mailto:contact-sprint6@example.test', false)
        ->assertSee('https://instagram.com/etc-sprint6', false)
        ->assertSee('https://maps.example.test/etc', false);
});

it('counts a reel view once per visitor session', function () {
    $reel = Reel::query()->create([
        'title' => 'Sprint 6 Controlled View',
        'video_path' => 'videos/video1.mp4',
        'views_count' => 5,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->postJson(route('public.reels.views.store', $reel))
        ->assertOk()
        ->assertJsonPath('counted', true)
        ->assertJsonPath('views_count', 6);

    $this->postJson(route('public.reels.views.store', $reel))
        ->assertOk()
        ->assertJsonPath('counted', false)
        ->assertJsonPath('views_count', 6);

    expect($reel->refresh()->views_count)->toBe(6);
});

it('uses published CMS media and testimonial ratings on the public home page', function () {
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'ETC Sprint 6 Profile',
        'slug' => 'etc-profile',
        'image' => 'images/sprint-6-hero.jpg',
        'is_published' => true,
    ]);

    foreach ([4, 5] as $index => $rating) {
        Content::query()->create([
            'type' => Content::TYPE_TESTIMONIAL,
            'title' => 'Sprint 6 Testimonial '.($index + 1),
            'slug' => 'sprint-6-testimonial-'.($index + 1),
            'body' => 'Pengalaman belajar yang baik.',
            'meta' => ['rating' => $rating],
            'display_order' => $index,
            'is_published' => true,
        ]);
    }

    expect(app(PublicDiscoveryService::class)->stats()['satisfaction'])->toBe('90%');

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('data-home-hero-image', false)
        ->assertSee('images/sprint-6-hero.jpg')
        ->assertSee('90%')
        ->assertDontSee('98%');
});

it('answers pricing and schedule questions from current database records', function () {
    config([
        'rag.nvidia.api_key' => null,
        'rag.qdrant.url' => null,
    ]);

    $program = Program::query()->create([
        'name' => 'Sprint 6 Dynamic English',
        'slug' => 'sprint-6-dynamic-english',
        'category' => 'english',
        'price' => 1750000,
        'registration_fee' => 125000,
        'is_active' => true,
    ]);

    CourseClass::query()->create([
        'program_id' => $program->id,
        'name' => 'Sprint 6 Evening Class',
        'schedule_days' => 'Selasa-Kamis',
        'schedule_time' => '18.30-20.00',
        'status' => 'upcoming',
    ]);

    $pricingResponse = $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'sprint-6-pricing',
        'message' => 'Berapa biaya program sekarang?',
    ]);

    $pricingResponse
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('links.0.label', 'Sprint 6 Dynamic English');
    expect($pricingResponse->json('reply'))->toContain('Sprint 6 Dynamic English');

    $scheduleResponse = $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'sprint-6-schedule',
        'message' => 'Apa jadwal kelas yang tersedia?',
    ]);

    $scheduleResponse
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('links.0.label', 'Sprint 6 Dynamic English');
    expect($scheduleResponse->json('reply'))->toContain('Sprint 6 Evening Class');
});

it('keeps the public chatbot and reels operable with a keyboard', function () {
    $chatbot = file_get_contents(resource_path('views/components/public-discovery/chatbot.blade.php'));
    $reels = file_get_contents(resource_path('views/pages/public/reels/index.blade.php'));
    $javascript = file_get_contents(resource_path('js/app.js'));

    expect($chatbot)
        ->toContain('role="dialog"')
        ->toContain('role="log"')
        ->toContain('aria-live="polite"')
        ->toContain('aria-controls="public-discovery-chatbot-panel"')
        ->and($reels)
        ->toContain('tabindex="0"')
        ->toContain('aria-label="Putar atau jeda reel')
        ->and($javascript)
        ->toContain("event.key === 'Escape'")
        ->toContain("player.addEventListener('keydown'")
        ->toContain("['Enter', ' '].includes(event.key)");
});

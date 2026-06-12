<?php

use App\Models\ChatbotLog;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Program;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders all Miftah public discovery pages without foundation placeholders', function () {
    $reel = Reel::query()->create([
        'title' => 'Published Reel',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'is_published' => true,
        'published_at' => now(),
    ]);

    foreach ([
        '/',
        '/about',
        '/team',
        '/facilities',
        '/gallery',
        '/contact',
        '/faq',
    ] as $uri) {
        $this->get($uri)
            ->assertOk()
            ->assertDontSee('Fondasi halaman')
            ->assertDontSee('Implementasi penuh')
            ->assertSee('data-chatbot-widget', false);
    }

    $this->get('/reels')
        ->assertOk()
        ->assertDontSee('Fondasi halaman')
        ->assertDontSee('Implementasi penuh')
        ->assertDontSee('data-chatbot-widget', false);

    $this->get("/reels/{$reel->id}")
        ->assertRedirect('/reels');
});

it('connects public program CTAs into detail and the registration picker', function () {
    $program = Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Program komunikasi bahasa Inggris.',
        'duration_meetings' => 20,
        'max_students' => 12,
        'price' => 1500000,
        'registration_fee' => 200000,
        'thumbnail' => 'images/pu2-img.jpg',
        'is_active' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee(route('public.programs.index'), false)
        ->assertSee(route('public.programs.show', $program), false);

    $this->get(route('public.programs.show', $program))
        ->assertOk()
        ->assertSee(route('registrations.create', ['program' => $program->id]), false);

    $this->get(route('public.programs.index'))
        ->assertOk()
        ->assertSee(route('registrations.create', ['program' => $program->id]), false)
        ->assertDontSee(route('registrations.programs.index', ['program' => $program->id]), false);

    $this->get(route('registrations.create', ['program' => $program->id]))
        ->assertOk()
        ->assertSee('value="'.$program->id.'" selected', false)
        ->assertSee($program->name);

    $this->get(route('registrations.programs.index', ['program' => $program->id]))
        ->assertRedirect(route('public.programs.index'));
});

it('stores valid contact messages and rejects invalid contact messages', function () {
    $this->post('/contact', [])
        ->assertSessionHasErrors(['name', 'email', 'message']);

    $this->post('/contact', [
        'name' => 'Miftah',
        'email' => 'miftah@example.test',
        'phone' => '081234567890',
        'subject' => 'Info Program',
        'message' => 'Saya ingin bertanya tentang kelas English.',
    ])
        ->assertRedirect('/contact')
        ->assertSessionHas('status');

    expect(ContactMessage::query()->where('email', 'miftah@example.test')->exists())->toBeTrue();
});

it('logs chatbot messages and returns the public chatbot JSON shape', function () {
    $response = $this->postJson('/chatbot/messages', [
        'message' => 'Berapa biaya pendaftaran dan program?',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure(['status', 'session_id', 'intent', 'reply'])
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('intent', 'pricing');

    expect(ChatbotLog::query()->count())->toBe(1);
});

it('only shows published reels publicly and hides unpublished reel detail', function () {
    $published = Reel::query()->create([
        'title' => 'Published Activity',
        'video_path' => 'videos/video1.mp4',
        'is_published' => true,
        'published_at' => now(),
    ]);
    $draft = Reel::query()->create([
        'title' => 'Draft Activity',
        'video_path' => 'videos/video2.mp4',
        'is_published' => false,
    ]);

    $this->get('/reels')
        ->assertOk()
        ->assertSee($published->title)
        ->assertDontSee($draft->title);

    $this->get("/reels/{$published->id}")->assertRedirect('/reels');
    $this->get("/reels/{$draft->id}")->assertNotFound();
});

it('increments public reel views only for published reels', function () {
    $reel = Reel::query()->create([
        'title' => 'Viewed Reel',
        'video_path' => 'videos/video1.mp4',
        'views_count' => 4,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->postJson("/reels/{$reel->id}/views")
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('views_count', 5);

    expect($reel->refresh()->views_count)->toBe(5);
});

it('toggles reel likes in the session without going negative', function () {
    $reel = Reel::query()->create([
        'title' => 'Liked Reel',
        'video_path' => 'videos/video1.mp4',
        'likes_count' => 0,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->postJson("/reels/{$reel->id}/likes")
        ->assertOk()
        ->assertJsonPath('liked', true)
        ->assertJsonPath('likes_count', 1);

    $this->postJson("/reels/{$reel->id}/likes")
        ->assertOk()
        ->assertJsonPath('liked', false)
        ->assertJsonPath('likes_count', 0);

    expect($reel->refresh()->likes_count)->toBe(0);
});

it('loads public discovery seed data idempotently', function () {
    $this->seed(\Database\Seeders\PublicDiscoverySeeder::class);
    $this->seed(\Database\Seeders\PublicDiscoverySeeder::class);

    expect(Content::query()->where('type', 'page')->where('slug', 'about')->count())->toBe(1)
        ->and(Reel::query()->where('is_published', true)->count())->toBeGreaterThan(0)
        ->and(User::query()->instructors()->where('show_on_team_page', true)->count())->toBeGreaterThan(0);
});

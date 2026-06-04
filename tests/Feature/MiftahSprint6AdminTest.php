<?php

use App\Models\ChatbotLog;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('protects all Miftah sprint 6 admin pages', function () {
    $student = User::factory()->create(['role' => 'student']);
    $reel = Reel::query()->create([
        'title' => 'Protected Reel',
        'video_path' => 'videos/video1.mp4',
    ]);
    $content = Content::query()->create([
        'type' => 'page',
        'title' => 'Protected Content',
        'slug' => 'protected-content',
    ]);
    $message = ContactMessage::query()->create([
        'name' => 'Visitor',
        'email' => 'visitor@example.test',
        'message' => 'Halo ETC.',
    ]);

    $urls = [
        route('admin.reels.index'),
        route('admin.reels.create'),
        route('admin.reels.edit', $reel),
        route('admin.contents.index'),
        route('admin.contents.create'),
        route('admin.contents.edit', $content),
        route('admin.contact-messages.index'),
        route('admin.contact-messages.show', $message),
        route('admin.chatbot-logs.index'),
        route('admin.settings.index'),
    ];

    foreach ($urls as $url) {
        $this->get($url)->assertRedirect(route('auth.login'));
    }

    foreach ($urls as $url) {
        $this->actingAs($student)->get($url)->assertForbidden();
    }
});

it('renders every Miftah sprint 6 admin page without placeholders', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $reel = Reel::query()->create([
        'title' => 'Published Admin Reel',
        'video_path' => 'videos/video1.mp4',
        'is_published' => true,
        'published_at' => now(),
    ]);
    $content = Content::query()->create([
        'type' => 'room',
        'title' => 'Studio Conversation',
        'slug' => 'studio-conversation',
        'body' => 'Ruang kelas nyaman.',
    ]);
    $message = ContactMessage::query()->create([
        'name' => 'Miftah Visitor',
        'email' => 'visitor@example.test',
        'subject' => 'Info Kelas',
        'message' => 'Saya ingin bertanya kelas English.',
    ]);
    ChatbotLog::query()->create([
        'session_id' => 'session-admin-render',
        'user_message' => 'Berapa biaya?',
        'bot_response' => 'Biaya tergantung program.',
        'intent' => 'pricing',
        'created_at' => now(),
    ]);

    foreach ([
        route('admin.reels.index') => 'Admin Reels',
        route('admin.reels.create') => 'Tambah Reel',
        route('admin.reels.edit', $reel) => 'Edit Reel',
        route('admin.contents.index') => 'CMS Konten',
        route('admin.contents.create') => 'Tambah Konten',
        route('admin.contents.edit', $content) => 'Edit Konten',
        route('admin.contact-messages.index') => 'Pesan Kontak',
        route('admin.contact-messages.show', $message) => 'Detail Pesan',
        route('admin.chatbot-logs.index') => 'Chatbot Logs',
        route('admin.settings.index') => 'Settings',
    ] as $url => $text) {
        $this->actingAs($admin)
            ->get($url)
            ->assertOk()
            ->assertSee($text)
            ->assertDontSee('Fondasi halaman')
            ->assertDontSee('Implementasi penuh');
    }
});

it('creates and updates reels with media and publish state', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.reels.store'), [
            'title' => 'Class Activity Reel',
            'description' => 'Dokumentasi kelas speaking.',
            'category' => 'dokumentasi',
            'duration_seconds' => 42,
            'is_published' => '1',
            'video' => UploadedFile::fake()->create('activity.mp4', 1000, 'video/mp4'),
            'thumbnail' => UploadedFile::fake()->image('activity.jpg'),
        ])
        ->assertRedirect(route('admin.reels.index'))
        ->assertSessionHasNoErrors();

    $reel = Reel::query()->where('title', 'Class Activity Reel')->firstOrFail();

    expect($reel->is_published)->toBeTrue()
        ->and($reel->published_at)->not->toBeNull()
        ->and($reel->video_path)->toStartWith('reels/videos/')
        ->and($reel->thumbnail_path)->toStartWith('reels/thumbnails/');

    Storage::disk('public')->assertExists($reel->video_path);
    Storage::disk('public')->assertExists($reel->thumbnail_path);

    $this->actingAs($admin)
        ->put(route('admin.reels.update', $reel), [
            'title' => 'Class Activity Reel Updated',
            'description' => 'Update caption.',
            'category' => 'event',
            'duration_seconds' => 55,
        ])
        ->assertRedirect(route('admin.reels.index'))
        ->assertSessionHasNoErrors();

    $reel->refresh();

    expect($reel->title)->toBe('Class Activity Reel Updated')
        ->and($reel->category)->toBe('event')
        ->and($reel->is_published)->toBeFalse()
        ->and($reel->published_at)->toBeNull();
});

it('creates and updates cms contents with media and meta data', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.contents.store'), [
            'type' => 'gallery',
            'title' => 'Open House Gallery',
            'slug' => 'open-house-gallery',
            'body' => 'Dokumentasi open house.',
            'display_order' => 3,
            'is_published' => '1',
            'meta' => [
                'event_date' => '2026-06-01',
                'location' => 'Padang',
            ],
            'image' => UploadedFile::fake()->image('cover.jpg'),
            'images' => [
                UploadedFile::fake()->image('gallery-1.jpg'),
                UploadedFile::fake()->image('gallery-2.jpg'),
            ],
        ])
        ->assertRedirect(route('admin.contents.index'))
        ->assertSessionHasNoErrors();

    $content = Content::query()->where('slug', 'open-house-gallery')->firstOrFail();

    expect($content->type)->toBe('gallery')
        ->and($content->meta['location'])->toBe('Padang')
        ->and($content->image)->toStartWith('contents/images/')
        ->and($content->images)->toHaveCount(2);

    $this->actingAs($admin)
        ->put(route('admin.contents.update', $content), [
            'type' => 'room',
            'title' => 'Studio Speaking',
            'slug' => 'studio-speaking',
            'body' => 'Ruang speaking baru.',
            'display_order' => 1,
            'meta' => [
                'capacity' => '12',
                'facility' => 'AC, Projector, Whiteboard',
            ],
        ])
        ->assertRedirect(route('admin.contents.index'))
        ->assertSessionHasNoErrors();

    $content->refresh();

    expect($content->type)->toBe('room')
        ->and($content->slug)->toBe('studio-speaking')
        ->and($content->meta['capacity'])->toBe('12')
        ->and($content->is_published)->toBeFalse();
});

it('validates invalid reel and content submissions', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.reels.store'), [])
        ->assertSessionHasErrors(['title', 'video']);

    $this->actingAs($admin)
        ->post(route('admin.contents.store'), [])
        ->assertSessionHasErrors(['type', 'title']);
});

it('lists contact messages, marks detail as read, and filters chatbot logs', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $message = ContactMessage::query()->create([
        'name' => 'Calon Siswa',
        'email' => 'calon@example.test',
        'subject' => 'Info Program',
        'message' => 'Apakah ada kelas weekend?',
        'is_read' => false,
    ]);
    ChatbotLog::query()->create([
        'session_id' => 'pricing-session',
        'user_message' => 'Berapa biaya pendaftaran?',
        'bot_response' => 'Biaya pendaftaran mulai Rp 200.000.',
        'intent' => 'pricing',
        'is_helpful' => true,
        'created_at' => now(),
    ]);
    ChatbotLog::query()->create([
        'session_id' => 'program-session',
        'user_message' => 'Ada program apa?',
        'bot_response' => 'Ada English, Mandarin, dan test prep.',
        'intent' => 'program',
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.index', ['search' => 'Calon']))
        ->assertOk()
        ->assertSee('Calon Siswa')
        ->assertSee('Baru');

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.show', $message))
        ->assertOk()
        ->assertSee('Apakah ada kelas weekend?');

    expect($message->refresh()->is_read)->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.chatbot-logs.index', ['intent' => 'pricing']))
        ->assertOk()
        ->assertSee('Berapa biaya pendaftaran?')
        ->assertDontSee('Ada program apa?');
});

it('updates settings through contents and exposes updated public contact data', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->seed(\Database\Seeders\PublicDiscoverySeeder::class);

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertSee('Jl. S. Parman');

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'address' => 'Jl. Sprint 6 No. 1, Padang',
            'phone' => '+62 812-9999-0000',
            'email' => 'sprint6@etcplanet.test',
            'instagram' => 'https://www.instagram.com/etcplanet/',
            'hours' => 'Senin-Jumat, 10.00-18.00',
            'bank_name' => 'BCA',
            'bank_account_name' => 'ETC Planet',
            'bank_account_number' => '1234567890',
            'payment_notes' => 'Konfirmasi pembayaran ke admin.',
            'qris' => UploadedFile::fake()->image('qris.png'),
        ])
        ->assertRedirect(route('admin.settings.index'))
        ->assertSessionHasNoErrors();

    expect(Content::query()->where('type', 'setting')->where('slug', 'address')->first()?->meta['value'])->toBe('Jl. Sprint 6 No. 1, Padang')
        ->and(Content::query()->where('type', 'setting')->where('slug', 'bank_name')->first()?->meta['value'])->toBe('BCA')
        ->and(Content::query()->where('type', 'setting')->where('slug', 'qris')->first()?->image)->toStartWith('settings/');

    $this->get(route('public.contact.index'))
        ->assertOk()
        ->assertSee('Jl. Sprint 6 No. 1, Padang')
        ->assertSee('+62 812-9999-0000')
        ->assertSee('sprint6@etcplanet.test');
});

it('keeps all documented Miftah sprint 6 route names registered', function () {
    foreach ([
        'admin.reels.index',
        'admin.reels.create',
        'admin.reels.store',
        'admin.reels.edit',
        'admin.reels.update',
        'admin.contents.index',
        'admin.contents.create',
        'admin.contents.store',
        'admin.contents.edit',
        'admin.contents.update',
        'admin.contact-messages.index',
        'admin.contact-messages.show',
        'admin.chatbot-logs.index',
        'admin.settings.index',
        'admin.settings.update',
    ] as $routeName) {
        expect(Route::has($routeName))->toBeTrue($routeName);
    }
});

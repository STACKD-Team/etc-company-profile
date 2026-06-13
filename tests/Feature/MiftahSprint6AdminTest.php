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
        'type' => 'gallery',
        'title' => 'Protected Content',
        'slug' => 'protected-content',
    ]);
    $message = ContactMessage::query()->create([
        'name' => 'Visitor',
        'email' => 'visitor@example.test',
        'message' => 'Halo ETC.',
    ]);

    $urls = [
        route('admin.reel.index'),
        route('admin.reel.create'),
        route('admin.reel.edit', $reel),
        route('admin.gallery.index'),
        route('admin.gallery.create'),
        route('admin.gallery.edit', $content),
        route('admin.contact-message.index'),
        route('admin.contact-message.show', $message),
        route('admin.chatbot-log.index'),
        route('admin.profile.index'),
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
        'type' => 'gallery',
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
        route('admin.reel.index') => 'Admin Reels',
        route('admin.reel.create') => 'Tambah Reel',
        route('admin.reel.edit', $reel) => 'Edit Reel',
        route('admin.gallery.index') => 'Gallery',
        route('admin.gallery.create') => 'Tambah Gallery',
        route('admin.gallery.edit', $content) => 'Edit Gallery',
        route('admin.contact-message.index') => 'Pesan Kontak',
        route('admin.contact-message.show', $message) => 'Detail Pesan',
        route('admin.chatbot-log.index') => 'Chatbot Logs',
        route('admin.profile.index') => 'Profile',
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
        ->post(route('admin.reel.store'), [
            'title' => 'Class Activity Reel',
            'description' => 'Dokumentasi kelas speaking.',
            'category' => 'dokumentasi',
            'duration_seconds' => 42,
            'is_published' => '1',
            'video' => UploadedFile::fake()->create('activity.mp4', 1000, 'video/mp4'),
            'thumbnail' => UploadedFile::fake()->image('activity.jpg'),
        ])
        ->assertRedirect(route('admin.reel.show', 1))
        ->assertSessionHasNoErrors();

    $reel = Reel::query()->where('title', 'Class Activity Reel')->firstOrFail();

    expect($reel->is_published)->toBeTrue()
        ->and($reel->published_at)->not->toBeNull()
        ->and($reel->video_path)->toStartWith('reels/videos/')
        ->and($reel->thumbnail_path)->toStartWith('reels/thumbnails/');

    Storage::disk('public')->assertExists($reel->video_path);
    Storage::disk('public')->assertExists($reel->thumbnail_path);

    $this->actingAs($admin)
        ->put(route('admin.reel.update', $reel), [
            'title' => 'Class Activity Reel Updated',
            'description' => 'Update caption.',
            'category' => 'event',
            'duration_seconds' => 55,
        ])
        ->assertRedirect(route('admin.reel.show', $reel))
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
        ->post(route('admin.gallery.store'), [
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
        ->assertRedirect(route('admin.gallery.show', 1))
        ->assertSessionHasNoErrors();

    $content = Content::query()->where('slug', 'open-house-gallery')->firstOrFail();

    expect($content->type)->toBe('gallery')
        ->and($content->meta['location'])->toBe('Padang')
        ->and($content->image)->toStartWith('contents/images/')
        ->and($content->images)->toHaveCount(2);

    $this->actingAs($admin)
        ->put(route('admin.gallery.update', $content), [
            'type' => 'faq',
            'title' => 'Open House Gallery Updated',
            'slug' => 'studio-speaking',
            'body' => 'Dokumentasi open house diperbarui.',
            'display_order' => 1,
            'meta' => [
                'location' => 'Padang',
            ],
        ])
        ->assertRedirect(route('admin.gallery.show', $content))
        ->assertSessionHasNoErrors();

    $content->refresh();

    expect($content->type)->toBe('gallery')
        ->and($content->slug)->toBe('studio-speaking')
        ->and($content->meta['location'])->toBe('Padang')
        ->and($content->is_published)->toBeFalse();
});

it('validates invalid reel and content submissions', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.reel.store'), [])
        ->assertSessionHasErrors(['title', 'video']);

    $this->actingAs($admin)
        ->post(route('admin.gallery.store'), [])
        ->assertSessionHasErrors(['title']);
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
        ->get(route('admin.contact-message.index', ['search' => 'Calon']))
        ->assertOk()
        ->assertSee('Calon Siswa')
        ->assertSee('Baru');

    $this->actingAs($admin)
        ->get(route('admin.contact-message.show', $message))
        ->assertOk()
        ->assertSee('Apakah ada kelas weekend?');

    expect($message->refresh()->is_read)->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.chatbot-log.index', ['intent' => 'pricing']))
        ->assertOk()
        ->assertSee('Berapa biaya pendaftaran?')
        ->assertDontSee('Ada program apa?');
});

it('updates settings through contents and exposes updated public contact data', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->seed(\Database\Seeders\PublicDiscoverySeeder::class);

    $this->actingAs($admin)
        ->get(route('admin.profile.index'))
        ->assertOk()
        ->assertSee('Jl. S. Parman');

    $this->actingAs($admin)
        ->put(route('admin.profile.update'), [
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
        ->assertRedirect(route('admin.profile.index'))
        ->assertSessionHasNoErrors();

    expect(Content::query()->where('type', 'profile')->where('slug', 'address')->first()?->meta['value'])->toBe('Jl. Sprint 6 No. 1, Padang')
        ->and(Content::query()->where('type', 'profile')->where('slug', 'bank_name')->first()?->meta['value'])->toBe('BCA')
        ->and(Content::query()->where('type', 'profile')->where('slug', 'qris')->first()?->image)->toStartWith('settings/');

    $this->get(route('public.contact.index'))
        ->assertOk()
        ->assertSee('Jl. Sprint 6 No. 1, Padang')
        ->assertSee('+62 812-9999-0000')
        ->assertSee('sprint6@etcplanet.test');
});

it('keeps all documented Miftah sprint 6 route names registered', function () {
    foreach ([
        'admin.reel.index',
        'admin.reel.create',
        'admin.reel.store',
        'admin.reel.edit',
        'admin.reel.update',
        'admin.gallery.index',
        'admin.gallery.create',
        'admin.gallery.store',
        'admin.gallery.edit',
        'admin.gallery.update',
        'admin.contact-message.index',
        'admin.contact-message.show',
        'admin.chatbot-log.index',
        'admin.profile.index',
        'admin.profile.update',
    ] as $routeName) {
        expect(Route::has($routeName))->toBeTrue($routeName);
    }
});

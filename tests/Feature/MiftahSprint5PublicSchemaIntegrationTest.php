<?php

use App\Models\Content;
use App\Models\CourseClass;
use App\Models\Program;
use App\Models\Room;
use App\Services\PublicDiscoveryService;
use Database\Seeders\PublicDiscoverySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('uses the canonical Sprint 5 room and content schema', function () {
    expect(Schema::hasTable('rooms'))->toBeTrue()
        ->and(Schema::hasColumns('rooms', [
            'id',
            'name',
            'description',
            'capacity',
            'image',
            'facilities',
            'is_active',
            'display_order',
            'created_at',
            'updated_at',
            'deleted_at',
        ]))->toBeTrue()
        ->and(Schema::hasColumn('classes', 'room_id'))->toBeTrue()
        ->and(Schema::hasColumn('classes', 'room'))->toBeFalse()
        ->and(Content::TYPES)->toBe([
            Content::TYPE_GALLERY,
            Content::TYPE_PARTNER,
            Content::TYPE_PROFILE,
            Content::TYPE_FAQ,
            Content::TYPE_TESTIMONIAL,
        ]);

    $program = Program::query()->create([
        'name' => 'Sprint 5 Room Program',
        'slug' => 'sprint-5-room-program',
        'category' => 'english',
    ]);
    $room = Room::query()->create([
        'name' => 'Sprint 5 Linked Room',
        'capacity' => 12,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'room_id' => $room->id,
        'name' => 'Sprint 5 Linked Class',
    ]);

    expect($class->refresh()->room->is($room))->toBeTrue()
        ->and($room->classes()->whereKey($class->getKey())->exists())->toBeTrue();
});

it('seeds the Sprint 5 public contract idempotently', function () {
    $this->seed(PublicDiscoverySeeder::class);
    $this->seed(PublicDiscoverySeeder::class);

    expect(Room::query()->count())->toBe(3)
        ->and(Content::query()->where('type', Content::TYPE_PROFILE)->count())->toBe(1)
        ->and(Content::query()->where('type', Content::TYPE_FAQ)->count())->toBe(4)
        ->and(Content::query()->where('type', Content::TYPE_GALLERY)->count())->toBe(3)
        ->and(Content::query()->where('type', Content::TYPE_PARTNER)->count())->toBe(3)
        ->and(Content::query()->where('type', Content::TYPE_TESTIMONIAL)->count())->toBe(3)
        ->and(Content::query()->whereNotIn('type', Content::TYPES)->count())->toBe(0)
        ->and(Room::query()->whereNotNull('facilities')->count())->toBe(3);
});

it('renders only active rooms from the standalone rooms table', function () {
    Room::query()->create([
        'name' => 'Second Active Room',
        'description' => 'Second room description.',
        'capacity' => 14,
        'image' => 'images/room-second.jpg',
        'facilities' => ['AC', 'Projector'],
        'display_order' => 2,
        'is_active' => true,
    ]);
    Room::query()->create([
        'name' => 'First Active Room',
        'description' => null,
        'capacity' => 10,
        'image' => 'images/room-first.jpg',
        'display_order' => 1,
        'is_active' => true,
    ]);
    Room::query()->create([
        'name' => 'Inactive Room',
        'display_order' => 0,
        'is_active' => false,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_GALLERY,
        'title' => 'Content That Is Not A Room',
        'slug' => 'content-that-is-not-a-room',
        'is_published' => true,
    ]);

    $this->get(route('public.facilities.index'))
        ->assertOk()
        ->assertSeeInOrder(['First Active Room', 'Second Active Room'])
        ->assertSee('Second room description.')
        ->assertSee('Kapasitas 14 siswa')
        ->assertSee('Projector')
        ->assertSee('images/room-second.jpg')
        ->assertDontSee('Inactive Room')
        ->assertDontSee('Content That Is Not A Room');
});

it('merges a main profile with published per-slug settings as overrides', function () {
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'Published ETC Profile',
        'slug' => 'about',
        'body' => 'Published profile body.',
        'meta' => [
            'vision' => 'Vision from the main profile.',
            'mission' => ['Published mission'],
            'values' => ['Friendly'],
            'address' => 'Old profile address',
            'email' => 'published@example.test',
        ],
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'Alamat',
        'slug' => 'address',
        'meta' => ['value' => 'Updated Address Padang'],
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'Visi',
        'slug' => 'vision',
        'meta' => ['value' => 'Vision from the separate setting.'],
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'Telepon Draft',
        'slug' => 'phone',
        'meta' => ['value' => '0800000000'],
        'is_published' => false,
    ]);

    $settings = app(PublicDiscoveryService::class)->settings();

    expect(app(PublicDiscoveryService::class)->profile()?->slug)->toBe('about')
        ->and($settings['address'])->toBe('Updated Address Padang')
        ->and($settings['vision'])->toBe('Vision from the separate setting.')
        ->and($settings['email'])->toBe('published@example.test')
        ->and($settings)->not->toHaveKey('phone');

    $this->get(route('public.about'))
        ->assertOk()
        ->assertSee('Published ETC Profile')
        ->assertSee('Published profile body.')
        ->assertSee('Vision from the separate setting.')
        ->assertDontSee('Vision from the main profile.');

    $this->get(route('public.contact.index'))
        ->assertOk()
        ->assertSee('Updated Address Padang')
        ->assertSee('published@example.test')
        ->assertDontSee('Old profile address')
        ->assertDontSee('0800000000');
});

it('renders profile information stored only as published per-slug settings', function () {
    foreach ([
        'general_info' => 'ETC profile from individual settings.',
        'vision' => 'Individual setting vision.',
        'mission' => "First mission\nSecond mission",
        'values' => 'Friendly, Focused',
    ] as $slug => $value) {
        Content::query()->create([
            'type' => Content::TYPE_PROFILE,
            'title' => str($slug)->replace('_', ' ')->headline(),
            'slug' => $slug,
            'meta' => ['value' => $value],
            'is_published' => true,
        ]);
    }

    expect(app(PublicDiscoveryService::class)->profile())->toBeNull();

    $this->get(route('public.about'))
        ->assertOk()
        ->assertSee('Tentang ETC Planet')
        ->assertSee('ETC profile from individual settings.')
        ->assertSee('Individual setting vision.')
        ->assertSee('First mission')
        ->assertSee('Second mission')
        ->assertSee('Friendly')
        ->assertSee('Focused');
});

it('shows only published CMS discovery content in display order', function () {
    foreach ([
        [Content::TYPE_FAQ, 'Second Published FAQ?', 'Second published FAQ answer.', 2, true],
        [Content::TYPE_FAQ, 'First Published FAQ?', 'First published FAQ answer.', 1, true],
        [Content::TYPE_FAQ, 'Draft FAQ?', 'Draft FAQ answer.', 0, false],
        [Content::TYPE_GALLERY, 'Published Gallery', 'Published gallery description.', 1, true],
        [Content::TYPE_GALLERY, 'Draft Gallery', null, 0, false],
        [Content::TYPE_PARTNER, 'Published Partner', 'Published partner description.', 1, true],
        [Content::TYPE_PARTNER, 'Draft Partner', null, 0, false],
    ] as [$type, $title, $body, $order, $published]) {
        Content::query()->create([
            'type' => $type,
            'title' => $title,
            'slug' => str($title)->slug(),
            'body' => $body,
            'image' => $type === Content::TYPE_GALLERY ? 'images/basic-gallery.jpg' : null,
            'display_order' => $order,
            'is_published' => $published,
        ]);
    }

    $this->get(route('public.faq.index'))
        ->assertOk()
        ->assertSeeInOrder(['First Published FAQ?', 'Second Published FAQ?'])
        ->assertDontSee('Draft FAQ?');

    $this->get(route('public.gallery.index'))
        ->assertOk()
        ->assertSee('Published Gallery')
        ->assertSee('Published gallery description.')
        ->assertSee('images/basic-gallery.jpg')
        ->assertDontSee('Draft Gallery');

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('Published Partner')
        ->assertDontSee('Draft Partner');
});

it('renders published testimonials with bounded ratings and optional photos', function () {
    foreach ([
        ['Photo Testimonial', 'Photo message.', 'images/photo-testimonial.jpg', 3, 1, true],
        ['Initial Only Testimonial', 'Initial message.', null, 5, 2, true],
        ['Low Rating Testimonial', 'Low rating message.', null, 0, 3, true],
        ['High Rating Testimonial', 'High rating message.', null, 8, 4, true],
        ['Draft Testimonial', 'Draft message.', null, 5, 0, false],
    ] as [$title, $body, $image, $rating, $order, $published]) {
        Content::query()->create([
            'type' => Content::TYPE_TESTIMONIAL,
            'title' => $title,
            'slug' => str($title)->slug(),
            'body' => $body,
            'image' => $image,
            'meta' => ['role' => 'Siswa', 'rating' => $rating],
            'display_order' => $order,
            'is_published' => $published,
        ]);
    }

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('Photo Testimonial')
        ->assertSee('images/photo-testimonial.jpg')
        ->assertSee('Initial Only Testimonial')
        ->assertSee('IO')
        ->assertSee('data-testimonial-rating="3"', false)
        ->assertSee('data-testimonial-rating="1"', false)
        ->assertSee('data-testimonial-rating="5"', false)
        ->assertDontSee('Draft Testimonial');
});

it('uses published FAQ and profile settings in the chatbot', function () {
    config([
        'rag.nvidia.api_key' => null,
        'rag.qdrant.url' => null,
    ]);

    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'Chatbot Profile',
        'slug' => 'etc-profile',
        'meta' => [
            'address' => 'Jl. Chatbot Sprint 5',
            'whatsapp' => '081299998888',
            'instagram' => 'https://instagram.example.test/chatbot',
        ],
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_FAQ,
        'title' => 'Apakah tersedia kelas akhir pekan?',
        'slug' => 'kelas-akhir-pekan',
        'body' => 'Kelas akhir pekan tersedia sesuai kuota.',
        'is_published' => true,
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'sprint-5-faq',
        'message' => 'Saya mencari kelas akhir pekan.',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('reply', 'Kelas akhir pekan tersedia sesuai kuota.');

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'sprint-5-contact',
        'message' => 'Di mana alamat dan WhatsApp ETC?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath(
            'reply',
            'ETC Planet berlokasi di Jl. Chatbot Sprint 5. Kamu dapat menghubungi 081299998888 untuk konsultasi. Instagram ETC Planet: https://instagram.example.test/chatbot.',
        );
});

it('shows honest empty states without hardcoded CMS or organization data', function () {
    config([
        'rag.nvidia.api_key' => null,
        'rag.qdrant.url' => null,
    ]);

    expect(app(PublicDiscoveryService::class)->faqItems())->toBe([])
        ->and(app(PublicDiscoveryService::class)->settings())->toBe([]);

    $this->get(route('public.about'))
        ->assertOk()
        ->assertSee('Profil ETC Planet belum dipublikasikan')
        ->assertDontSee('Menjadi pusat pembelajaran bahasa yang kredibel');

    $this->get(route('public.contact.index'))
        ->assertOk()
        ->assertSee('Detail kontak belum dipublikasikan')
        ->assertDontSee('Jl. S. Parman No. 202B')
        ->assertDontSee('+62 812-0000-0000');

    $this->get(route('public.faq.index'))
        ->assertOk()
        ->assertSee('FAQ belum tersedia')
        ->assertDontSee('Bagaimana cara mendaftar?');

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('FAQ belum tersedia')
        ->assertSee('Testimoni belum tersedia')
        ->assertDontSee('Andi Darmawan');

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'sprint-5-empty-contact',
        'message' => 'Di mana alamat ETC?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag_no_context')
        ->assertJsonPath(
            'reply',
            'Aku tidak tahu berdasarkan knowledge base ETC Planet saat ini.',
        );
});

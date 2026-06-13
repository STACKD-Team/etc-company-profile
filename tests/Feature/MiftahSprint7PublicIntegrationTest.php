<?php

use App\Models\ChatbotLog;
use App\Models\Content;
use App\Models\Program;
use App\Models\Reel;
use App\Models\Room;
use App\Services\PublicDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'cloudinary.cloud_name' => 'miftah-sprint-7',
        'cloudinary.api_key' => 'test-key',
        'cloudinary.api_secret' => 'test-secret',
        'cloudinary.url' => null,
        'rag.nvidia.api_key' => 'nvidia-test-key',
        'rag.nvidia.base_url' => 'https://nvidia.example.test/v1',
        'rag.nvidia.model' => 'test-chat-model',
        'rag.nvidia.embedding_model' => 'test-embedding-model',
        'rag.qdrant.url' => 'https://qdrant.example.test',
        'rag.qdrant.api_key' => 'qdrant-test-key',
        'rag.qdrant.collection' => 'etc-test-knowledge',
        'rag.min_score' => 0.60,
    ]);
});

it('renders Cloudinary images and videos throughout Miftah public discovery', function () {
    Content::query()->create([
        'type' => Content::TYPE_PROFILE,
        'title' => 'Sprint 7 Profile',
        'slug' => 'etc-profile',
        'image' => 'cloudinary://public/profile/hero',
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_GALLERY,
        'title' => 'Sprint 7 Gallery',
        'slug' => 'sprint-7-gallery',
        'image' => 'cloudinary://public/gallery/main',
        'images' => ['cloudinary://public/gallery/extra'],
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_PARTNER,
        'title' => 'Sprint 7 Partner',
        'slug' => 'sprint-7-partner',
        'image' => 'cloudinary://public/partners/logo',
        'is_published' => true,
    ]);
    Content::query()->create([
        'type' => Content::TYPE_TESTIMONIAL,
        'title' => 'Sprint 7 Testimonial',
        'slug' => 'sprint-7-testimonial',
        'image' => 'cloudinary://public/testimonials/photo',
        'meta' => ['rating' => 5],
        'is_published' => true,
    ]);
    Room::query()->create([
        'name' => 'Sprint 7 Cloud Room',
        'image' => 'cloudinary://public/rooms/photo',
        'is_active' => true,
    ]);
    $program = Program::query()->create([
        'name' => 'Sprint 7 Cloud Program',
        'slug' => 'sprint-7-cloud-program',
        'category' => 'english',
        'thumbnail' => 'cloudinary://public/programs/cover',
        'is_active' => true,
    ]);
    Reel::query()->create([
        'title' => 'Sprint 7 Cloud Reel',
        'video_path' => 'cloudinary://public/reels/video',
        'thumbnail_path' => 'cloudinary://public/reels/poster',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('/image/upload/', false)
        ->assertSee('public/profile/hero', false)
        ->assertSee('public/partners/logo', false)
        ->assertSee('public/testimonials/photo', false)
        ->assertSee('public/reels/poster', false)
        ->assertSee('/video/upload/', false)
        ->assertSee('public/reels/video', false);

    $this->get(route('public.programs.index'))
        ->assertOk()
        ->assertSee('/image/upload/', false)
        ->assertSee('public/programs/cover', false);

    $this->get(route('public.programs.show', $program))
        ->assertOk()
        ->assertSee('/image/upload/', false)
        ->assertSee('public/programs/cover', false);

    $this->get(route('public.gallery.index'))
        ->assertOk()
        ->assertSee('public/gallery/main', false)
        ->assertSee('public/gallery/extra', false);

    $this->get(route('public.facilities.index'))
        ->assertOk()
        ->assertSee('public/rooms/photo', false);

    $this->get(route('public.reels.index'))
        ->assertOk()
        ->assertSee('/image/upload/', false)
        ->assertSee('public/reels/poster', false)
        ->assertSee('/video/upload/', false)
        ->assertSee('public/reels/video', false);
});

it('keeps remote, local, and fallback media URL behavior compatible', function () {
    $media = app(PublicDiscoveryService::class);

    expect($media->mediaUrl('https://cdn.example.test/photo.jpg'))
        ->toBe('https://cdn.example.test/photo.jpg')
        ->and($media->mediaUrl('images/local-photo.jpg'))
        ->toBe(asset('images/local-photo.jpg'))
        ->and($media->mediaUrl(null, 'images/fallback.jpg'))
        ->toBe(asset('images/fallback.jpg'));

    config([
        'cloudinary.cloud_name' => null,
        'cloudinary.api_key' => null,
        'cloudinary.api_secret' => null,
    ]);

    expect($media->mediaUrl('cloudinary://public/missing/photo', 'images/fallback.jpg'))
        ->toBe(asset('images/fallback.jpg'));
});

it('answers the public chatbot from Qdrant context and NVIDIA completion', function () {
    Http::fake([
        'https://nvidia.example.test/v1/embeddings' => Http::response([
            'data' => [['embedding' => [0.1, 0.2, 0.3]]],
        ]),
        'https://qdrant.example.test/collections/etc-test-knowledge/points/search' => Http::response([
            'result' => [[
                'score' => 0.91,
                'payload' => [
                    'content' => 'Placement test ETC dilakukan secara offline.',
                    'metadata' => ['source' => 'placement-test'],
                ],
            ]],
        ]),
        'https://nvidia.example.test/v1/chat/completions' => Http::response([
            'choices' => [[
                'message' => ['content' => 'Placement test ETC dilaksanakan secara offline.'],
            ]],
        ]),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-rag',
        'message' => 'Bagaimana pelaksanaan placement test?',
    ])
        ->assertOk()
        ->assertExactJson([
            'status' => 'ok',
            'session_id' => 'miftah-sprint-7-rag',
            'intent' => 'rag',
            'reply' => 'Placement test ETC dilaksanakan secara offline.',
        ]);

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-rag')
        ->where('intent', 'rag')
        ->where('bot_response', 'Placement test ETC dilaksanakan secara offline.')
        ->exists())->toBeTrue();

    Http::assertSentCount(3);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://qdrant.example.test/collections/etc-test-knowledge/points/search'
        && $request->hasHeader('api-key', 'qdrant-test-key')
        && $request['limit'] === 5);
});

it('falls back to current public data when retrieved knowledge is not relevant', function () {
    $program = Program::query()->create([
        'name' => 'Sprint 7 Pricing Program',
        'slug' => 'sprint-7-pricing-program',
        'category' => 'english',
        'price' => 1500000,
        'registration_fee' => 100000,
        'is_active' => true,
    ]);

    Http::fake([
        'https://nvidia.example.test/v1/embeddings' => Http::response([
            'data' => [['embedding' => [0.1, 0.2, 0.3]]],
        ]),
        'https://qdrant.example.test/collections/etc-test-knowledge/points/search' => Http::response([
            'result' => [[
                'score' => 0.59,
                'payload' => [
                    'content' => 'Konteks yang tidak cukup relevan.',
                    'metadata' => [],
                ],
            ]],
        ]),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-low-score',
        'message' => 'Berapa biaya program?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'pricing')
        ->assertJsonPath(
            'reply',
            'Biaya pendaftaran mulai dari Rp 100.000. Biaya program saat ini Rp 1.500.000. Buka halaman Program untuk melihat harga dan promo aktif setiap kelas.',
        );

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-low-score')
        ->where('intent', 'pricing')
        ->exists())->toBeTrue();
    Http::assertSentCount(2);
});

it('returns a safe fallback instead of HTTP 500 when an external RAG service fails', function () {
    Http::fake([
        'https://nvidia.example.test/v1/embeddings' => Http::failedConnection('Connection timed out.'),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-timeout',
        'message' => 'Ceritakan informasi yang belum ada di database publik.',
    ])
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('session_id', 'miftah-sprint-7-timeout')
        ->assertJsonPath('intent', 'general')
        ->assertJsonPath('reply', 'Halo! Aku bisa bantu jawab tentang program, biaya, jadwal, pendaftaran, placement test, dan kontak ETC Planet.');

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-timeout')
        ->where('intent', 'general')
        ->exists())->toBeTrue();
});

it('returns a safe fallback when an external RAG service responds with an HTTP error', function () {
    Http::fake([
        'https://nvidia.example.test/v1/embeddings' => Http::response([
            'message' => 'Upstream service unavailable.',
        ], 503),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-http-error',
        'message' => 'Apa saja informasi umum ETC?',
    ])
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('intent', 'general');

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-http-error')
        ->where('intent', 'general')
        ->exists())->toBeTrue();
});

it('keeps home reel cards clickable while preserving drag navigation', function () {
    $reel = Reel::query()->create([
        'title' => 'Sprint 7 Clickable Home Reel',
        'video_path' => 'videos/video1.mp4',
        'thumbnail_path' => 'images/pu1-img.jpg',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('data-home-reel-link', false)
        ->assertSee('href="'.route('public.reels.index', ['reel' => $reel->id]).'"', false);

    $javascript = file_get_contents(resource_path('js/app.js'));
    $pointerDownBlock = str($javascript)
        ->after("viewport.addEventListener('pointerdown'")
        ->before("viewport.addEventListener('pointermove'")
        ->toString();
    $pointerMoveBlock = str($javascript)
        ->after("viewport.addEventListener('pointermove'")
        ->before('const finishDrag')
        ->toString();

    expect($pointerDownBlock)
        ->not->toContain('setPointerCapture')
        ->and($pointerMoveBlock)
        ->toContain('if (!isDragging && Math.abs(distance) > 6)')
        ->toContain('viewport.setPointerCapture?.(event.pointerId)');
});

it('allows the home reels carousel to move right manually', function () {
    foreach (range(1, 3) as $index) {
        Reel::query()->create([
            'title' => 'Sprint 7 Sliding Reel '.$index,
            'video_path' => 'videos/video1.mp4',
            'thumbnail_path' => 'images/pu1-img.jpg',
            'is_published' => true,
            'published_at' => now()->subMinutes($index),
        ]);
    }

    $this->get(route('public.home'))
        ->assertOk()
        ->assertSee('data-home-reels-carousel', false)
        ->assertSee('data-carousel-autoplay="false"', false)
        ->assertSee('data-carousel-next', false);

    $javascript = file_get_contents(resource_path('js/app.js'));

    expect($javascript)
        ->toContain('const moveBy = (direction) => {')
        ->toContain('left: slideStep() * direction')
        ->toContain('moveBy(1);')
        ->toContain('window.setTimeout(resetLoopPosition, reducedMotion ? 0 : 520)');
});

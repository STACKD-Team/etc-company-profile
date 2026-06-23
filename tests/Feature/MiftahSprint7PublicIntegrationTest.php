<?php

use App\Models\ChatbotLog;
use App\Models\Content;
use App\Models\Program;
use App\Models\RagKnowledgeChunk;
use App\Models\RagKnowledgeSource;
use App\Models\Reel;
use App\Models\Room;
use App\Services\MediaStorageService;
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

it('builds public storage media URLs from the active application origin', function () {
    app('url')->forceRootUrl('http://127.0.0.1:8000');

    expect(app(MediaStorageService::class)->url('contents/images/partner.png'))
        ->toBe('http://127.0.0.1:8000/storage/contents/images/partner.png');
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
        'https://nvidia.example.test/v1/chat/completions' => Http::sequence()
            ->push([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'scope' => 'etc',
                        'retrieval_query' => 'placement test ETC Planet',
                        'public_data_needed' => [],
                    ])],
                ]],
            ])
            ->push([
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

    Http::assertSentCount(4);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://qdrant.example.test/collections/etc-test-knowledge/points/search'
        && $request->hasHeader('api-key', 'qdrant-test-key')
        && $request['limit'] === 5);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://nvidia.example.test/v1/embeddings'
        && $request['input_type'] === 'query');
});

it('uses allowlisted public program data and links when retrieved knowledge is not relevant', function () {
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
        'https://nvidia.example.test/v1/chat/completions' => Http::sequence()
            ->push([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'scope' => 'etc',
                        'retrieval_query' => 'biaya program ETC Planet',
                        'public_data_needed' => ['programs'],
                    ])],
                ]],
            ])
            ->push([
                'choices' => [[
                    'message' => ['content' => 'Biaya program Sprint 7 Pricing Program adalah Rp 1.500.000 dan biaya pendaftarannya Rp 100.000.'],
                ]],
            ]),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-low-score',
        'message' => 'Berapa biaya program?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('reply', 'Biaya program Sprint 7 Pricing Program adalah Rp 1.500.000 dan biaya pendaftarannya Rp 100.000.')
        ->assertJsonPath('links.0.label', 'Sprint 7 Pricing Program')
        ->assertJsonPath('links.0.url', route('public.programs.show', $program, false));

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-low-score')
        ->where('intent', 'rag')
        ->exists())->toBeTrue();
    Http::assertSentCount(4);
});

it('does not expose inactive public program data in chatbot links', function () {
    Program::query()->create([
        'name' => 'Inactive Secret Program',
        'slug' => 'inactive-secret-program',
        'category' => 'english',
        'price' => 999999,
        'registration_fee' => 100000,
        'is_active' => false,
    ]);
    $active = Program::query()->create([
        'name' => 'Active English Program',
        'slug' => 'active-english-program',
        'category' => 'english',
        'price' => 1500000,
        'registration_fee' => 100000,
        'is_active' => true,
    ]);

    Http::fake([
        'https://nvidia.example.test/v1/embeddings' => Http::response([
            'data' => [['embedding' => [0.1, 0.2, 0.3]]],
        ]),
        'https://qdrant.example.test/collections/etc-test-knowledge/points/search' => Http::response(['result' => []]),
        'https://nvidia.example.test/v1/chat/completions' => Http::sequence()
            ->push([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'scope' => 'etc',
                        'retrieval_query' => 'program bahasa Inggris ETC Planet',
                        'public_data_needed' => ['programs'],
                    ])],
                ]],
            ])
            ->push([
                'choices' => [[
                    'message' => ['content' => 'Untuk latihan bahasa Inggris, kamu bisa melihat Active English Program.'],
                ]],
            ]),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-active-only',
        'message' => 'Program yang bagus untuk melatih bahasa Inggris?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('links.0.label', 'Active English Program')
        ->assertJsonMissing(['label' => 'Inactive Secret Program'])
        ->assertJsonPath('links.0.url', route('public.programs.show', $active, false));
});

it('refuses chatbot questions outside ETC Planet Padang context', function () {
    Http::fake([
        'https://nvidia.example.test/v1/chat/completions' => Http::response([
            'choices' => [[
                'message' => ['content' => json_encode([
                    'scope' => 'out_of_scope',
                    'retrieval_query' => 'presiden Amerika',
                    'public_data_needed' => [],
                ])],
            ]],
        ]),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-out-of-scope',
        'message' => 'Siapa presiden Amerika?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag_out_of_scope')
        ->assertJsonPath('reply', 'Aku hanya bisa membantu menjawab informasi seputar ETC Planet Padang.')
        ->assertJsonMissingPath('links');

    Http::assertSentCount(1);
});

it('falls back to allowlisted public data when the AI router is unavailable', function () {
    $program = Program::query()->create([
        'name' => 'English Conversation Fallback',
        'slug' => 'english-conversation-fallback',
        'category' => 'english',
        'description' => 'Program untuk melatih percakapan bahasa Inggris.',
        'price' => 1500000,
        'registration_fee' => 100000,
        'is_active' => true,
    ]);

    Http::fake([
        'https://nvidia.example.test/v1/chat/completions' => Http::response([
            'message' => 'Router unavailable.',
        ], 503),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-public-fallback',
        'message' => 'Program disini apa saja?',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('links.0.label', 'English Conversation Fallback')
        ->assertJsonPath('links.0.url', route('public.programs.show', $program, false));
});

it('falls back to local indexed knowledge chunks when vector retrieval is unavailable', function () {
    $source = RagKnowledgeSource::query()->create([
        'title' => 'Company Profile',
        'source_type' => 'upload',
        'status' => 'ready',
        'is_active' => true,
        'extracted_text' => 'Company profile ETC.',
    ]);
    RagKnowledgeChunk::query()->create([
        'knowledge_source_id' => $source->id,
        'qdrant_point_id' => 'local-founder-point',
        'chunk_index' => 0,
        'content' => 'Saya, Debby Susiyanti, S.Pd, sebagai pemilik Lembaga Pendidikan dan Pelatihan Kerja ETC. Debby Susiyanti, S.Pd adalah Managing Director.',
        'metadata' => ['title' => 'Company Profile'],
        'embedding_model' => 'test',
    ]);

    Http::fake([
        'https://nvidia.example.test/v1/chat/completions' => Http::response([
            'message' => 'Router unavailable.',
        ], 503),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-local-founder',
        'message' => 'siapa pendiri ETC Padang',
    ])
        ->assertOk()
        ->assertJsonPath('intent', 'rag')
        ->assertJsonPath('reply', 'Debby Susiyanti, S.Pd adalah pemilik Lembaga Pendidikan dan Pelatihan Kerja ETC sekaligus Managing Director.');
});

it('renders chatbot links safely from structured response data', function () {
    $javascript = file_get_contents(resource_path('js/app.js'));

    expect($javascript)
        ->toContain('const appendLinks = (container, links = [], isPublic = false) => {')
        ->toContain("const anchor = document.createElement('a');")
        ->toContain('anchor.textContent = link.label')
        ->toContain('data?.links || []')
        ->toContain('bubble.textContent = message')
        ->toContain("const messagesStorageKey = 'etc_public_chatbot_messages'")
        ->toContain('const restoreMessages = () => {')
        ->toContain('window.localStorage?.setItem(messagesStorageKey')
        ->not->toContain('bubble.innerHTML = message');
});

it('returns a safe fallback instead of HTTP 500 when an external RAG service fails', function () {
    Http::fake([
        'https://nvidia.example.test/v1/chat/completions' => Http::response([
            'choices' => [[
                'message' => ['content' => json_encode([
                    'scope' => 'etc',
                    'retrieval_query' => 'informasi database publik ETC Planet',
                    'public_data_needed' => [],
                ])],
            ]],
        ]),
        'https://nvidia.example.test/v1/embeddings' => Http::failedConnection('Connection timed out.'),
    ]);

    $this->postJson(route('public.chatbot.messages.store'), [
        'session_id' => 'miftah-sprint-7-timeout',
        'message' => 'Ceritakan informasi yang belum ada di database publik.',
    ])
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('session_id', 'miftah-sprint-7-timeout')
        ->assertJsonPath('intent', 'rag_no_context')
        ->assertJsonPath('reply', 'Aku tidak tahu berdasarkan knowledge base ETC Planet saat ini.');

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-timeout')
        ->where('intent', 'rag_no_context')
        ->exists())->toBeTrue();
});

it('returns a safe fallback when an external RAG service responds with an HTTP error', function () {
    Http::fake([
        'https://nvidia.example.test/v1/chat/completions' => Http::response([
            'choices' => [[
                'message' => ['content' => json_encode([
                    'scope' => 'etc',
                    'retrieval_query' => 'informasi umum ETC',
                    'public_data_needed' => [],
                ])],
            ]],
        ]),
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
        ->assertJsonPath('intent', 'rag_no_context');

    expect(ChatbotLog::query()->where('session_id', 'miftah-sprint-7-http-error')
        ->where('intent', 'rag_no_context')
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

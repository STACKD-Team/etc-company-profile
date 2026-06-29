<?php

use App\Models\Program;
use App\Models\ProgramPromotion;
use App\Models\RagKnowledgeSource;
use App\Models\Registration;
use App\Models\User;
use App\Services\KnowledgeSourceService;
use App\Services\MediaStorageService;
use App\Services\MidtransPaymentService;
use App\Services\RagChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    if (! filter_var(env('SPRINT8_EXTERNAL_TESTS', false), FILTER_VALIDATE_BOOLEAN)) {
        $this->markTestSkipped('Set SPRINT8_EXTERNAL_TESTS=true to run mandatory Sprint 8 external integration checks.');
    }

    if (! sprint8ExternalHostAvailable('app.sandbox.midtrans.com')) {
        $this->markTestSkipped('External Sprint 8 services are not reachable from this environment.');
    }

    config([
        'midtrans.server_key' => env('MIDTRANS_SERVER_KEY'),
        'midtrans.client_key' => env('MIDTRANS_CLIENT_KEY'),
        'cloudinary.cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'cloudinary.api_key' => env('CLOUDINARY_API_KEY'),
        'cloudinary.api_secret' => env('CLOUDINARY_API_SECRET'),
        'cloudinary.url' => env('CLOUDINARY_URL'),
        'cloudinary.allow_test_uploads' => true,
    ]);
});

it('requires every mandatory external Sprint 8 credential when enabled', function () {
    expectMissingExternalEnv([
        'MIDTRANS_SERVER_KEY',
        'MIDTRANS_CLIENT_KEY',
        'CLOUDINARY_CLOUD_NAME',
        'CLOUDINARY_API_KEY',
        'CLOUDINARY_API_SECRET',
        'NVIDIA_API_KEY',
        'NVIDIA_EMBEDDING_MODEL',
        'QDRANT_URL',
        'QDRANT_API_KEY',
        'QDRANT_COLLECTION',
    ]);

    expect((bool) config('cloudinary.allow_test_uploads'))
        ->toBeTrue('Set CLOUDINARY_ALLOW_TEST_UPLOADS=true for Sprint 8 external upload verification.');
});

it('creates a real Midtrans sandbox transaction with the promo final amount', function () {
    expectMissingExternalEnv(['MIDTRANS_SERVER_KEY', 'MIDTRANS_CLIENT_KEY']);

    $program = sprint8ExternalProgram();
    ProgramPromotion::query()->create([
        'program_id' => $program->id,
        'title' => 'External Sprint 8 Promo',
        'discount_type' => 'fixed',
        'discount_value' => 150000,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $snapshot = app(MidtransPaymentService::class)->snapshotAmount($program->refresh());
    $registration = Registration::query()->create([
        'registration_code' => 'REG-SPRINT8-EXT-'.str()->upper(str()->random(8)),
        'program_id' => $program->id,
        'applicant_name' => 'Sprint 8 External',
        'applicant_email' => 'sprint8.external@example.test',
        'applicant_phone' => '081234567890',
        'payment_method' => 'bank_transfer',
        'status' => 'pending_payment',
        ...$snapshot,
    ]);

    $registration = app(MidtransPaymentService::class)->createTransaction($registration);

    expect($registration->midtrans_snap_token)->not->toBeNull()
        ->and($registration->midtrans_snap_token)->not->toStartWith('demo-')
        ->and($registration->midtrans_redirect_url)->toContain('midtrans')
        ->and((float) $registration->final_amount)->toBe(1250000.0)
        ->and($registration->payment_status)->toBe('waiting_payment');
});

it('uploads replaces previews and deletes image video and document files in Cloudinary', function () {
    expectMissingExternalEnv(['CLOUDINARY_CLOUD_NAME', 'CLOUDINARY_API_KEY', 'CLOUDINARY_API_SECRET']);
    expect((bool) config('cloudinary.allow_test_uploads'))->toBeTrue();

    $storage = app(MediaStorageService::class);

    foreach ([
        UploadedFile::fake()->image('sprint8-image.jpg', 320, 180),
        UploadedFile::fake()->createWithContent('sprint8-video.mp4', str_repeat('0', 8192), 'video/mp4'),
        UploadedFile::fake()->createWithContent('sprint8-document.txt', 'ETC Planet Sprint 8 document.'),
    ] as $file) {
        $path = $storage->putUploadedFile($file, 'sprint8/external');

        expect($path)->toStartWith('cloudinary://')
            ->and($storage->url($path))->toStartWith('https://res.cloudinary.com/');

        $replacement = $storage->replace($path, UploadedFile::fake()->createWithContent('replacement.txt', 'replacement'), 'sprint8/external');
        expect($replacement)->toStartWith('cloudinary://');
        $storage->delete($replacement);
    }
});

it('uploads indexes retrieves and answers from real Qdrant and NVIDIA RAG services', function () {
    expectMissingExternalEnv(['NVIDIA_API_KEY', 'NVIDIA_EMBEDDING_MODEL', 'QDRANT_URL', 'QDRANT_API_KEY', 'QDRANT_COLLECTION']);

    Config::set('cloudinary.allow_test_uploads', true);
    Config::set('rag.min_score', min((float) config('rag.min_score', 0.60), 0.50));

    $admin = User::factory()->create(['role' => 'admin']);
    $source = app(KnowledgeSourceService::class)->createFromUpload(
        ['title' => 'Sprint 8 External Knowledge', 'is_active' => true],
        UploadedFile::fake()->createWithContent('sprint8-knowledge.txt', str_repeat('ETC Planet memiliki program Teen English dan TOEFL Preparation di Padang. ', 20)),
        $admin->id,
        false,
    );

    $source = app(KnowledgeSourceService::class)->indexNow($source);

    expect($source->status)->toBe('ready')
        ->and($source->chunks()->count())->toBeGreaterThan(0)
        ->and(RagKnowledgeSource::query()->whereKey($source->id)->where('is_active', true)->exists())->toBeTrue();

    Config::set('rag.nvidia.model', null);

    $answer = app(RagChatService::class)->answer('Program Teen English ETC Planet ada?');

    expect($answer['intent'])->toBe('rag')
        ->and($answer['contexts'])->not->toBeEmpty()
        ->and($answer['reply'])->not->toBe('');
});

function expectMissingExternalEnv(array $keys): void
{
    $missing = collect($keys)->filter(fn (string $key): bool => blank(env($key)));

    expect($missing->values()->all())
        ->toBe([], 'Missing required Sprint 8 external env values: '.$missing->implode(', '));
}

function sprint8ExternalHostAvailable(string $host): bool
{
    $connection = @fsockopen($host, 443, $errorCode, $errorMessage, 1.0);

    if (! is_resource($connection)) {
        return false;
    }

    fclose($connection);

    return true;
}

function sprint8ExternalProgram(): Program
{
    $suffix = str()->lower(str()->random(8));

    return Program::query()->create([
        'name' => 'Sprint 8 External Program',
        'slug' => 'sprint-8-external-'.$suffix,
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
}

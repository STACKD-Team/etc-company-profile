<?php

use App\Models\MidtransNotification;
use App\Models\Program;
use App\Models\ProgramPromotion;
use App\Models\RagKnowledgeChunk;
use App\Models\RagKnowledgeSource;
use App\Models\Registration;
use App\Models\User;
use App\Services\EmbeddingService;
use App\Services\KnowledgeSourceService;
use App\Services\MediaStorageService;
use App\Services\QdrantVectorService;
use App\Services\RagChatService;
use App\Services\TextExtractionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('sprint 7 registration creates midtrans order and promo snapshot', function () {
    Config::set('midtrans.server_key', null);

    $program = miaSprint7Program();
    $promotion = ProgramPromotion::query()->create([
        'program_id' => $program->id,
        'title' => 'Promo Sprint 7',
        'discount_type' => 'fixed',
        'discount_value' => 150000,
        'is_active' => true,
    ]);

    $this->post(route('registrations.store'), miaSprint7RegistrationPayload($program))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $registration = Registration::query()->firstOrFail();

    expect($registration->midtrans_order_id)->toStartWith('ETC-'.$registration->registration_code)
        ->and($registration->midtrans_snap_token)->toStartWith('demo-')
        ->and($registration->payment_status)->toBe('waiting_payment')
        ->and((float) $registration->original_amount)->toBe(1400000.0)
        ->and((float) $registration->discount_amount)->toBe(150000.0)
        ->and((float) $registration->final_amount)->toBe(1250000.0)
        ->and($registration->program_promotion_id)->toBe($promotion->id)
        ->and($registration->program_promotion_title)->toBe('Promo Sprint 7');
});

test('sprint 7 valid settlement webhook marks registration paid and stores audit payload', function () {
    Config::set('midtrans.server_key', 'sprint7-secret');

    $registration = miaSprint7PaymentRegistration(['final_amount' => 1250000]);
    $payload = miaSprint7MidtransPayload($registration, 'settlement', 1250000);

    $this->postJson(route('payments.midtrans.notification'), $payload)
        ->assertOk()
        ->assertJson(['status' => 'processed']);

    $registration->refresh();

    expect($registration->status)->toBe('paid')
        ->and($registration->payment_status)->toBe('paid')
        ->and($registration->paid_at)->not->toBeNull()
        ->and(MidtransNotification::query()->where('processing_status', 'processed')->count())->toBe(1);
});

test('sprint 7 duplicate midtrans webhook is idempotent', function () {
    Config::set('midtrans.server_key', 'sprint7-secret');

    $registration = miaSprint7PaymentRegistration(['final_amount' => 1250000]);
    $payload = miaSprint7MidtransPayload($registration, 'settlement', 1250000);

    $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();
    $paidAt = $registration->refresh()->paid_at?->toISOString();
    $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();

    expect(MidtransNotification::query()->count())->toBe(1)
        ->and($registration->refresh()->paid_at?->toISOString())->toBe($paidAt);
});

test('sprint 7 midtrans webhook maps non paid statuses consistently', function (string $midtransStatus, string $fraudStatus, string $paymentStatus, string $registrationStatus) {
    Config::set('midtrans.server_key', 'sprint7-secret');

    $registration = miaSprint7PaymentRegistration([
        'registration_code' => 'REG-SPRINT7-'.Str::upper(Str::random(8)),
        'midtrans_order_id' => 'ETC-REG-SPRINT7-'.Str::upper(Str::random(8)),
        'final_amount' => 1250000,
    ]);
    $payload = miaSprint7MidtransPayload($registration, $midtransStatus, 1250000);
    $payload['fraud_status'] = $fraudStatus;
    $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));

    $this->postJson(route('payments.midtrans.notification'), $payload)
        ->assertOk()
        ->assertJson(['status' => 'processed']);

    $registration->refresh();

    expect($registration->payment_status)->toBe($paymentStatus)
        ->and($registration->status)->toBe($registrationStatus);
})->with([
    'pending' => ['pending', 'accept', 'waiting_payment', 'pending_payment'],
    'capture challenge' => ['capture', 'challenge', 'waiting_payment', 'pending_payment'],
    'expire' => ['expire', 'accept', 'expired', 'cancelled'],
    'cancel' => ['cancel', 'accept', 'cancelled', 'cancelled'],
    'deny' => ['deny', 'deny', 'failed', 'rejected'],
    'failure' => ['failure', 'accept', 'failed', 'rejected'],
]);

test('sprint 7 late non paid webhook does not downgrade a paid registration', function () {
    Config::set('midtrans.server_key', 'sprint7-secret');

    $registration = miaSprint7PaymentRegistration([
        'final_amount' => 1250000,
        'payment_status' => 'paid',
        'status' => 'paid',
        'paid_at' => now(),
    ]);
    $payload = miaSprint7MidtransPayload($registration, 'expire', 1250000);

    $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();

    expect($registration->refresh()->payment_status)->toBe('paid')
        ->and($registration->status)->toBe('paid');
});

test('sprint 7 invalid signature and mismatched amount do not mark registration paid', function () {
    Config::set('midtrans.server_key', 'sprint7-secret');

    $invalidSignatureRegistration = miaSprint7PaymentRegistration([
        'registration_code' => 'REG-SPRINT7-SIGNATURE',
        'midtrans_order_id' => 'ETC-REG-SPRINT7-SIGNATURE-1',
        'final_amount' => 1250000,
    ]);
    $badSignaturePayload = miaSprint7MidtransPayload($invalidSignatureRegistration, 'settlement', 1250000);
    $badSignaturePayload['signature_key'] = 'bad-signature';

    $this->postJson(route('payments.midtrans.notification'), $badSignaturePayload)->assertOk();

    $amountMismatchRegistration = miaSprint7PaymentRegistration([
        'registration_code' => 'REG-SPRINT7-AMOUNT',
        'midtrans_order_id' => 'ETC-REG-SPRINT7-AMOUNT-1',
        'final_amount' => 1250000,
    ]);
    $amountMismatchPayload = miaSprint7MidtransPayload($amountMismatchRegistration, 'settlement', 1200000);

    $this->postJson(route('payments.midtrans.notification'), $amountMismatchPayload)->assertOk();

    expect($invalidSignatureRegistration->refresh()->status)->toBe('pending_payment')
        ->and($amountMismatchRegistration->refresh()->status)->toBe('pending_payment')
        ->and(MidtransNotification::query()->where('processing_status', 'failed')->count())->toBe(2);
});

test('sprint 7 media storage local fallback and cloudinary path helpers work without real credentials', function () {
    Storage::fake('public');
    Config::set('cloudinary.cloud_name', 'etc-demo');
    Config::set('cloudinary.api_key', null);
    Config::set('cloudinary.api_secret', null);
    Config::set('cloudinary.url', null);

    $service = new MediaStorageService();
    $path = $service->putUploadedFile(UploadedFile::fake()->createWithContent('legacy.txt', 'legacy file'), 'legacy');

    Storage::disk('public')->assertExists($path);
    expect($service->url($path))->toContain('/storage/')
        ->and($service->url('cloudinary://raw/rag/source.pdf?v=123'))->toBe('https://res.cloudinary.com/etc-demo/raw/upload/v123/rag/source.pdf')
        ->and($service->url('cloudinary://video/reels/intro.mp4?v=456'))->toBe('https://res.cloudinary.com/etc-demo/video/upload/v456/reels/intro.mp4');

    $service->delete($path);
    Storage::disk('public')->assertMissing($path);
});

test('sprint 7 knowledge upload indexes chunks and reindex replaces old chunks', function () {
    Config::set('rag.qdrant.url', null);
    Config::set('rag.nvidia.api_key', null);
    $this->app->instance(MediaStorageService::class, new MiaSprint7FakeMediaStorageService());

    $source = app(KnowledgeSourceService::class)->createFromUpload(
        ['title' => 'Sprint 7 Knowledge', 'is_active' => true],
        UploadedFile::fake()->createWithContent('knowledge.txt', str_repeat('ETC Planet membuka kelas Teen dan TOEFL. ', 40)),
        null,
        false,
    );

    app(KnowledgeSourceService::class)->indexNow($source);
    $firstChunkCount = RagKnowledgeChunk::query()->where('knowledge_source_id', $source->id)->count();

    app(KnowledgeSourceService::class)->reindex($source);
    app(KnowledgeSourceService::class)->indexNow($source->refresh());

    expect($source->refresh()->status)->toBe('ready')
        ->and($firstChunkCount)->toBeGreaterThan(0)
        ->and(RagKnowledgeChunk::query()->where('knowledge_source_id', $source->id)->count())->toBe($firstChunkCount);
});

test('sprint 7 failed knowledge extraction stores source error', function () {
    $this->app->instance(MediaStorageService::class, new MiaSprint7FakeMediaStorageService());
    $this->app->instance(TextExtractionService::class, new class extends TextExtractionService {
        public function extract(UploadedFile $file): string
        {
            throw new RuntimeException('Extraction failed for Sprint 7.');
        }
    });

    $source = app(KnowledgeSourceService::class)->createFromUpload(
        ['title' => 'Broken Knowledge'],
        UploadedFile::fake()->createWithContent('broken.txt', 'broken'),
        null,
        false,
    );

    expect($source->status)->toBe('failed')
        ->and($source->error_message)->toContain('Extraction failed');
});

test('sprint 7 rag chatbot uses qdrant context and falls back safely', function () {
    Config::set('rag.nvidia.api_key', null);

    $rag = new RagChatService(
        new class extends EmbeddingService {
            public function embed(string $text, string $inputType = 'passage'): array
            {
                return [0.1, 0.2, 0.3];
            }
        },
        new class extends QdrantVectorService {
            public function isConfigured(): bool
            {
                return true;
            }

            public function search(array $vector, int $limit = 5): array
            {
                return [['content' => 'ETC Planet memiliki program Teen English.', 'score' => 0.9, 'metadata' => []]];
            }
        },
    );

    $answer = $rag->answer('Ada program teen?');

    $fallback = (new RagChatService(
        app(EmbeddingService::class),
        new class extends QdrantVectorService {
            public function isConfigured(): bool
            {
                return false;
            }
        },
    ))->answer('Apa saja?');

    expect($answer['intent'])->toBe('rag')
        ->and($answer['reply'])->toContain('ETC Planet')
        ->and($fallback['intent'])->toBe('rag_out_of_scope');
});

test('sprint 7 qdrant service bootstraps missing collection before upsert', function () {
    Config::set('rag.qdrant.url', 'https://qdrant.test');
    Config::set('rag.qdrant.collection', 'etc_planet_knowledge');

    Http::fake([
        'qdrant.test/collections/etc_planet_knowledge' => Http::sequence()
            ->push([], 404)
            ->push(['result' => true], 200),
        'qdrant.test/collections/etc_planet_knowledge/index?wait=true' => Http::response(['result' => true], 200),
        'qdrant.test/collections/etc_planet_knowledge/points?wait=true' => Http::response(['result' => true], 200),
    ]);

    app(QdrantVectorService::class)->upsert('point-1', [0.1, 0.2], ['content' => 'Knowledge']);

    Http::assertSentCount(5);
});

function miaSprint7Program(): Program
{
    $suffix = (string) Str::uuid();

    return Program::query()->create([
        'name' => 'Sprint 7 English',
        'slug' => 'sprint-7-english-'.$suffix,
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
}

function miaSprint7RegistrationPayload(Program $program): array
{
    return [
        'program_id' => $program->id,
        'applying_for' => 'smp_teen',
        'full_name' => 'Mia Sprint Seven',
        'email' => 'mia.sprint7@example.test',
        'mobile_phone' => '081234567890',
        'place_of_birth' => 'Padang',
        'date_of_birth' => '2005-01-01',
        'sex' => 'F',
        'religion' => 'Islam',
        'nationality' => 'Indonesia',
        'occupation_school' => 'SMP Padang',
        'nisn' => '1234567890',
        'nik' => '1371010101010001',
        'kps_receiver' => '0',
        'no_kps' => null,
        'worthy_of_pip' => '0',
        'pip_reason' => null,
        'no_kip' => null,
        'address' => 'Jl. Sprint 7',
        'rt_rw' => '001/002',
        'postal_code' => '25111',
        'village' => 'Ulak Karang',
        'sub_district' => 'Padang Utara',
        'district' => 'Padang',
        'province' => 'Sumatera Barat',
        'living_with' => 'Orang Tua',
        'transportation' => 'Kendaraan Pribadi',
        'mother_name' => 'Ibu Sprint',
        'father_name' => 'Ayah Sprint',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
    ];
}

function miaSprint7PaymentRegistration(array $overrides = []): Registration
{
    $student = User::factory()->create(['role' => 'student']);
    $program = miaSprint7Program();

    return Registration::query()->create($overrides + [
        'registration_code' => 'REG-SPRINT7-PAYMENT',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'applicant_name' => 'Mia Sprint Seven',
        'applicant_email' => 'mia.sprint7.payment@example.test',
        'applicant_phone' => '081234567890',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
        'payment_amount' => 1400000,
        'final_amount' => 1250000,
        'midtrans_order_id' => 'ETC-REG-SPRINT7-PAYMENT-1',
        'payment_gateway_id' => 'ETC-REG-SPRINT7-PAYMENT-1',
        'payment_status' => 'waiting_payment',
        'status' => 'pending_payment',
    ]);
}

function miaSprint7MidtransPayload(Registration $registration, string $status, int $grossAmount): array
{
    $payload = [
        'order_id' => $registration->midtrans_order_id,
        'transaction_status' => $status,
        'transaction_id' => 'MID-'.$registration->id,
        'payment_type' => 'bank_transfer',
        'fraud_status' => 'accept',
        'status_code' => '200',
        'gross_amount' => (string) $grossAmount,
    ];

    $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));

    return $payload;
}

class MiaSprint7FakeMediaStorageService extends MediaStorageService
{
    public function putUploadedFile(UploadedFile $file, string $directory): string
    {
        return trim($directory, '/').'/'.$file->getClientOriginalName();
    }

    public function delete(?string $path): void
    {
        //
    }
}

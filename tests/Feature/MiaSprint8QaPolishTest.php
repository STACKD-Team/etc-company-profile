<?php

use App\Models\MidtransNotification;
use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use App\Services\MediaStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('keeps web route loader clean while preserving the Midtrans webhook contract', function () {
    $webRoutes = file_get_contents(base_path('routes/web.php'));
    $registrationRoutes = file_get_contents(base_path('routes/web/registration.php'));
    $route = Route::getRoutes()->getByName('payments.midtrans.notification');

    expect($webRoutes)
        ->not->toContain('Route::')
        ->not->toContain('MidtransNotificationController')
        ->and($registrationRoutes)
        ->toContain('MidtransNotificationController')
        ->toContain("Route::post('/payments/midtrans/notification'")
        ->and($route)->not->toBeNull()
        ->and($route->uri())->toBe('payments/midtrans/notification')
        ->and($route->methods())->toContain('POST')
        ->and($route->gatherMiddleware())->toContain('throttle:payment')
        ->and(route('payments.midtrans.notification', absolute: false))->toBe('/payments/midtrans/notification');
});

it('processes validated Midtrans webhook fields while storing the raw audit payload', function () {
    Config::set('midtrans.server_key', 'mia-sprint-8-secret');

    $registration = miaSprint8PaymentRegistration();
    $payload = miaSprint8MidtransPayload($registration, 'settlement', 1250000);
    $payload['unexpected_admin_override'] = 'should stay in raw audit only';

    $this->postJson(route('payments.midtrans.notification'), $payload)
        ->assertOk()
        ->assertJson(['status' => 'processed']);

    $notification = MidtransNotification::query()->firstOrFail();

    expect($registration->refresh()->status)->toBe('paid')
        ->and($registration->payment_status)->toBe('paid')
        ->and($notification->raw_payload)->toHaveKey('unexpected_admin_override')
        ->and($notification->raw_payload['unexpected_admin_override'])->toBe('should stay in raw audit only')
        ->and(file_get_contents(app_path('Http/Controllers/Payment/MidtransNotificationController.php')))
        ->not->toContain('handleNotification($request->all())');
});

it('filters admin payment monitoring by gateway status and renders registration status separately', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $program = miaSprint8Program();

    Registration::query()->create([
        'registration_code' => 'REG-MIA-S8-PAID',
        'program_id' => $program->id,
        'applicant_name' => 'Mia Sprint Eight Paid',
        'applicant_email' => 'mia.sprint8.paid@example.test',
        'applicant_phone' => '081234567890',
        'payment_method' => 'bank_transfer',
        'payment_status' => 'paid',
        'payment_amount' => 1400000,
        'final_amount' => 1250000,
        'midtrans_order_id' => 'ETC-REG-MIA-S8-PAID-1',
        'payment_gateway_id' => 'MID-MIA-S8-PAID',
        'status' => 'placement_test',
    ]);

    Registration::query()->create([
        'registration_code' => 'REG-MIA-S8-WAITING',
        'program_id' => $program->id,
        'applicant_name' => 'Mia Sprint Eight Waiting',
        'applicant_email' => 'mia.sprint8.waiting@example.test',
        'applicant_phone' => '081234567891',
        'payment_method' => 'virtual_account',
        'payment_status' => 'waiting_payment',
        'payment_amount' => 1400000,
        'final_amount' => 1400000,
        'midtrans_order_id' => 'ETC-REG-MIA-S8-WAITING-1',
        'payment_gateway_id' => 'MID-MIA-S8-WAITING',
        'status' => 'pending_payment',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.payment.index', [
            'payment_status' => 'paid',
            'sort' => 'final_amount',
            'direction' => 'asc',
        ]))
        ->assertOk()
        ->assertSee('Status Gateway')
        ->assertSee('Status Pendaftaran')
        ->assertSee('Mia Sprint Eight Paid')
        ->assertSee('Placement Test')
        ->assertDontSee('Mia Sprint Eight Waiting');
});

it('locks Filament upload MIME and size limits for Mia managed resources', function () {
    $contentForm = file_get_contents(app_path('Filament/Resources/Contents/Schemas/ContentForm.php'));
    $programForm = file_get_contents(app_path('Filament/Resources/Programs/Schemas/ProgramForm.php'));
    $reelForm = file_get_contents(app_path('Filament/Resources/Reels/Schemas/ReelForm.php'));
    $roomForm = file_get_contents(app_path('Filament/Resources/Rooms/Schemas/RoomForm.php'));
    $reportCardForm = file_get_contents(app_path('Filament/Resources/ReportCards/Schemas/ReportCardForm.php'));
    $knowledgeForm = file_get_contents(app_path('Filament/Resources/RagKnowledgeSources/Schemas/RagKnowledgeSourceForm.php'));

    expect($contentForm)
        ->toContain("FileUpload::make('image')")
        ->toContain('->maxSize(4096)')
        ->toContain("FileUpload::make('images')")
        ->toContain('->maxFiles(8)')
        ->and($programForm)->toContain("FileUpload::make('thumbnail')")->toContain('->maxSize(4096)')
        ->and($roomForm)->toContain("FileUpload::make('image')")->toContain('->maxSize(4096)')
        ->and($reelForm)->toContain("FileUpload::make('video_path')")->toContain('video/mp4')->toContain('->maxSize(51200)')
        ->and($reelForm)->toContain("FileUpload::make('thumbnail_path')")->toContain('->maxSize(4096)')
        ->and($reportCardForm)->toContain("FileUpload::make('pdf_path')")->toContain('application/pdf')->toContain('->maxSize(10240)')
        ->and($knowledgeForm)->toContain("FileUpload::make('source_file')")->toContain('application/pdf')->toContain('text/plain')->toContain('->maxSize(10240)');
});

it('treats Cloudinary delete as a safe no-op when credentials are unavailable', function () {
    Storage::fake('public');
    Config::set('cloudinary.cloud_name', null);
    Config::set('cloudinary.api_key', null);
    Config::set('cloudinary.api_secret', null);
    Config::set('cloudinary.url', null);

    app(MediaStorageService::class)->delete('cloudinary://raw/rag/source.pdf?v=123');

    Storage::disk('public')->assertMissing('cloudinary://raw/rag/source.pdf?v=123');
    expect(true)->toBeTrue();
});

function miaSprint8Program(): Program
{
    $suffix = (string) Str::uuid();

    return Program::query()->create([
        'name' => 'Mia Sprint 8 Program',
        'slug' => 'mia-sprint-8-program-'.$suffix,
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
}

function miaSprint8PaymentRegistration(): Registration
{
    $program = miaSprint8Program();

    return Registration::query()->create([
        'registration_code' => 'REG-MIA-S8-WEBHOOK',
        'program_id' => $program->id,
        'applicant_name' => 'Mia Sprint Eight Webhook',
        'applicant_email' => 'mia.sprint8.webhook@example.test',
        'applicant_phone' => '081234567890',
        'payment_method' => 'bank_transfer',
        'payment_amount' => 1400000,
        'final_amount' => 1250000,
        'midtrans_order_id' => 'ETC-REG-MIA-S8-WEBHOOK-1',
        'payment_gateway_id' => 'ETC-REG-MIA-S8-WEBHOOK-1',
        'payment_status' => 'waiting_payment',
        'status' => 'pending_payment',
    ]);
}

function miaSprint8MidtransPayload(Registration $registration, string $status, int $grossAmount): array
{
    $payload = [
        'order_id' => $registration->midtrans_order_id,
        'transaction_status' => $status,
        'transaction_id' => 'MID-MIA-S8-'.$registration->id,
        'payment_type' => 'bank_transfer',
        'fraud_status' => 'accept',
        'status_code' => '200',
        'gross_amount' => (string) $grossAmount,
    ];

    $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));

    return $payload;
}

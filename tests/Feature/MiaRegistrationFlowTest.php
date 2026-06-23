<?php

use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use App\Services\MidtransPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config(['midtrans.server_key' => null]);
});

test('documented mia sprint two route names are registered', function () {
    collect([
        'registrations.start',
        'registrations.create',
        'registrations.store',
        'registrations.payment.show',
        'registrations.confirmation.show',
        'registrations.receipt.download',
    ])->each(fn (string $routeName) => expect(Route::has($routeName))->toBeTrue($routeName));

    expect(Route::has('registrations.payment.proof.store'))->toBeFalse()
        ->and(Route::has('registrations.payment.confirm'))->toBeFalse();
});

test('mia registration form stores student profile and redirects to payment', function () {
    $this->withoutVite();

    $program = Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english-mia',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 850000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $this->post(route('registrations.store'), registrationPayload($program))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $registration = Registration::query()->firstOrFail();
    $student = User::query()->where('email', 'mia.student@example.test')->firstOrFail();

    expect($registration->registration_code)->toStartWith('REG-')
        ->and($registration->status)->toBe('pending_payment')
        ->and($registration->user_id)->toBe($student->id)
        ->and($student->full_name)->toBe('Mia Student')
        ->and($student->address)->toBe('Jl. Padang No. 1');
});

test('mia midtrans payment page and receipt work without legacy proof upload', function () {
    $program = Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english-payment-mia',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 850000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $this->post(route('registrations.store'), registrationPayload($program));

    $registration = Registration::query()->firstOrFail();

    $this->get(route('registrations.payment.show', $registration))
        ->assertOk()
        ->assertSee('Midtrans Checkout')
        ->assertSee('Lanjutkan ke Midtrans')
        ->assertDontSee('Upload Bukti')
        ->assertDontSee('Konfirmasi Legacy');

    $this->get(route('registrations.receipt.download', $registration))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
        ->assertSee($registration->registration_code);
});

test('mia midtrans snap payload redirects finished payments to confirmation page', function () {
    config(['app.url' => 'https://etc.test']);

    $program = Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english-midtrans-finish-mia',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 850000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $registration = Registration::query()->create([
        'registration_code' => 'REG-MIA-MIDTRANS-FINISH',
        'program_id' => $program->id,
        'applicant_name' => 'Mia Student',
        'applicant_email' => 'mia.student@example.test',
        'applicant_phone' => '081234567890',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
        'payment_amount' => 1050000,
        'final_amount' => 1050000,
        'payment_status' => 'waiting_payment',
        'status' => 'pending_payment',
    ]);

    $service = app(MidtransPaymentService::class);
    $method = new \ReflectionMethod($service, 'buildSnapPayload');
    $method->setAccessible(true);

    $payload = $method->invoke($service, $registration->load('program'), 'ETC-REG-MIA-MIDTRANS-FINISH-1', 1050000);

    expect($payload['callbacks']['finish'])
        ->toBe(route('registrations.confirmation.show', ['registration' => $registration], true));
});

test('mia demo midtrans redirect uses confirmation page when credentials are missing', function () {
    config([
        'app.url' => 'https://etc.test',
        'midtrans.server_key' => null,
    ]);

    $program = Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english-midtrans-demo-mia',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 850000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $registration = Registration::query()->create([
        'registration_code' => 'REG-MIA-MIDTRANS-DEMO',
        'program_id' => $program->id,
        'applicant_name' => 'Mia Student',
        'applicant_email' => 'mia.student@example.test',
        'applicant_phone' => '081234567890',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
        'payment_amount' => 1050000,
        'final_amount' => 1050000,
        'payment_status' => 'waiting_payment',
        'status' => 'pending_payment',
    ]);

    $updated = app(MidtransPaymentService::class)->createTransaction($registration);

    expect($updated->midtrans_redirect_url)
        ->toBe(route('registrations.confirmation.show', ['registration' => $updated], true));
});

function registrationPayload(Program $program): array
{
    return [
        'program_id' => $program->id,
        'applying_for' => 'smp_teen',
        'full_name' => 'Mia Student',
        'email' => 'mia.student@example.test',
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
        'address' => 'Jl. Padang No. 1',
        'rt_rw' => '001/002',
        'postal_code' => '25111',
        'village' => 'Ulak Karang',
        'sub_district' => 'Padang Utara',
        'district' => 'Padang',
        'province' => 'Sumatera Barat',
        'living_with' => 'Orang Tua',
        'transportation' => 'Kendaraan Pribadi',
        'mother_name' => 'Ibu Mia',
        'father_name' => 'Ayah Mia',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
    ];
}

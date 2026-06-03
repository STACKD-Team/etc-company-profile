<?php

use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('documented mia sprint two route names are registered', function () {
    collect([
        'registrations.start',
        'registrations.create',
        'registrations.store',
        'registrations.payment.show',
        'registrations.payment.proof.store',
        'registrations.payment.confirm',
        'registrations.confirmation.show',
        'registrations.receipt.download',
    ])->each(fn (string $routeName) => expect(Route::has($routeName))->toBeTrue($routeName));
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

test('mia payment proof upload confirmation and receipt work', function () {
    Storage::fake('public');

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

    $this->post(route('registrations.payment.proof.store', $registration), [
        'payment_method' => 'bank_transfer',
        'payment_proof' => UploadedFile::fake()->image('proof.jpg'),
    ])->assertSessionHasNoErrors();

    $registration->refresh();

    expect($registration->payment_method)->toBe('bank_transfer')
        ->and($registration->payment_proof)->not->toBeNull();

    Storage::disk('public')->assertExists($registration->payment_proof);

    $this->post(route('registrations.payment.confirm', $registration), [
        'payment_method' => 'bank_transfer',
        'payment_confirmed' => '1',
    ])->assertSessionHasNoErrors()
        ->assertRedirect();

    $registration->refresh();

    expect($registration->status)->toBe('pending_payment')
        ->and($registration->notes)->toContain('payment_confirmation');

    $this->get(route('registrations.receipt.download', $registration))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
        ->assertSee($registration->registration_code);
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

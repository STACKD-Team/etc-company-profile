<?php

use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('documented mia sprint three route names are registered', function () {
    collect([
        'admin.registrations.index',
        'admin.registrations.show',
        'admin.registrations.edit',
        'admin.registrations.update',
        'admin.payments.index',
        'admin.payments.show',
        'admin.payments.verify',
        'admin.payments.reject',
    ])->each(fn (string $routeName) => expect(Route::has($routeName))->toBeTrue($routeName));
});

test('mia admin intake pages require admin role', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->get(route('admin.registrations.index'))->assertRedirect('/login');

    $this->actingAs($student)
        ->get(route('admin.registrations.index'))
        ->assertForbidden();
});

test('admin can list show edit and update mia registration data', function () {
    $this->withoutVite();

    $admin = User::factory()->create(['role' => 'admin']);
    $program = createMiaProgram('General English', 'general-english-admin-mia');
    $registration = createMiaRegistration($program);

    $this->actingAs($admin)
        ->get(route('admin.registrations.index'))
        ->assertOk()
        ->assertSee('Mia Applicant')
        ->assertSee($registration->registration_code);

    $this->actingAs($admin)
        ->get(route('admin.registrations.show', $registration))
        ->assertOk()
        ->assertSee('Mia Applicant')
        ->assertSee('General English');

    $this->actingAs($admin)
        ->get(route('admin.registrations.edit', $registration))
        ->assertOk()
        ->assertSee('Edit Pendaftaran');

    $this->actingAs($admin)
        ->put(route('admin.registrations.update', $registration), [
            'program_id' => $program->id,
            'applicant_name' => 'Mia Updated Applicant',
            'applicant_email' => 'mia.updated@example.test',
            'applicant_phone' => '081299988877',
            'preferred_days' => 'tue_thu',
            'preferred_time' => '11.00-12.30',
            'payment_method' => 'bank_transfer',
            'payment_amount' => 1400000,
            'status' => 'pending_payment',
            'notes' => 'Admin correction',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.registrations.show', $registration));

    $registration->refresh();

    expect($registration->applicant_name)->toBe('Mia Updated Applicant')
        ->and($registration->preferred_days)->toBe('tue_thu')
        ->and((float) $registration->payment_amount)->toBe(1400000.0);
});

test('admin can view verify and reject mia registration payments', function () {
    $this->withoutVite();

    $admin = User::factory()->create(['role' => 'admin']);
    $program = createMiaProgram('Payment English', 'payment-english-admin-mia');
    $registration = createMiaRegistration($program, [
        'payment_method' => 'bank_transfer',
        'payment_proof' => 'registrations/payment-proofs/proof.jpg',
        'notes' => '{"payment_confirmation":true}',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.payments.index'))
        ->assertOk()
        ->assertSee('Mia Applicant')
        ->assertSee('Transfer Bank');

    $this->actingAs($admin)
        ->get(route('admin.payments.show', ['payment' => $registration]))
        ->assertOk()
        ->assertSee('Detail Pembayaran')
        ->assertSee($registration->registration_code);

    $this->actingAs($admin)
        ->post(route('admin.payments.verify', ['payment' => $registration]), [
            'payment_amount' => 1500000,
            'payment_method' => 'bank_transfer',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.payments.show', ['payment' => $registration]));

    $registration->refresh();

    expect($registration->status)->toBe('paid')
        ->and($registration->paid_at)->not->toBeNull()
        ->and($registration->payment_proof)->toBe('registrations/payment-proofs/proof.jpg')
        ->and((float) $registration->payment_amount)->toBe(1500000.0);

    $this->actingAs($admin)
        ->post(route('admin.payments.reject', ['payment' => $registration]), [
            'notes' => 'Bukti pembayaran tidak valid.',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.payments.show', ['payment' => $registration]));

    $registration->refresh();

    expect($registration->status)->toBe('rejected')
        ->and($registration->notes)->toBe('Bukti pembayaran tidak valid.')
        ->and($registration->payment_proof)->toBe('registrations/payment-proofs/proof.jpg');
});

function createMiaProgram(string $name, string $slug): Program
{
    return Program::query()->create([
        'name' => $name,
        'slug' => $slug,
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
}

function createMiaRegistration(Program $program, array $overrides = []): Registration
{
    return Registration::query()->create($overrides + [
        'registration_code' => 'REG-20260604-MIA',
        'program_id' => $program->id,
        'applicant_name' => 'Mia Applicant',
        'applicant_email' => 'mia.applicant@example.test',
        'applicant_phone' => '081234567890',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
        'payment_amount' => 1400000,
        'status' => 'pending_payment',
    ]);
}

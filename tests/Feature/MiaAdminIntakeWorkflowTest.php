<?php

use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('documented mia sprint three route names are registered', function () {
    collect([
        'admin.registration.index',
        'admin.registration.show',
        'admin.registration.edit',
        'admin.registration.update',
        'admin.payment.index',
        'admin.payment.show',
    ])->each(fn (string $routeName) => expect(Route::has($routeName))->toBeTrue($routeName));

    expect(Route::has('admin.payment.verify'))->toBeFalse()
        ->and(Route::has('admin.payment.reject'))->toBeFalse();
});

test('mia admin intake pages require admin role', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->get(route('admin.registration.index'))->assertRedirect('/login');

    $this->actingAs($student)
        ->get(route('admin.registration.index'))
        ->assertForbidden();
});

test('admin can list show edit and update mia registration data', function () {
    $this->withoutVite();

    $admin = User::factory()->create(['role' => 'admin']);
    $program = createMiaProgram('General English', 'general-english-admin-mia');
    $registration = createMiaRegistration($program);

    $this->actingAs($admin)
        ->get(route('admin.registration.index'))
        ->assertOk()
        ->assertSee('Mia Applicant')
        ->assertSee($registration->registration_code);

    $this->actingAs($admin)
        ->get(route('admin.registration.show', $registration))
        ->assertOk()
        ->assertSee('Mia Applicant')
        ->assertSee('General English');

    $this->actingAs($admin)
        ->get(route('admin.registration.edit', $registration))
        ->assertOk()
        ->assertSee('Edit Pendaftaran');

    $this->actingAs($admin)
        ->put(route('admin.registration.update', $registration), [
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
        ->assertRedirect(route('admin.registration.show', $registration));

    $registration->refresh();

    expect($registration->applicant_name)->toBe('Mia Updated Applicant')
        ->and($registration->preferred_days)->toBe('tue_thu')
        ->and((float) $registration->payment_amount)->toBe(1400000.0);
});

test('admin can view mia midtrans registration payments', function () {
    $this->withoutVite();

    $admin = User::factory()->create(['role' => 'admin']);
    $program = createMiaProgram('Payment English', 'payment-english-admin-mia');
    $registration = createMiaRegistration($program, [
        'payment_method' => 'bank_transfer',
        'midtrans_order_id' => 'ETC-REG-20260604-MIA-1',
        'payment_gateway_id' => 'ETC-REG-20260604-MIA-1',
        'payment_status' => 'waiting_payment',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.payment.index'))
        ->assertOk()
        ->assertSee('Mia Applicant')
        ->assertSee('Transfer Bank');

    $this->actingAs($admin)
        ->get(route('admin.payment.show', ['payment' => $registration]))
        ->assertOk()
        ->assertSee('Detail Pembayaran')
        ->assertSee($registration->registration_code)
        ->assertSee('Gateway Midtrans')
        ->assertDontSee('Fallback Legacy')
        ->assertDontSee('Verifikasi Legacy');
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

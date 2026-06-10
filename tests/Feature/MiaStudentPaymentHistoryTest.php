<?php

use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('documented mia sprint four student payment route names are registered', function () {
    collect([
        'student.payments.index',
        'student.payments.show',
    ])->each(fn (string $routeName) => expect(Route::has($routeName))->toBeTrue($routeName));
});

test('mia student payment pages require student role', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get(route('student.payments.index'))->assertRedirect('/login');

    $this->actingAs($admin)
        ->get(route('student.payments.index'))
        ->assertForbidden();
});

test('student can only see own payment history in mia sprint four index', function () {
    $this->withoutVite();

    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Mia Student']);
    $otherStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Other Student']);
    $program = createMiaSprintFourProgram();

    $ownPayment = createMiaSprintFourPayment($program, $student, [
        'registration_code' => 'REG-MIA-STUDENT-OWN',
        'applicant_name' => 'Mia Student',
    ]);

    createMiaSprintFourPayment($program, $otherStudent, [
        'registration_code' => 'REG-MIA-STUDENT-OTHER',
        'applicant_name' => 'Other Student',
    ]);

    $this->actingAs($student)
        ->get(route('student.payments.index'))
        ->assertOk()
        ->assertSee('Riwayat Pembayaran')
        ->assertSee('data-pagination', false)
        ->assertSee('data-pagination-summary', false)
        ->assertSee($ownPayment->registration_code)
        ->assertSee('Mia Student')
        ->assertDontSee('REG-MIA-STUDENT-OTHER')
        ->assertDontSee('Other Student');
});

test('student can view own payment detail and cannot view another student payment', function () {
    $this->withoutVite();

    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Mia Student']);
    $otherStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Other Student']);
    $program = createMiaSprintFourProgram('Detail English', 'detail-english-mia-sprint-four');

    $ownPayment = createMiaSprintFourPayment($program, $student, [
        'registration_code' => 'REG-MIA-STUDENT-DETAIL',
        'applicant_name' => 'Mia Student',
        'payment_amount' => 1500000,
        'payment_method' => 'bank_transfer',
        'payment_proof' => 'registrations/payment-proofs/mia-proof.jpg',
        'status' => 'paid',
        'paid_at' => now(),
    ]);
    $otherPayment = createMiaSprintFourPayment($program, $otherStudent, [
        'registration_code' => 'REG-MIA-STUDENT-FORBIDDEN',
        'applicant_name' => 'Other Student',
    ]);

    $this->actingAs($student)
        ->get(route('student.payments.show', ['payment' => $ownPayment]))
        ->assertOk()
        ->assertSee('Detail Pembayaran')
        ->assertSee('REG-MIA-STUDENT-DETAIL')
        ->assertSee('Transfer Bank')
        ->assertSee('Rp 1.500.000');

    $this->actingAs($student)
        ->get(route('student.payments.show', ['payment' => $otherPayment]))
        ->assertForbidden();
});

function createMiaSprintFourProgram(string $name = 'Payment History English', string $slug = 'payment-history-english-mia-sprint-four'): Program
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

function createMiaSprintFourPayment(Program $program, User $student, array $overrides = []): Registration
{
    return Registration::query()->create($overrides + [
        'registration_code' => 'REG-MIA-STUDENT-PAYMENT',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'applicant_name' => $student->full_name ?? $student->name,
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'preferred_days' => 'mon_wed',
        'preferred_time' => '09.00-10.30',
        'payment_amount' => 1400000,
        'payment_method' => 'qris',
        'status' => 'pending_payment',
    ]);
}

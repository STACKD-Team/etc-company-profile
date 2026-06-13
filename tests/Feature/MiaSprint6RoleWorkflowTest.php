<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders Mia sprint 6 admin report card pages from role-specific views', function () {
    $this->withoutVite();

    [$admin, $student, $instructor, $class, $enrollment, $reportCard] = miaSprintSixAcademicContext();

    $this->actingAs($admin)
        ->get(route('admin.report-card.index'))
        ->assertOk()
        ->assertViewIs('pages.admin.report-card.index')
        ->assertSee('Mia Sprint 6 Student')
        ->assertSee('Mia Sprint 6 Class');

    $this->actingAs($admin)
        ->get(route('admin.report-card.create'))
        ->assertOk()
        ->assertViewIs('pages.admin.report-card.form')
        ->assertSee('Buat Rapor');

    $this->actingAs($admin)
        ->get(route('admin.report-card.show', $reportCard))
        ->assertOk()
        ->assertViewIs('pages.admin.report-card.show')
        ->assertSee('Mia Sprint 6 Student')
        ->assertSee('Preview Template');

    $this->actingAs($admin)
        ->get(route('admin.report-card.edit', $reportCard))
        ->assertOk()
        ->assertViewIs('pages.admin.report-card.form')
        ->assertSee('Edit Rapor');

    expect($enrollment->user->is($student))->toBeTrue()
        ->and($class->instructor->is($instructor))->toBeTrue();
});

it('lets admin see the full operational scope for class student payment and report card workflows', function () {
    $this->withoutVite();

    [$admin, $student, $instructor, $class, $enrollment, $reportCard] = miaSprintSixAcademicContext();
    [$otherAdmin, $otherStudent, $otherInstructor, $otherClass, $otherEnrollment, $otherReportCard] = miaSprintSixAcademicContext('Other');

    $payment = miaSprintSixPayment($student, $class, ['registration_code' => 'REG-MIA-S6-OWN']);
    $otherPayment = miaSprintSixPayment($otherStudent, $otherClass, ['registration_code' => 'REG-MIA-S6-OTHER']);

    $this->actingAs($admin)
        ->get(route('admin.student.show', $student))
        ->assertOk()
        ->assertSee('Histori Kelas')
        ->assertSee('Mia Sprint 6 Class')
        ->assertSee('Mia Sprint 6 Instructor')
        ->assertSee(route('admin.report-card.show', $reportCard), false)
        ->assertDontSee('Other Sprint 6 Class');

    $this->actingAs($admin)
        ->get(route('admin.class.show', $class))
        ->assertOk()
        ->assertSee('Enrollment Kelas')
        ->assertSee('Mia Sprint 6 Student')
        ->assertSee(route('admin.report-card.show', $reportCard), false)
        ->assertDontSee('Other Sprint 6 Student');

    $this->actingAs($admin)
        ->get(route('admin.payment.index'))
        ->assertOk()
        ->assertSee('REG-MIA-S6-OWN')
        ->assertSee('REG-MIA-S6-OTHER');

    $this->actingAs($admin)
        ->get(route('admin.payment.show', ['payment' => $otherPayment]))
        ->assertOk()
        ->assertSee('REG-MIA-S6-OTHER')
        ->assertSee('Other Sprint 6 Student');

    $this->actingAs($admin)
        ->get(route('admin.report-card.index'))
        ->assertOk()
        ->assertSee('Mia Sprint 6 Student')
        ->assertSee('Other Sprint 6 Student');

    expect($otherAdmin->role)->toBe('admin')
        ->and($payment->user->is($student))->toBeTrue()
        ->and($otherReportCard->enrollment->user->is($otherStudent))->toBeTrue()
        ->and($otherEnrollment->courseClass->instructor->is($otherInstructor))->toBeTrue();
});

it('blocks non admin roles from Mia sprint 6 admin workflow routes', function () {
    [$admin, $student, $instructor, $class, $enrollment, $reportCard] = miaSprintSixAcademicContext();
    $payment = miaSprintSixPayment($student, $class);

    $routes = [
        route('admin.student.index'),
        route('admin.student.show', $student),
        route('admin.class.index'),
        route('admin.class.show', $class),
        route('admin.payment.index'),
        route('admin.payment.show', ['payment' => $payment]),
        route('admin.report-card.index'),
        route('admin.report-card.show', $reportCard),
    ];

    foreach ($routes as $url) {
        $this->get($url)->assertRedirect(route('auth.login'));
    }

    foreach ($routes as $url) {
        $this->actingAs($student)->get($url)->assertForbidden();
        $this->actingAs($instructor)->get($url)->assertForbidden();
    }

    $this->actingAs($admin)
        ->get(route('admin.report-card.show', $reportCard))
        ->assertOk();

    expect($enrollment->reportCard->is($reportCard))->toBeTrue();
});

it('keeps student owned payment and published report card visibility isolated', function () {
    [$admin, $student, $instructor, $class, $enrollment, $reportCard] = miaSprintSixAcademicContext();
    [$otherAdmin, $otherStudent, $otherInstructor, $otherClass, $otherEnrollment, $otherReportCard] = miaSprintSixAcademicContext('Other');

    $payment = miaSprintSixPayment($student, $class, ['registration_code' => 'REG-MIA-S6-STUDENT']);
    $otherPayment = miaSprintSixPayment($otherStudent, $otherClass, ['registration_code' => 'REG-MIA-S6-HIDDEN']);

    $this->actingAs($student)
        ->get(route('student.payments.index'))
        ->assertOk()
        ->assertSee('REG-MIA-S6-STUDENT')
        ->assertDontSee('REG-MIA-S6-HIDDEN');

    $this->actingAs($student)
        ->get(route('student.payments.show', ['payment' => $payment]))
        ->assertOk()
        ->assertSee('REG-MIA-S6-STUDENT');

    $this->actingAs($student)
        ->get(route('student.payments.show', ['payment' => $otherPayment]))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('student.report-cards.index'))
        ->assertOk()
        ->assertSee('Mia Sprint 6 Program')
        ->assertDontSee('Other Sprint 6 Program');

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $reportCard))
        ->assertOk()
        ->assertSee('Detail Rapor');

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $otherReportCard))
        ->assertForbidden();

    expect($admin->role)->toBe('admin')
        ->and($otherAdmin->role)->toBe('admin')
        ->and($instructor->role)->toBe('instructor')
        ->and($otherInstructor->role)->toBe('instructor');
});

function miaSprintSixAcademicContext(string $prefix = 'Mia'): array
{
    static $count = 0;
    $count++;

    $admin = User::factory()->create([
        'role' => 'admin',
        'name' => "{$prefix} Sprint 6 Admin {$count}",
        'is_active' => true,
    ]);
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'name' => "{$prefix} Sprint 6 Instructor {$count}",
        'full_name' => "{$prefix} Sprint 6 Instructor",
    ]);
    $student = User::factory()->create([
        'role' => 'student',
        'name' => "{$prefix} Sprint 6 Student {$count}",
        'full_name' => "{$prefix} Sprint 6 Student",
    ]);
    $program = Program::query()->create([
        'name' => "{$prefix} Sprint 6 Program",
        'slug' => strtolower($prefix)."-sprint-6-program-{$count}",
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => "{$prefix} Sprint 6 Class",
        'schedule_days' => 'Mon-Wed',
        'schedule_time' => '15.00-16.30',
        'status' => 'ongoing',
    ]);
    $enrollment = Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => '2026-06-13',
        'status' => 'active',
    ]);
    $reportCard = ReportCard::query()->create([
        'enrollment_id' => $enrollment->id,
        'instructor_id' => $instructor->id,
        'score_listening' => 18,
        'score_vocabulary' => 18,
        'score_structure' => 17,
        'score_reading' => 17,
        'score_writing' => 18,
        'total_score' => 88,
        'final_grade' => 'A',
        'comments' => "{$prefix} Sprint 6 report card comment.",
        'is_published' => true,
        'issued_at' => '2026-06-20',
    ]);

    return [$admin, $student, $instructor, $class, $enrollment, $reportCard];
}

function miaSprintSixPayment(User $student, CourseClass $class, array $overrides = []): Registration
{
    static $count = 0;
    $count++;

    return Registration::query()->create($overrides + [
        'registration_code' => "REG-MIA-S6-{$count}",
        'user_id' => $student->id,
        'program_id' => $class->program_id,
        'class_id' => $class->id,
        'applicant_name' => $student->full_name ?? $student->name,
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'payment_method' => 'virtual_account',
        'payment_gateway_id' => "MIDTRANS-MIA-S6-{$count}",
        'payment_status' => 'paid',
        'payment_amount' => 1400000,
        'original_amount' => 1400000,
        'discount_amount' => 0,
        'final_amount' => 1400000,
        'paid_at' => '2026-06-13 10:00:00',
        'status' => 'paid',
    ]);
}

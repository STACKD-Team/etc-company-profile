<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration programs page renders active programs from the database', function () {
    Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Program komunikasi bahasa Inggris.',
        'duration_meetings' => 20,
        'max_students' => 12,
        'price' => 1500000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    Program::query()->create([
        'name' => 'Inactive Program',
        'slug' => 'inactive-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 12,
        'max_students' => 12,
        'price' => 1000000,
        'registration_fee' => 100000,
        'is_active' => false,
    ]);

    $this->get('/registration/programs')
        ->assertOk()
        ->assertSee('General English')
        ->assertDontSee('Inactive Program');
});

test('public programs page renders active programs with discovery details', function () {
    Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english-public',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'description' => 'Program komunikasi bahasa Inggris.',
        'duration_meetings' => 20,
        'max_students' => 12,
        'price' => 1500000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    Program::query()->create([
        'name' => 'Inactive Program',
        'slug' => 'inactive-public-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 12,
        'max_students' => 12,
        'price' => 1000000,
        'registration_fee' => 100000,
        'is_active' => false,
    ]);

    $this->get('/programs')
        ->assertOk()
        ->assertSee('General English')
        ->assertSee('Program komunikasi bahasa Inggris.')
        ->assertSee('20 pertemuan')
        ->assertSee('Teen')
        ->assertSee('Regular')
        ->assertSee('Maks. 12 siswa')
        ->assertSee('Rp 1.500.000')
        ->assertSee('Biaya pendaftaran')
        ->assertSee('Rp 200.000')
        ->assertSee('Lihat Detail')
        ->assertDontSee('Inactive Program');
});

test('public programs page filters by category', function () {
    Program::query()->create([
        'name' => 'English Teen',
        'slug' => 'english-teen-public',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    Program::query()->create([
        'name' => 'Mandarin Starter',
        'slug' => 'mandarin-starter-public',
        'category' => 'mandarin',
        'type' => 'private',
        'target_age' => 'adult',
        'duration_meetings' => 12,
        'max_students' => 6,
        'price' => 1800000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $this->get('/programs?category=english')
        ->assertOk()
        ->assertSee('English Teen')
        ->assertSee('English')
        ->assertDontSee('Mandarin Starter');
});

test('student dashboard is only available for student users', function () {
    $student = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Mecca Student',
        'is_active' => true,
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $this->get('/student/dashboard')->assertRedirect('/login');

    $this->actingAs($admin)
        ->get('/student/dashboard')
        ->assertForbidden();

    $this->actingAs($student)
        ->get('/student/dashboard')
        ->assertOk()
        ->assertSee('Mecca Student');
});

test('legacy mecca urls are not registered', function () {
    collect([
        '/pilih-program',
        '/pilih_program',
        '/dashboard-siswa',
        '/dashboard_siswa',
        '/dashboard',
    ])->each(fn (string $uri) => $this->get($uri)->assertNotFound());
});

test('documented mecca route names are registered', function () {
    collect([
        'public.programs.index',
        'registrations.programs.index',
        'student.dashboard',
        'student.profile.show',
        'student.profile.update',
        'student.classes.index',
        'student.classes.show',
        'student.learning-history.index',
        'student.report-cards.index',
        'student.report-cards.show',
        'student.report-cards.download',
        'student.help.index',
        'admin.students.index',
        'admin.students.show',
        'admin.instructors.index',
        'admin.instructors.show',
        'admin.programs.index',
        'admin.programs.create',
        'admin.programs.store',
        'admin.programs.edit',
        'admin.programs.update',
        'admin.classes.index',
        'admin.classes.create',
        'admin.classes.store',
        'admin.classes.edit',
        'admin.classes.update',
        'admin.enrollments.index',
        'admin.enrollments.store',
    ])->each(fn (string $routeName) => expect(\Illuminate\Support\Facades\Route::has($routeName))->toBeTrue());
});

test('student mecca pages render for authenticated student', function () {
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Mecca Student']);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'ETC Instructor']);
    $program = Program::query()->create([
        'name' => 'General English',
        'slug' => 'general-english-student',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 20,
        'max_students' => 12,
        'price' => 1500000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Intermediate B1',
        'status' => 'ongoing',
    ]);
    $enrollment = Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => now()->toDateString(),
        'status' => 'active',
    ]);
    $reportCard = ReportCard::query()->create([
        'enrollment_id' => $enrollment->id,
        'total_score' => 88,
        'final_grade' => 'A',
        'is_published' => true,
        'issued_at' => now()->toDateString(),
    ]);

    $this->actingAs($student)->get('/student/profile')->assertOk();
    $this->actingAs($student)->get('/student/classes')->assertOk()->assertSee('Intermediate B1');
    $this->actingAs($student)->get("/student/classes/{$class->id}")->assertOk()->assertSee('Intermediate B1');
    $this->actingAs($student)->get('/student/learning-history')->assertOk();
    $this->actingAs($student)->get('/student/report-cards')->assertOk();
    $this->actingAs($student)->get("/student/report-cards/{$reportCard->id}")->assertOk()->assertSee('Detail Rapor');
    $this->actingAs($student)->get('/student/help')->assertOk();
});

test('admin academic mecca pages render for authenticated admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get('/admin/students')->assertOk();
    $this->actingAs($admin)->get('/admin/instructors')->assertOk();
    $this->actingAs($admin)->get('/admin/programs')->assertOk();
    $this->actingAs($admin)->get('/admin/programs/create')->assertOk();
    $this->actingAs($admin)->get('/admin/classes')->assertOk();
    $this->actingAs($admin)->get('/admin/classes/create')->assertOk();
    $this->actingAs($admin)->get('/admin/enrollments')->assertOk();
});

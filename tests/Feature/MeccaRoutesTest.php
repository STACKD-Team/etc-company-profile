<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createMeccaSprint4Program(array $attributes = []): Program
{
    static $count = 0;
    $count++;

    return Program::query()->create(array_merge([
        'name' => 'Sprint 4 Program '.$count,
        'slug' => 'sprint-4-program-'.$count,
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ], $attributes));
}

function createMeccaSprint4Class(array $attributes = []): CourseClass
{
    static $count = 0;
    $count++;

    $program = $attributes['program_id'] ?? createMeccaSprint4Program()->id;
    $instructor = $attributes['instructor_id'] ?? User::factory()->create(['role' => 'instructor'])->id;

    return CourseClass::query()->create(array_merge([
        'program_id' => $program,
        'instructor_id' => $instructor,
        'name' => 'Sprint 4 Class '.$count,
        'schedule_days' => 'Mon-Wed',
        'schedule_time' => '15.00-16.30',
        'room' => 'Hard Rock',
        'status' => 'ongoing',
    ], $attributes));
}

function createMeccaSprint4Enrollment(User $student, CourseClass $class, array $attributes = []): Enrollment
{
    return Enrollment::query()->create(array_merge([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => '2026-06-12',
        'status' => 'active',
    ], $attributes));
}

function createMeccaSprint4ReportCard(Enrollment $enrollment, array $attributes = []): ReportCard
{
    return ReportCard::query()->create(array_merge([
        'enrollment_id' => $enrollment->id,
        'score_listening' => 18,
        'score_vocabulary' => 17,
        'score_structure' => 16,
        'score_reading' => 18,
        'score_writing' => 17,
        'total_score' => 86,
        'final_grade' => 'A',
        'comments' => 'Progress belajar sangat baik.',
        'is_published' => true,
        'issued_at' => '2026-06-20',
    ], $attributes));
}

test('registration programs page renders active programs and links the selected program to registration form', function () {
    $activeProgram = Program::query()->create([
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
        ->assertSee('value="'.$activeProgram->id.'"', false)
        ->assertSee('checked', false)
        ->assertSee('/registration/form/'.$activeProgram->id, false)
        ->assertDontSee(route('public.contact.index', ['program' => $activeProgram->id]), false)
        ->assertDontSee('Inactive Program');
});

test('registration programs page can preselect a program from query string', function () {
    $english = Program::query()->create([
        'name' => 'English Teen',
        'slug' => 'english-teen-picker',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $mandarin = Program::query()->create([
        'name' => 'Mandarin Starter',
        'slug' => 'mandarin-starter-picker',
        'category' => 'mandarin',
        'type' => 'private',
        'target_age' => 'adult',
        'duration_meetings' => 12,
        'max_students' => 6,
        'price' => 1800000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $this->get('/registration/programs?program='.$mandarin->id)
        ->assertOk()
        ->assertSee('value="'.$english->id.'"', false)
        ->assertSee('value="'.$mandarin->id.'"', false)
        ->assertSee('<strong id="summary-name" class="font-heading text-lg">Mandarin Starter</strong>', false)
        ->assertSee('/registration/form/'.$mandarin->id, false)
        ->assertDontSee(route('public.contact.index', ['program' => $mandarin->id]), false);
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

test('student sprint four routes require student access', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student']);
    $class = createMeccaSprint4Class();
    $enrollment = createMeccaSprint4Enrollment($student, $class);
    $reportCard = createMeccaSprint4ReportCard($enrollment, ['pdf_path' => 'report-cards/student-access.pdf']);

    $routes = [
        ['get', route('student.dashboard', [], false), []],
        ['get', route('student.profile.show', [], false), []],
        ['put', route('student.profile.update', [], false), ['full_name' => 'Blocked Update']],
        ['get', route('student.classes.index', [], false), []],
        ['get', route('student.classes.show', $class, false), []],
        ['get', route('student.learning-history.index', [], false), []],
        ['get', route('student.report-cards.index', [], false), []],
        ['get', route('student.report-cards.show', $reportCard, false), []],
        ['get', route('student.report-cards.download', $reportCard, false), []],
        ['get', route('student.help.index', [], false), []],
    ];

    foreach ($routes as [$method, $uri, $payload]) {
        $this->{$method}($uri, $payload)->assertRedirect('/login');
    }

    foreach ($routes as [$method, $uri, $payload]) {
        $this->actingAs($admin)
            ->{$method}($uri, $payload)
            ->assertForbidden();
    }
});

test('student dashboard shows owned learning data and working mecca links', function () {
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Mecca Student']);
    $otherStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Other Student']);

    $activeClass = createMeccaSprint4Class([
        'program_id' => createMeccaSprint4Program(['name' => 'Owned Active Program', 'slug' => 'owned-active-program'])->id,
        'name' => 'Owned Active Class',
    ]);
    $activeEnrollment = createMeccaSprint4Enrollment($student, $activeClass);
    $downloadableReport = createMeccaSprint4ReportCard($activeEnrollment, [
        'issued_at' => '2026-06-22',
        'pdf_path' => 'report-cards/owned-active.pdf',
    ]);

    $historyClass = createMeccaSprint4Class([
        'program_id' => createMeccaSprint4Program(['name' => 'Owned History Program', 'slug' => 'owned-history-program'])->id,
        'name' => 'Owned History Class',
    ]);
    $historyEnrollment = createMeccaSprint4Enrollment($student, $historyClass, ['status' => 'completed']);
    $viewOnlyReport = createMeccaSprint4ReportCard($historyEnrollment, [
        'issued_at' => '2026-06-21',
        'pdf_path' => null,
    ]);

    $otherClass = createMeccaSprint4Class([
        'program_id' => createMeccaSprint4Program(['name' => 'Other Student Program', 'slug' => 'other-student-program'])->id,
        'name' => 'Other Student Class',
    ]);
    createMeccaSprint4Enrollment($otherStudent, $otherClass);

    $this->actingAs($student)
        ->get(route('student.dashboard', [], false))
        ->assertOk()
        ->assertSee('Mecca Student')
        ->assertSee('Owned Active Class')
        ->assertDontSee('Other Student Class')
        ->assertSee(route('student.help.index', [], false), false)
        ->assertSee(route('student.classes.show', $activeClass, false), false)
        ->assertSee(route('student.report-cards.index', [], false), false)
        ->assertSee(route('student.report-cards.download', $downloadableReport, false), false)
        ->assertSee(route('student.report-cards.show', $viewOnlyReport, false), false);
});

test('student profile update uses validated allowed fields', function () {
    $student = User::factory()->create([
        'role' => 'student',
        'full_name' => 'Old Name',
        'mobile_phone' => '0812000000',
    ]);

    $this->actingAs($student)
        ->put(route('student.profile.update', [], false), [
            'full_name' => 'Updated Mecca Student',
            'mobile_phone' => '0812999888',
            'sex' => 'F',
            'province' => 'Sumatera Barat',
            'address' => 'Jl. S. Parman Padang',
        ])
        ->assertRedirect(route('student.profile.show', [], false));

    $this->assertDatabaseHas('users', [
        'id' => $student->id,
        'full_name' => 'Updated Mecca Student',
        'mobile_phone' => '0812999888',
        'sex' => 'F',
        'province' => 'Sumatera Barat',
    ]);

    $this->actingAs($student)
        ->from(route('student.profile.show', [], false))
        ->put(route('student.profile.update', [], false), [
            'full_name' => 'Invalid Sex Student',
            'sex' => 'X',
        ])
        ->assertRedirect(route('student.profile.show', [], false))
        ->assertSessionHasErrors('sex');
});

test('student classes only expose enrollments owned by the authenticated student', function () {
    $student = User::factory()->create(['role' => 'student']);
    $otherStudent = User::factory()->create(['role' => 'student']);
    $ownedClass = createMeccaSprint4Class(['name' => 'Owned Student Class']);
    $otherClass = createMeccaSprint4Class(['name' => 'Other Student Private Class']);

    createMeccaSprint4Enrollment($student, $ownedClass);
    createMeccaSprint4Enrollment($otherStudent, $otherClass);

    $this->actingAs($student)
        ->get(route('student.classes.index', [], false))
        ->assertOk()
        ->assertSee('Owned Student Class')
        ->assertDontSee('Other Student Private Class');

    $this->actingAs($student)
        ->get(route('student.classes.show', $ownedClass, false))
        ->assertOk()
        ->assertSee('Owned Student Class');

    $this->actingAs($student)
        ->get(route('student.classes.show', $otherClass, false))
        ->assertNotFound();
});

test('student learning history only shows completed and dropped enrollments', function () {
    $student = User::factory()->create(['role' => 'student']);
    $activeClass = createMeccaSprint4Class(['name' => 'Still Active Class']);
    $completedClass = createMeccaSprint4Class(['name' => 'Completed History Class']);
    $droppedClass = createMeccaSprint4Class(['name' => 'Dropped History Class']);

    createMeccaSprint4Enrollment($student, $activeClass, ['status' => 'active']);
    createMeccaSprint4Enrollment($student, $completedClass, [
        'status' => 'completed',
        'completed_at' => '2026-07-12',
    ]);
    createMeccaSprint4Enrollment($student, $droppedClass, [
        'status' => 'dropped',
        'completed_at' => '2026-07-13',
    ]);

    $this->actingAs($student)
        ->get(route('student.learning-history.index', [], false))
        ->assertOk()
        ->assertSee('Completed History Class')
        ->assertSee('Dropped History Class')
        ->assertDontSee('Still Active Class');
});

test('student report cards only expose published reports owned by the authenticated student', function () {
    $student = User::factory()->create(['role' => 'student']);
    $otherStudent = User::factory()->create(['role' => 'student']);

    $ownedPublishedClass = createMeccaSprint4Class([
        'program_id' => createMeccaSprint4Program(['name' => 'Owned Published Program', 'slug' => 'owned-published-program'])->id,
    ]);
    $ownedPublishedEnrollment = createMeccaSprint4Enrollment($student, $ownedPublishedClass);
    $ownedPublishedReport = createMeccaSprint4ReportCard($ownedPublishedEnrollment, ['comments' => 'Owned published comment.']);

    $ownedUnpublishedClass = createMeccaSprint4Class([
        'program_id' => createMeccaSprint4Program(['name' => 'Owned Draft Program', 'slug' => 'owned-draft-program'])->id,
    ]);
    $ownedUnpublishedEnrollment = createMeccaSprint4Enrollment($student, $ownedUnpublishedClass);
    $ownedUnpublishedReport = createMeccaSprint4ReportCard($ownedUnpublishedEnrollment, ['is_published' => false]);

    $otherClass = createMeccaSprint4Class([
        'program_id' => createMeccaSprint4Program(['name' => 'Other Published Program', 'slug' => 'other-published-program'])->id,
    ]);
    $otherEnrollment = createMeccaSprint4Enrollment($otherStudent, $otherClass);
    $otherReport = createMeccaSprint4ReportCard($otherEnrollment);

    $this->actingAs($student)
        ->get(route('student.report-cards.index', [], false))
        ->assertOk()
        ->assertSee('Owned Published Program')
        ->assertDontSee('Owned Draft Program')
        ->assertDontSee('Other Published Program');

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $ownedPublishedReport, false))
        ->assertOk()
        ->assertSee('Owned published comment.');

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $ownedUnpublishedReport, false))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $otherReport, false))
        ->assertForbidden();
});

test('student report card download requires a published owned file', function () {
    Storage::fake('local');

    $student = User::factory()->create(['role' => 'student']);
    $otherStudent = User::factory()->create(['role' => 'student']);
    Storage::put('report-cards/owned.pdf', 'owned report');
    Storage::put('report-cards/unpublished.pdf', 'unpublished report');
    Storage::put('report-cards/other.pdf', 'other report');

    $ownedEnrollment = createMeccaSprint4Enrollment($student, createMeccaSprint4Class());
    $ownedReport = createMeccaSprint4ReportCard($ownedEnrollment, ['pdf_path' => 'report-cards/owned.pdf']);

    $unpublishedEnrollment = createMeccaSprint4Enrollment($student, createMeccaSprint4Class());
    $unpublishedReport = createMeccaSprint4ReportCard($unpublishedEnrollment, [
        'is_published' => false,
        'pdf_path' => 'report-cards/unpublished.pdf',
    ]);

    $otherEnrollment = createMeccaSprint4Enrollment($otherStudent, createMeccaSprint4Class());
    $otherReport = createMeccaSprint4ReportCard($otherEnrollment, ['pdf_path' => 'report-cards/other.pdf']);

    $missingFileEnrollment = createMeccaSprint4Enrollment($student, createMeccaSprint4Class());
    $missingFileReport = createMeccaSprint4ReportCard($missingFileEnrollment, ['pdf_path' => 'report-cards/missing.pdf']);

    $this->actingAs($student)
        ->get(route('student.report-cards.download', $ownedReport, false))
        ->assertOk();

    $this->actingAs($student)
        ->get(route('student.report-cards.download', $unpublishedReport, false))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('student.report-cards.download', $otherReport, false))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('student.report-cards.download', $missingFileReport, false))
        ->assertNotFound();
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

test('admin academic routes require admin access', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->get('/admin/programs')->assertRedirect('/login');

    $this->actingAs($student)
        ->get('/admin/programs')
        ->assertForbidden();
});

test('admin student and instructor pages only expose matching roles', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create([
        'role' => 'student',
        'name' => 'Student Account',
        'full_name' => 'Mecca Student',
    ]);
    $instructor = User::factory()->create([
        'role' => 'instructor',
        'name' => 'Instructor Account',
        'full_name' => 'ETC Instructor',
    ]);

    $this->actingAs($admin)
        ->get('/admin/students')
        ->assertOk()
        ->assertSee('Mecca Student')
        ->assertDontSee('ETC Instructor');

    $this->actingAs($admin)
        ->get("/admin/students/{$student->id}")
        ->assertOk()
        ->assertSee('Mecca Student');

    $this->actingAs($admin)
        ->get("/admin/students/{$instructor->id}")
        ->assertNotFound();

    $this->actingAs($admin)
        ->get('/admin/instructors')
        ->assertOk()
        ->assertSee('ETC Instructor')
        ->assertDontSee('Mecca Student');

    $this->actingAs($admin)
        ->get("/admin/instructors/{$instructor->id}")
        ->assertOk()
        ->assertSee('ETC Instructor');

    $this->actingAs($admin)
        ->get("/admin/instructors/{$student->id}")
        ->assertNotFound();
});

test('admin can create and update programs', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.programs.store'), [
            'name' => 'Academic English',
            'slug' => 'academic-english',
            'category' => 'english',
            'type' => 'regular',
            'target_age' => 'teen',
            'description' => 'Program akademik untuk remaja.',
            'duration_meetings' => 16,
            'max_students' => 10,
            'price' => 1250000,
            'registration_fee' => 200000,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.programs.index'));

    $program = Program::query()->where('slug', 'academic-english')->firstOrFail();

    expect($program->is_active)->toBeTrue();

    $this->actingAs($admin)
        ->put(route('admin.programs.update', $program), [
            'name' => 'Academic English Updated',
            'slug' => 'academic-english',
            'category' => 'test_prep',
            'type' => 'private',
            'target_age' => 'university',
            'description' => 'Program akademik intensif.',
            'duration_meetings' => 18,
            'max_students' => 6,
            'price' => 1750000,
            'registration_fee' => 250000,
        ])
        ->assertRedirect(route('admin.programs.index'));

    $program->refresh();

    expect($program->name)->toBe('Academic English Updated')
        ->and($program->category)->toBe('test_prep')
        ->and($program->type)->toBe('private')
        ->and($program->is_active)->toBeFalse();
});

test('admin can create and update classes with instructor role validation', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'ETC Instructor']);
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Mecca Student']);
    $program = Program::query()->create([
        'name' => 'General English Class Program',
        'slug' => 'general-english-class-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);

    $classPayload = [
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Teen B1',
        'schedule_days' => 'Mon-Wed',
        'schedule_time' => '15.00-16.30',
        'room' => 'Hard Rock',
        'start_date' => '2026-06-10',
        'end_date' => '2026-08-10',
        'status' => 'upcoming',
    ];

    $this->actingAs($admin)
        ->post(route('admin.classes.store'), $classPayload)
        ->assertRedirect(route('admin.classes.index'));

    $class = CourseClass::query()->where('name', 'Teen B1')->firstOrFail();

    expect($class->instructor_id)->toBe($instructor->id)
        ->and($class->schedule_days)->toBe('Mon-Wed')
        ->and($class->status)->toBe('upcoming');

    $this->actingAs($admin)
        ->put(route('admin.classes.update', $class), array_merge($classPayload, [
            'name' => 'Teen B2',
            'status' => 'ongoing',
        ]))
        ->assertRedirect(route('admin.classes.index'));

    expect($class->refresh()->name)->toBe('Teen B2')
        ->and($class->status)->toBe('ongoing');

    $this->actingAs($admin)
        ->post(route('admin.classes.store'), array_merge($classPayload, [
            'name' => 'Invalid Instructor Class',
            'instructor_id' => $student->id,
        ]))
        ->assertSessionHasErrors('instructor_id');
});

test('admin can assign student enrollments and duplicate assignments are rejected', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Mecca Student']);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'ETC Instructor']);
    $program = Program::query()->create([
        'name' => 'Enrollment Program',
        'slug' => 'enrollment-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Enrollment Class',
        'status' => 'ongoing',
    ]);

    $payload = [
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => '2026-06-12',
        'status' => 'active',
    ];

    $this->actingAs($admin)
        ->post(route('admin.enrollments.store'), $payload)
        ->assertRedirect(route('admin.enrollments.index'));

    $enrollment = Enrollment::query()
        ->where('user_id', $student->id)
        ->where('class_id', $class->id)
        ->firstOrFail();

    expect($enrollment->enrolled_at->toDateString())->toBe('2026-06-12')
        ->and($enrollment->status)->toBe('active');

    $this->actingAs($admin)
        ->post(route('admin.enrollments.store'), $payload)
        ->assertSessionHasErrors('class_id');
});

test('admin enrollment rejects non student users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'ETC Instructor']);
    $program = Program::query()->create([
        'name' => 'Role Guard Program',
        'slug' => 'role-guard-program',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'duration_meetings' => 16,
        'max_students' => 10,
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Role Guard Class',
        'status' => 'ongoing',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.enrollments.store'), [
            'user_id' => $instructor->id,
            'class_id' => $class->id,
            'enrolled_at' => '2026-06-12',
            'status' => 'active',
        ])
        ->assertSessionHasErrors('user_id');
});

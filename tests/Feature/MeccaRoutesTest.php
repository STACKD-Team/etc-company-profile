<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\ReportCard;
use App\Models\Room;
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

test('legacy registration programs route redirects to the public programs page', function () {
    $this->get('/registration/programs')
        ->assertRedirect(route('public.programs.index'));
});

test('legacy registration programs query also redirects to the public programs page', function () {
    $this->get('/registration/programs?program=2')
        ->assertRedirect(route('public.programs.index'));
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
        'student.payments.index',
        'student.payments.show',
        'student.report-cards.index',
        'student.report-cards.show',
        'student.report-cards.download',
        'student.help.index',
        'admin.student.index',
        'admin.student.show',
        'admin.instructor.index',
        'admin.instructor.show',
        'admin.program.index',
        'admin.program.create',
        'admin.program.store',
        'admin.program.edit',
        'admin.program.update',
        'admin.class.index',
        'admin.class.create',
        'admin.class.store',
        'admin.class.edit',
        'admin.class.update',
        'admin.enrollment.index',
        'admin.enrollment.store',
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

    $this->actingAs($student)->get(route('student.profile.show', [], false))->assertOk()->assertViewIs('pages.student.profile.show');
    $this->actingAs($student)->get(route('student.classes.index', [], false))->assertOk()->assertViewIs('pages.student.class.index')->assertSee('Intermediate B1');
    $this->actingAs($student)->get(route('student.classes.show', $class, false))->assertOk()->assertViewIs('pages.student.class.show')->assertSee('Intermediate B1');
    $this->actingAs($student)->get(route('student.learning-history.index', [], false))->assertOk()->assertViewIs('pages.student.learning-history.index');
    $this->actingAs($student)->get(route('student.report-cards.index', [], false))->assertOk()->assertViewIs('pages.student.report-card.index');
    $this->actingAs($student)->get(route('student.report-cards.show', $reportCard, false))->assertOk()->assertViewIs('pages.student.report-card.show')->assertSee('Detail Rapor');
    $this->actingAs($student)->get(route('student.help.index', [], false))->assertOk()->assertViewIs('pages.student.help.index');
});

test('student sprint three plural urls remain available as legacy aliases', function () {
    $student = User::factory()->create(['role' => 'student']);
    $class = createMeccaSprint4Class(['name' => 'Legacy Alias Class']);
    $enrollment = createMeccaSprint4Enrollment($student, $class);
    $reportCard = createMeccaSprint4ReportCard($enrollment);
    $payment = Registration::query()->create([
        'registration_code' => 'REG-LEGACY-ALIAS',
        'user_id' => $student->id,
        'program_id' => $class->program_id,
        'class_id' => $class->id,
        'applicant_name' => 'Legacy Alias Student',
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'payment_amount' => 1400000,
        'status' => 'pending_payment',
    ]);

    $this->actingAs($student)->get('/student/classes')->assertOk()->assertSee('Legacy Alias Class');
    $this->actingAs($student)->get("/student/classes/{$class->id}")->assertOk()->assertSee('Legacy Alias Class');
    $this->actingAs($student)->get('/student/payments')->assertOk()->assertSee('REG-LEGACY-ALIAS');
    $this->actingAs($student)->get("/student/payments/{$payment->id}")->assertOk()->assertSee('REG-LEGACY-ALIAS');
    $this->actingAs($student)->get('/student/report-cards')->assertOk();
    $this->actingAs($student)->get("/student/report-cards/{$reportCard->id}")->assertOk()->assertSee('Detail Rapor');
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
        ['get', route('student.payments.index', [], false), []],
        ['get', route('student.payments.show', Registration::query()->create([
            'registration_code' => 'REG-STUDENT-ACCESS',
            'user_id' => $student->id,
            'program_id' => $class->program_id,
            'class_id' => $class->id,
            'applicant_name' => 'Student Access',
            'applicant_email' => $student->email,
            'applicant_phone' => '081234567890',
            'payment_amount' => 1400000,
            'status' => 'pending_payment',
        ]), false), []],
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

    Registration::query()->create([
        'registration_code' => 'REG-DASHBOARD-PAID',
        'user_id' => $student->id,
        'program_id' => $activeClass->program_id,
        'class_id' => $activeClass->id,
        'applicant_name' => 'Mecca Student',
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'payment_amount' => 1400000,
        'payment_method' => 'qris',
        'paid_at' => '2026-06-10 10:00:00',
        'status' => 'paid',
    ]);

    $this->actingAs($student)
        ->get(route('student.dashboard', [], false))
        ->assertOk()
        ->assertSee('Mecca Student')
        ->assertSee('Owned Active Class')
        ->assertSee('Pembayaran Terakhir')
        ->assertSee('REG-DASHBOARD-PAID')
        ->assertSee('Lunas')
        ->assertSee('Rp 1.400.000')
        ->assertSee('Rapor Terbaru')
        ->assertSee('Riwayat Belajar Ringkas')
        ->assertDontSee('Other Student Class')
        ->assertSee(route('student.help.index', [], false), false)
        ->assertSee(route('student.classes.show', $activeClass, false), false)
        ->assertSee(route('student.report-cards.download', $downloadableReport, false), false)
        ->assertSee(route('student.learning-history.index', [], false), false);
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
        ->assertSee('data-student-classes-table', false)
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

test('student learning history shows all owned enrollments and published report links', function () {
    $student = User::factory()->create(['role' => 'student']);
    $otherStudent = User::factory()->create(['role' => 'student']);
    $activeClass = createMeccaSprint4Class(['name' => 'Still Active Class']);
    $completedClass = createMeccaSprint4Class(['name' => 'Completed History Class']);
    $droppedClass = createMeccaSprint4Class(['name' => 'Dropped History Class']);
    $otherClass = createMeccaSprint4Class(['name' => 'Other Student History Class']);

    createMeccaSprint4Enrollment($student, $activeClass, ['status' => 'active']);
    $completedEnrollment = createMeccaSprint4Enrollment($student, $completedClass, [
        'status' => 'completed',
        'completed_at' => '2026-07-12',
    ]);
    createMeccaSprint4Enrollment($student, $droppedClass, [
        'status' => 'dropped',
        'completed_at' => '2026-07-13',
    ]);
    createMeccaSprint4Enrollment($otherStudent, $otherClass, ['status' => 'completed']);
    $publishedReport = createMeccaSprint4ReportCard($completedEnrollment);

    $this->actingAs($student)
        ->get(route('student.learning-history.index', [], false))
        ->assertOk()
        ->assertSee('data-student-learning-history-table', false)
        ->assertSee('Still Active Class')
        ->assertSee('Completed History Class')
        ->assertSee('Dropped History Class')
        ->assertSee('Sedang Berjalan')
        ->assertSee('Rapor Published')
        ->assertSee(route('student.report-cards.show', $publishedReport, false), false)
        ->assertDontSee('Other Student History Class');
});

test('student sprint five rooms render from room relation and remain scoped in search', function () {
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Room Relation Student']);
    $otherStudent = User::factory()->create(['role' => 'student', 'full_name' => 'Other Room Student']);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'Room Instructor']);
    $program = createMeccaSprint4Program(['name' => 'Room Relation Program', 'slug' => 'room-relation-program']);
    $room = Room::query()->create([
        'name' => 'Mecca Sprint 5 Room',
        'description' => 'Room from rooms table.',
        'capacity' => 12,
        'is_active' => true,
        'display_order' => 1,
    ]);
    $hiddenRoom = Room::query()->create([
        'name' => 'Hidden Sprint 5 Room',
        'capacity' => 8,
        'is_active' => true,
        'display_order' => 2,
    ]);

    $ownedClass = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'room_id' => $room->id,
        'name' => 'Room Relation Class',
        'schedule_days' => 'Tue-Thu',
        'schedule_time' => '16.00-17.30',
        'status' => 'ongoing',
    ]);
    $otherOwnedClass = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'room_id' => $hiddenRoom->id,
        'name' => 'Different Room Class',
        'schedule_days' => 'Fri',
        'schedule_time' => '13.00-14.30',
        'status' => 'ongoing',
    ]);
    $otherStudentClass = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'room_id' => $room->id,
        'name' => 'Other Student Same Room Class',
        'schedule_days' => 'Sat',
        'schedule_time' => '09.00-10.30',
        'status' => 'ongoing',
    ]);

    $enrollment = createMeccaSprint4Enrollment($student, $ownedClass, ['status' => 'active']);
    createMeccaSprint4Enrollment($student, $otherOwnedClass, ['status' => 'completed']);
    createMeccaSprint4Enrollment($otherStudent, $otherStudentClass, ['status' => 'active']);
    $reportCard = createMeccaSprint4ReportCard($enrollment, ['pdf_path' => 'report-cards/room-relation.pdf']);

    $this->actingAs($student)
        ->get(route('student.dashboard', [], false))
        ->assertOk()
        ->assertSee('Room Relation Class')
        ->assertSee('Mecca Sprint 5 Room')
        ->assertDontSee('Other Student Same Room Class');

    $this->actingAs($student)
        ->get(route('student.classes.index', ['search' => 'Mecca Sprint 5 Room'], false))
        ->assertOk()
        ->assertViewHas('enrollments', fn ($enrollments) => collect($enrollments->items())->pluck('id')->all() === [$enrollment->id])
        ->assertSee('data-student-classes-table', false)
        ->assertSee('Room Relation Class')
        ->assertSee('Mecca Sprint 5 Room')
        ->assertDontSee('Other Student Same Room Class');

    $this->actingAs($student)
        ->get(route('student.classes.show', $ownedClass, false))
        ->assertOk()
        ->assertSee('Room Relation Class')
        ->assertSee('Mecca Sprint 5 Room');

    $this->actingAs($student)
        ->get(route('student.learning-history.index', ['search' => 'Mecca Sprint 5 Room'], false))
        ->assertOk()
        ->assertViewHas('enrollments', fn ($enrollments) => collect($enrollments->items())->pluck('id')->all() === [$enrollment->id])
        ->assertSee('Room Relation Class')
        ->assertSee('Mecca Sprint 5 Room')
        ->assertDontSee('Other Student Same Room Class');

    $this->actingAs($student)
        ->get(route('student.report-cards.index', [], false))
        ->assertOk()
        ->assertSee('Room Relation Program')
        ->assertSee('Mecca Sprint 5 Room');

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $reportCard, false))
        ->assertOk()
        ->assertSee('Room Relation Class')
        ->assertSee('Mecca Sprint 5 Room');
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
        ->assertSee('data-student-report-cards-table', false)
        ->assertSee('Owned Published Program')
        ->assertDontSee('Owned Draft Program')
        ->assertDontSee('Other Published Program');

    $this->actingAs($student)
        ->get(route('student.report-cards.show', $ownedPublishedReport, false))
        ->assertOk()
        ->assertSee('Written Test')
        ->assertSee('Overall Class Assessment')
        ->assertSee('Next Class')
        ->assertSee('Comments and Suggestions')
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

test('student sprint two list tables support search filters sorting and pagination', function () {
    $student = User::factory()->create(['role' => 'student']);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'Filter Instructor']);
    $program = createMeccaSprint4Program(['name' => 'Filter Program', 'slug' => 'filter-program']);
    $targetClass = createMeccaSprint4Class([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Filter Target Class',
    ]);
    $hiddenClass = createMeccaSprint4Class(['name' => 'Hidden Filter Class']);

    $targetEnrollment = createMeccaSprint4Enrollment($student, $targetClass, ['status' => 'completed']);
    createMeccaSprint4Enrollment($student, $hiddenClass, ['status' => 'active']);
    $report = createMeccaSprint4ReportCard($targetEnrollment, [
        'final_grade' => 'B',
        'total_score' => 82,
        'pdf_path' => 'report-cards/filter-target.pdf',
    ]);

    Registration::query()->create([
        'registration_code' => 'REG-FILTER-TARGET',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'class_id' => $targetClass->id,
        'applicant_name' => 'Filter Student',
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'payment_method' => 'qris',
        'payment_status' => 'paid',
        'payment_amount' => 1500000,
        'original_amount' => 1500000,
        'discount_amount' => 250000,
        'final_amount' => 1250000,
        'program_promotion_title' => 'Promo Mecca',
        'paid_at' => '2026-06-10 10:00:00',
        'status' => 'paid',
    ]);

    Registration::query()->create([
        'registration_code' => 'REG-FILTER-HIDDEN',
        'user_id' => $student->id,
        'program_id' => $hiddenClass->program_id,
        'class_id' => $hiddenClass->id,
        'applicant_name' => 'Filter Student',
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'payment_method' => 'manual',
        'payment_status' => 'waiting_payment',
        'payment_amount' => 1000000,
        'status' => 'pending_payment',
    ]);

    $this->actingAs($student)
        ->get(route('student.classes.index', [
            'search' => 'Target',
            'status' => 'completed',
            'sort' => 'status',
            'direction' => 'asc',
            'per_page' => 10,
        ], false))
        ->assertOk()
        ->assertViewHas('enrollments', fn ($enrollments) => collect($enrollments->items())->pluck('id')->all() === [$targetEnrollment->id])
        ->assertSee('data-student-classes-table', false)
        ->assertSee('Filter Target Class');

    $this->actingAs($student)
        ->get(route('student.learning-history.index', [
            'program_id' => $program->id,
            'instructor_id' => $instructor->id,
            'sort' => 'completed_at',
        ], false))
        ->assertOk()
        ->assertViewHas('enrollments', fn ($enrollments) => collect($enrollments->items())->pluck('id')->all() === [$targetEnrollment->id])
        ->assertSee('Filter Target Class')
        ->assertSee(route('student.report-cards.show', $report, false), false);

    $this->actingAs($student)
        ->get(route('student.payments.index', [
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'sort' => 'payment_amount',
            'direction' => 'desc',
        ], false))
        ->assertOk()
        ->assertSee('data-student-payments-table', false)
        ->assertSee('REG-FILTER-TARGET')
        ->assertSee('Promo Mecca')
        ->assertSee('Rp 1.250.000')
        ->assertDontSee('REG-FILTER-HIDDEN');

    $this->actingAs($student)
        ->get(route('student.report-cards.index', [
            'final_grade' => 'B',
            'report_status' => 'with_file',
            'sort' => 'total_score',
        ], false))
        ->assertOk()
        ->assertSee('data-student-report-cards-table', false)
        ->assertSee('Filter Program')
        ->assertSee('Nilai B');
});

test('student payment detail shows midtrans snapshot and continue payment link', function () {
    $student = User::factory()->create(['role' => 'student']);
    $program = createMeccaSprint4Program(['name' => 'Snapshot Payment Program', 'slug' => 'snapshot-payment-program']);

    $payment = Registration::query()->create([
        'registration_code' => 'REG-SNAPSHOT-PAY',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'applicant_name' => 'Snapshot Student',
        'applicant_email' => $student->email,
        'applicant_phone' => '081234567890',
        'payment_method' => 'virtual_account',
        'payment_status' => 'waiting_payment',
        'payment_amount' => 1800000,
        'original_amount' => 1800000,
        'discount_amount' => 300000,
        'final_amount' => 1500000,
        'program_promotion_title' => 'Promo Snapshot',
        'payment_gateway_id' => 'MIDTRANS-123',
        'midtrans_redirect_url' => 'https://pay.example.test/snap',
        'midtrans_snap_token' => 'SNAP-TOKEN-123',
        'payment_expires_at' => now()->addHour(),
        'status' => 'pending_payment',
    ]);

    $this->actingAs($student)
        ->get(route('student.payments.show', $payment, false))
        ->assertOk()
        ->assertSee('REG-SNAPSHOT-PAY')
        ->assertSee('Virtual Account')
        ->assertSee('MIDTRANS-123')
        ->assertSee('SNAP-TOKEN-123')
        ->assertSee('Promo Snapshot')
        ->assertSee('Rp 1.800.000')
        ->assertSee('Rp 300.000')
        ->assertSee('Rp 1.500.000')
        ->assertSee('Lanjutkan Pembayaran')
        ->assertSee('https://pay.example.test/snap', false)
        ->assertDontSee('Bukti Pembayaran');
});

test('admin academic mecca pages render for authenticated admin', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)->get('/admin/student')->assertOk();
    $this->actingAs($admin)->get('/admin/instructor')->assertOk();
    $this->actingAs($admin)->get('/admin/program')->assertOk();
    $this->actingAs($admin)->get('/admin/program/create')->assertOk();
    $this->actingAs($admin)->get('/admin/class')->assertOk();
    $this->actingAs($admin)->get('/admin/class/create')->assertOk();
    $this->actingAs($admin)->get('/admin/enrollment')->assertOk();

    $this->actingAs($admin)->get('/admin/students')->assertRedirect('/admin/student');
    $this->actingAs($admin)->get('/admin/programs/create')->assertRedirect('/admin/program/create');
    $this->actingAs($admin)->get('/admin/course-classes')->assertRedirect('/admin/class');
    $this->actingAs($admin)->get('/admin/classes/create')->assertRedirect('/admin/class/create');
});

test('admin academic routes require admin access', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->get('/admin/programs')->assertRedirect('/login');

    $this->actingAs($student)
        ->get(route('admin.program.index', [], false))
        ->assertForbidden();
});

test('admin student and instructor pages only expose matching roles', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
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
        ->get(route('admin.student.index', [], false))
        ->assertOk()
        ->assertSee('Mecca Student')
        ->assertDontSee('ETC Instructor');

    $this->actingAs($admin)
        ->get(route('admin.student.show', $student, false))
        ->assertOk()
        ->assertSee('Mecca Student');

    $this->actingAs($admin)
        ->get(route('admin.student.show', $instructor, false))
        ->assertNotFound();

    $this->actingAs($admin)
        ->get(route('admin.instructor.index', [], false))
        ->assertOk()
        ->assertSee('ETC Instructor')
        ->assertDontSee('Mecca Student');

    $this->actingAs($admin)
        ->get(route('admin.instructor.show', $instructor, false))
        ->assertOk()
        ->assertSee('ETC Instructor');

    $this->actingAs($admin)
        ->get(route('admin.instructor.show', $student, false))
        ->assertNotFound();
});

test('admin can create and update programs', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('admin.program.store'), [
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
        ->assertRedirect(route('admin.program.show', 'academic-english'));

    $program = Program::query()->where('slug', 'academic-english')->firstOrFail();

    expect($program->is_active)->toBeTrue();

    $this->actingAs($admin)
        ->put(route('admin.program.update', $program), [
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
        ->assertRedirect(route('admin.program.show', $program));

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
        ->post(route('admin.class.store'), $classPayload)
        ->assertRedirect(route('admin.class.show', 1));

    $class = CourseClass::query()->where('name', 'Teen B1')->firstOrFail();

    expect($class->instructor_id)->toBe($instructor->id)
        ->and($class->schedule_days)->toBe('Mon-Wed')
        ->and($class->status)->toBe('upcoming');

    $this->actingAs($admin)
        ->put(route('admin.class.update', $class), array_merge($classPayload, [
            'name' => 'Teen B2',
            'status' => 'ongoing',
        ]))
        ->assertRedirect(route('admin.class.show', $class));

    expect($class->refresh()->name)->toBe('Teen B2')
        ->and($class->status)->toBe('ongoing');

    $this->actingAs($admin)
        ->post(route('admin.class.store'), array_merge($classPayload, [
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
        ->post(route('admin.enrollment.store'), $payload)
        ->assertRedirect(route('admin.enrollment.show', 1));

    $enrollment = Enrollment::query()
        ->where('user_id', $student->id)
        ->where('class_id', $class->id)
        ->firstOrFail();

    expect($enrollment->enrolled_at->toDateString())->toBe('2026-06-12')
        ->and($enrollment->status)->toBe('active');

    $this->actingAs($admin)
        ->post(route('admin.enrollment.store'), $payload)
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
        ->post(route('admin.enrollment.store'), [
            'user_id' => $instructor->id,
            'class_id' => $class->id,
            'enrolled_at' => '2026-06-12',
            'status' => 'active',
        ])
        ->assertSessionHasErrors('user_id');
});

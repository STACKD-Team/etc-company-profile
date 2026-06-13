<?php

use App\Models\Content;
use App\Models\CourseClass;
use App\Models\ChatbotLog;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Reel;
use App\Models\ReportCard;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('registers Mia sprint four singular Blade admin route targets', function () {
    collect([
        'admin.student.index' => '/admin/student',
        'admin.instructor.index' => '/admin/instructor',
        'admin.registration.index' => '/admin/registration',
        'admin.payment.index' => '/admin/payment',
        'admin.placement-test.index' => '/admin/placement-test',
        'admin.program.index' => '/admin/program',
        'admin.class.index' => '/admin/class',
        'admin.room.index' => '/admin/room',
        'admin.enrollment.index' => '/admin/enrollment',
        'admin.report-card.index' => '/admin/report-card',
        'admin.reel.index' => '/admin/reel',
        'admin.gallery.index' => '/admin/gallery',
        'admin.partner.index' => '/admin/partner',
        'admin.testimonial.index' => '/admin/testimonial',
        'admin.faq.index' => '/admin/faq',
        'admin.profile.index' => '/admin/profile',
        'admin.contact-message.index' => '/admin/contact-message',
        'admin.chatbot-log.index' => '/admin/chatbot-log',
        'admin.program.show' => '/admin/program/example-program',
        'admin.class.show' => '/admin/class/1',
        'admin.enrollment.show' => '/admin/enrollment/1',
        'admin.reel.show' => '/admin/reel/1',
        'admin.gallery.show' => '/admin/gallery/1',
        'admin.partner.show' => '/admin/partner/1',
        'admin.testimonial.show' => '/admin/testimonial/1',
        'admin.faq.show' => '/admin/faq/1',
        'admin.chatbot-log.show' => '/admin/chatbot-log/1',
    ])->each(function (string $uri, string $routeName): void {
        expect(Route::has($routeName))->toBeTrue($routeName)
            ->and(route($routeName, sprintFourRouteParameters($routeName), false))->toBe($uri);
    });
});

it('redirects old plural admin URLs only after admin access is satisfied', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

    $this->get('/admin/programs')->assertRedirect('/login');

    $this->actingAs($student)
        ->get('/admin/programs')
        ->assertForbidden();

    $this->actingAs($admin)
        ->get('/admin/programs/12/edit')
        ->assertRedirect('/admin/program/12/edit');

    $this->actingAs($admin)
        ->get('/admin/classes/create')
        ->assertRedirect('/admin/class/create');
});

it('keeps RD admin resources without create edit or delete surfaces', function () {
    expect(Route::has('admin.payment.create'))->toBeFalse()
        ->and(Route::has('admin.payment.edit'))->toBeFalse()
        ->and(Route::has('admin.contact-message.create'))->toBeFalse()
        ->and(Route::has('admin.contact-message.edit'))->toBeFalse()
        ->and(Route::has('admin.chatbot-log.create'))->toBeFalse()
        ->and(Route::has('admin.chatbot-log.edit'))->toBeFalse()
        ->and(Route::has('admin.enrollment.create'))->toBeFalse();
});

it('renders sprint four detail pages with related operational links', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = User::factory()->create(['role' => 'student', 'full_name' => 'Sprint Student', 'is_active' => true]);
    $instructor = User::factory()->create(['role' => 'instructor', 'full_name' => 'Sprint Instructor', 'is_active' => true]);
    $program = Program::query()->create([
        'name' => 'Sprint Program Detail',
        'slug' => 'sprint-program-detail',
        'category' => 'english',
        'type' => 'regular',
        'target_age' => 'teen',
        'price' => 1200000,
        'registration_fee' => 200000,
        'is_active' => true,
    ]);
    $room = Room::query()->create(['name' => 'Sprint Detail Room', 'is_active' => true]);
    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'room_id' => $room->id,
        'name' => 'Sprint Detail Class',
        'status' => 'ongoing',
    ]);
    $enrollment = Enrollment::query()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'enrolled_at' => now(),
        'status' => 'active',
    ]);
    $reportCard = ReportCard::query()->create([
        'enrollment_id' => $enrollment->id,
        'final_grade' => 'A',
        'is_published' => true,
    ]);
    $reel = Reel::query()->create([
        'title' => 'Sprint Reel Detail',
        'video_path' => 'videos/video1.mp4',
        'category' => 'event',
        'is_published' => true,
    ]);
    $gallery = Content::query()->create([
        'type' => Content::TYPE_GALLERY,
        'title' => 'Sprint Gallery Detail',
        'slug' => 'sprint-gallery-detail',
        'is_published' => true,
    ]);
    $log = ChatbotLog::query()->create([
        'session_id' => 'sprint-session',
        'user_message' => 'Ada program apa?',
        'bot_response' => 'Ada banyak program.',
        'intent' => 'program',
    ]);

    $this->actingAs($admin)->get(route('admin.program.show', $program))
        ->assertOk()
        ->assertSee('Detail Program')
        ->assertSee('Sprint Detail Class')
        ->assertSee('Pendaftaran Terkait');

    $this->actingAs($admin)->get(route('admin.class.show', $class))
        ->assertOk()
        ->assertSee('Detail Kelas')
        ->assertSee('Sprint Student')
        ->assertSee(route('admin.report-card.show', $reportCard), false);

    $this->actingAs($admin)->get(route('admin.enrollment.show', $enrollment))
        ->assertOk()
        ->assertSee('Detail Enrollment')
        ->assertSee('Sprint Detail Class')
        ->assertSee(route('admin.report-card.show', $reportCard), false);

    $this->actingAs($admin)->get(route('admin.reel.show', $reel))
        ->assertOk()
        ->assertSee('Detail Reel')
        ->assertSee('Preview Media');

    $this->actingAs($admin)->get(route('admin.gallery.show', $gallery))
        ->assertOk()
        ->assertSee('Sprint Gallery Detail')
        ->assertSee('Media');

    $this->actingAs($admin)->get(route('admin.chatbot-log.show', $log))
        ->assertOk()
        ->assertSee('Detail Chatbot Log')
        ->assertSee('Ada program apa?');
});

it('aligns room schema and class room relationships for Sprint 4', function () {
    expect(Schema::hasTable('rooms'))->toBeTrue()
        ->and(Schema::hasColumn('classes', 'room_id'))->toBeTrue();

    $program = Program::query()->create([
        'name' => 'Room Schema Program',
        'slug' => 'room-schema-program',
        'category' => 'english',
    ]);
    $instructor = User::factory()->create(['role' => 'instructor']);
    $room = Room::query()->create(['name' => 'Sprint 4 Room', 'capacity' => 12]);

    $class = CourseClass::query()->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'room_id' => $room->id,
        'name' => 'Room Linked Class',
    ]);

    expect($class->refresh()->room->is($room))->toBeTrue()
        ->and($room->classes()->whereKey($class->id)->exists())->toBeTrue()
        ->and($class->room_label)->toBe('Sprint 4 Room');
});

it('uses only Sprint 4 CMS content types for admin-managed content', function () {
    foreach ([Content::TYPE_GALLERY, Content::TYPE_PARTNER, Content::TYPE_PROFILE, Content::TYPE_FAQ, Content::TYPE_TESTIMONIAL] as $type) {
        Content::query()->create([
            'type' => $type,
            'title' => 'Sprint 4 '.$type,
            'slug' => 'sprint-4-'.$type,
        ]);
    }

    expect(Content::TYPES)->toBe([
        Content::TYPE_GALLERY,
        Content::TYPE_PARTNER,
        Content::TYPE_PROFILE,
        Content::TYPE_FAQ,
        Content::TYPE_TESTIMONIAL,
    ])
        ->and(Content::query()->whereIn('type', Content::TYPES)->count())->toBe(5);
});

function sprintFourRouteParameters(string $routeName): array
{
    return match ($routeName) {
        'admin.program.show' => ['program' => 'example-program'],
        'admin.class.show' => ['class' => 1],
        'admin.enrollment.show' => ['enrollment' => 1],
        'admin.reel.show' => ['reel' => 1],
        'admin.gallery.show', 'admin.partner.show', 'admin.testimonial.show', 'admin.faq.show' => ['content' => 1],
        'admin.chatbot-log.show' => ['chatbotLog' => 1],
        default => [],
    };
}

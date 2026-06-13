<?php

use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\User;
use App\Services\ChatbotLogService;
use App\Services\ContactMessageService;
use App\Services\ContentService;
use App\Services\CourseClassService;
use App\Services\EnrollmentService;
use App\Services\MediaStorageService;
use App\Services\ProgramService;
use App\Services\ReelService;
use App\Services\RegistrationService;
use App\Services\ReportCardService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

class FakeMediaStorageService extends MediaStorageService
{
    public array $deleted = [];

    private int $counter = 0;

    public function putUploadedFile(UploadedFile $file, string $directory): string
    {
        $this->counter++;

        return trim($directory, '/').'/fake-'.$this->counter.'.'.($file->getClientOriginalExtension() ?: 'bin');
    }

    public function delete(?string $path): void
    {
        if ($path !== null && $path !== '') {
            $this->deleted[] = $path;
        }
    }

    public function replace(?string $oldPath, UploadedFile $file, string $directory): string
    {
        $path = $this->putUploadedFile($file, $directory);
        $this->delete($oldPath);

        return $path;
    }
}

beforeEach(function () {
    $this->mediaStorage = new FakeMediaStorageService();
    $this->app->instance(MediaStorageService::class, $this->mediaStorage);
});

it('performs CRUD operations through all model services', function () {
    $users = app(UserService::class);
    $programs = app(ProgramService::class);
    $classes = app(CourseClassService::class);
    $registrations = app(RegistrationService::class);
    $enrollments = app(EnrollmentService::class);
    $reportCards = app(ReportCardService::class);
    $reels = app(ReelService::class);
    $contents = app(ContentService::class);
    $messages = app(ContactMessageService::class);
    $chatbotLogs = app(ChatbotLogService::class);

    $student = $users->create([
        'name' => 'Budi Student',
        'email' => 'budi@example.test',
        'password' => 'password',
        'role' => 'student',
        'full_name' => 'Budi Student',
        'no_induk' => 'ETC001',
    ]);
    $instructor = $users->create([
        'name' => 'Ms Instructor',
        'email' => 'instructor@example.test',
        'password' => 'password',
        'role' => 'instructor',
    ]);

    $program = $programs->create([
        'name' => 'General English',
        'slug' => 'general-english',
        'category' => 'english',
        'type' => 'regular',
        'price' => 350000,
    ]);

    $courseClass = $classes->create([
        'program_id' => $program->id,
        'instructor_id' => $instructor->id,
        'name' => 'Teen 4',
        'schedule_days' => 'Tuesday and Thursday',
        'schedule_time' => '17:30 - 19:00',
    ]);

    $registration = $registrations->create([
        'registration_code' => 'REG-TEST-001',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'class_id' => $courseClass->id,
        'applicant_name' => 'Budi Student',
        'applicant_email' => 'budi@example.test',
        'applicant_phone' => '08123456789',
    ]);

    $enrollment = $enrollments->create([
        'user_id' => $student->id,
        'class_id' => $courseClass->id,
        'enrolled_at' => '2026-05-15',
    ]);

    $reportCard = $reportCards->create([
        'enrollment_id' => $enrollment->id,
        'score_listening' => 15,
        'score_vocabulary' => 16,
        'score_structure' => 14,
        'score_reading' => 15,
        'score_writing' => 16,
        'total_score' => 76,
        'final_grade' => 'B',
        'instructor_id' => $instructor->id,
    ]);

    $reel = $reels->create([
        'title' => 'Class Activity',
        'video_path' => 'reels/class-activity.mp4',
        'category' => 'dokumentasi',
    ]);

    $content = $contents->create([
        'type' => 'profile',
        'title' => 'Tentang ETC',
        'slug' => 'tentang-etc',
        'body' => 'Profil ETC Planet.',
    ]);

    $message = $messages->create([
        'name' => 'Visitor',
        'email' => 'visitor@example.test',
        'subject' => 'Info Program',
        'message' => 'Saya ingin bertanya.',
    ]);

    $log = $chatbotLogs->logInteraction(
        'session-1',
        'Apa saja programnya?',
        'ETC punya program English, TOEFL, dan lainnya.',
        'program',
        $student,
    );

    expect($users->find($student->id)->email)->toBe('budi@example.test')
        ->and($programs->find($program->id)->slug)->toBe('general-english')
        ->and($classes->find($courseClass->id)->getTable())->toBe('classes')
        ->and($registrations->find($registration->id)->registration_code)->toBe('REG-TEST-001')
        ->and($enrollments->find($enrollment->id)->status)->toBe('active')
        ->and($reportCards->find($reportCard->id)->total_score)->toBe(76)
        ->and($reels->find($reel->id)->views_count)->toBe(0)
        ->and($contents->find($content->id)->slug)->toBe('tentang-etc')
        ->and($messages->find($message->id)->is_read)->toBeFalse()
        ->and($chatbotLogs->find($log->id)->intent)->toBe('program');

    $users->update($student, ['full_name' => 'Budi Updated']);
    $programs->update($program, ['name' => 'General English Updated']);
    $classes->update($courseClass, ['status' => 'ongoing']);
    $registrations->uploadPaymentProof($registration, UploadedFile::fake()->create('proof.jpg', 1, 'image/jpeg'));
    $registrations->markAsPaid($registration, 550000, 'qris');
    $enrollments->complete($enrollment, '2026-08-15');
    $reportCards->publish($reportCard);
    $reels->incrementViews($reel);
    $contents->update($content, ['title' => 'Tentang ETC Updated']);
    $messages->markAsRead($message);
    $chatbotLogs->update($log, ['is_helpful' => true]);

    expect($student->refresh()->full_name)->toBe('Budi Updated')
        ->and($program->refresh()->name)->toBe('General English Updated')
        ->and($courseClass->refresh()->status)->toBe('ongoing')
        ->and($registration->refresh()->status)->toBe('paid')
        ->and($registration->paid_at)->not->toBeNull()
        ->and($enrollment->refresh()->status)->toBe('completed')
        ->and($reportCard->refresh()->is_published)->toBeTrue()
        ->and($reel->refresh()->views_count)->toBe(1)
        ->and($content->refresh()->title)->toBe('Tentang ETC Updated')
        ->and($message->refresh()->is_read)->toBeTrue()
        ->and($log->refresh()->is_helpful)->toBeTrue();

    $reportCards->delete($reportCard);
    $reels->delete($reel);
    $contents->delete($content);
    $messages->delete($message);
    $chatbotLogs->delete($log);
    $registrations->delete($registration);
    $enrollments->delete($enrollment);
    $classes->delete($courseClass);
    $programs->delete($program);
    $users->delete($student);

    expect($reportCard->fresh())->toBeNull()
        ->and($reel->fresh())->toBeNull()
        ->and($content->fresh())->toBeNull()
        ->and($message->fresh())->toBeNull()
        ->and($log->fresh())->toBeNull()
        ->and(Registration::withTrashed()->find($registration->id)->trashed())->toBeTrue()
        ->and(Enrollment::withTrashed()->find($enrollment->id)->trashed())->toBeTrue()
        ->and(CourseClass::withTrashed()->find($courseClass->id)->trashed())->toBeTrue()
        ->and(Program::withTrashed()->find($program->id)->trashed())->toBeTrue()
        ->and(User::withTrashed()->find($student->id)->trashed())->toBeTrue();
});

it('restores soft-deleted models and runs domain helper methods', function () {
    $users = app(UserService::class);
    $programs = app(ProgramService::class);
    $classes = app(CourseClassService::class);
    $registrations = app(RegistrationService::class);
    $enrollments = app(EnrollmentService::class);
    $reportCards = app(ReportCardService::class);
    $reels = app(ReelService::class);
    $messages = app(ContactMessageService::class);

    $student = $users->create([
        'name' => 'Restore Student',
        'email' => 'restore.student@example.test',
        'password' => 'password',
    ]);
    $instructor = $users->create([
        'name' => 'Restore Instructor',
        'email' => 'restore.instructor@example.test',
        'password' => 'password',
        'role' => 'instructor',
    ]);
    $program = $programs->create(['name' => 'TOEFL Prep', 'slug' => 'toefl-prep', 'category' => 'test_prep']);
    $courseClass = $classes->create(['program_id' => $program->id, 'instructor_id' => $instructor->id, 'name' => 'TOEFL A']);
    $registration = $registrations->create([
        'registration_code' => 'REG-TEST-002',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'applicant_name' => 'Restore Student',
        'applicant_email' => 'restore.student@example.test',
        'applicant_phone' => '0811111111',
    ]);
    $enrollment = $enrollments->create([
        'user_id' => $student->id,
        'class_id' => $courseClass->id,
        'enrolled_at' => '2026-05-15',
    ]);
    $reportCard = $reportCards->create(['enrollment_id' => $enrollment->id, 'total_score' => 80, 'final_grade' => 'B']);
    $reel = $reels->create(['title' => 'TOEFL Tips', 'video_path' => 'reels/toefl-tips.mp4']);
    $message = $messages->create(['name' => 'Visitor', 'email' => 'visitor2@example.test', 'message' => 'Halo']);

    $registrations->schedulePlacementTest($registration, '2026-05-20 10:00:00');
    $registrations->assignClass($registration, $courseClass);
    $enrollments->drop($enrollment);
    $reportCards->unpublish($reportCard);
    $reels->publish($reel);
    $reels->incrementLikes($reel);
    $messages->markAsReplied($message, '2026-05-15 12:00:00');

    expect($registration->refresh()->status)->toBe('enrolled')
        ->and($registration->class_id)->toBe($courseClass->id)
        ->and($enrollment->refresh()->status)->toBe('dropped')
        ->and($reportCard->refresh()->is_published)->toBeFalse()
        ->and($reel->refresh()->is_published)->toBeTrue()
        ->and($reel->likes_count)->toBe(1)
        ->and($message->refresh()->replied_at)->not->toBeNull();

    foreach ([[$users, $student], [$programs, $program], [$classes, $courseClass], [$registrations, $registration], [$enrollments, $enrollment]] as [$service, $model]) {
        $service->delete($model);
        $service->restore($model->id);

        expect($model::withTrashed()->find($model->id)->trashed())->toBeFalse();
    }
});

it('handles media lifecycle for file-aware services', function () {
    $users = app(UserService::class);
    $programs = app(ProgramService::class);
    $classes = app(CourseClassService::class);
    $registrations = app(RegistrationService::class);
    $enrollments = app(EnrollmentService::class);
    $reportCards = app(ReportCardService::class);
    $reels = app(ReelService::class);
    $contents = app(ContentService::class);

    $student = $users->create([
        'name' => 'Media Student',
        'email' => 'media.student@example.test',
        'password' => 'password',
    ]);
    $student = $users->updateAvatar($student, UploadedFile::fake()->create('avatar.jpg', 1, 'image/jpeg'));

    $program = $programs->createWithThumbnail([
        'name' => 'Media Program',
        'slug' => 'media-program',
        'category' => 'english',
    ], UploadedFile::fake()->create('program.jpg', 1, 'image/jpeg'));
    $program = $programs->updateWithThumbnail($program, [
        'name' => 'Media Program Updated',
    ], UploadedFile::fake()->create('program-new.jpg', 1, 'image/jpeg'));

    $courseClass = $classes->create([
        'program_id' => $program->id,
        'name' => 'Media Class',
    ]);
    $registration = $registrations->createFromOnlineForm([
        'registration_code' => 'REG-MEDIA-001',
        'user_id' => $student->id,
        'program_id' => $program->id,
        'applicant_name' => 'Media Student',
        'applicant_email' => 'media.student@example.test',
        'applicant_phone' => '0812222222',
    ]);
    $registration = $registrations->uploadPaymentProof($registration, UploadedFile::fake()->create('proof.jpg', 1, 'image/jpeg'));
    $oldProof = $registration->payment_proof;
    $registration = $registrations->uploadPaymentProof($registration, UploadedFile::fake()->create('proof-new.jpg', 1, 'image/jpeg'));
    $registrations->assignClass($registration, $courseClass);
    $enrollment = $registrations->createEnrollmentFromRegistration($registration);

    $reportCard = $reportCards->create([
        'enrollment_id' => $enrollment->id,
        'total_score' => 88,
        'final_grade' => 'A',
    ]);
    $reportCard = $reportCards->attachPdf($reportCard, UploadedFile::fake()->create('report.pdf', 1, 'application/pdf'));

    $reel = $reels->createWithMedia([
        'title' => 'Media Reel',
        'category' => 'edukasi',
    ], UploadedFile::fake()->create('lesson.mp4', 10, 'video/mp4'), UploadedFile::fake()->create('thumb.jpg', 1, 'image/jpeg'));
    $emptyReel = $reels->create([
        'title' => 'Empty Reel',
        'video_path' => '',
    ]);

    $content = $contents->createWithMedia([
        'type' => 'gallery',
        'title' => 'Media Gallery',
        'slug' => 'media-gallery',
    ], UploadedFile::fake()->create('cover.jpg', 1, 'image/jpeg'), [
        UploadedFile::fake()->create('gallery-1.jpg', 1, 'image/jpeg'),
        UploadedFile::fake()->create('gallery-2.jpg', 1, 'image/jpeg'),
    ]);
    $content = $contents->updateWithMedia($content, [
        'title' => 'Media Gallery Updated',
    ], UploadedFile::fake()->create('cover-new.jpg', 1, 'image/jpeg'), [
        UploadedFile::fake()->create('gallery-new.jpg', 1, 'image/jpeg'),
    ]);

    expect($student->avatar)->toStartWith('users/avatars/')
        ->and($program->thumbnail)->toStartWith('programs/thumbnails/')
        ->and($registration->payment_proof)->toStartWith('registrations/payment-proofs/')
        ->and($oldProof)->toBeIn($this->mediaStorage->deleted)
        ->and($enrollment)->toBeInstanceOf(Enrollment::class)
        ->and($reportCard->pdf_path)->toStartWith('report-cards/pdfs/')
        ->and($reel->video_path)->toStartWith('reels/videos/')
        ->and($reel->thumbnail_path)->toStartWith('reels/thumbnails/')
        ->and($content->image)->toStartWith('contents/images/')
        ->and($content->images)->toHaveCount(1);

    expect(fn () => $reels->publish($emptyReel))->toThrow(RuntimeException::class);

    $reels->publish($reel);
    $reels->forceDelete($reel);
    $contents->forceDelete($content);
    $reportCards->forceDelete($reportCard);
    $enrollments->forceDelete($enrollment);
    $registrations->forceDelete($registration);
    $classes->forceDelete($courseClass);
    $programs->forceDelete($program);
    $users->forceDelete($student);

    expect($this->mediaStorage->deleted)->toContain(
        $reel->video_path,
        $reel->thumbnail_path,
        $content->image,
        $reportCard->pdf_path,
        $registration->payment_proof,
        $program->thumbnail,
        $student->avatar,
    );
});

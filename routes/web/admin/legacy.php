<?php

use App\Http\Controllers\Admin\ChatbotLogController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\PlacementTestController;
use App\Http\Controllers\Admin\PlacementTestResultController;
use App\Http\Controllers\Admin\PlacementTestScheduleController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\ReelController;
use App\Http\Controllers\Admin\ReportCardController;
use App\Http\Controllers\Admin\ReportCardPublishController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/legacy')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
        Route::get('/registrations/{registration}/edit', [RegistrationController::class, 'edit'])->name('registrations.edit');
        Route::put('/registrations/{registration}', [RegistrationController::class, 'update'])->name('registrations.update');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [PaymentVerificationController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.reject');

        Route::get('/placement-tests', [PlacementTestController::class, 'index'])->name('placement-tests.index');
        Route::get('/placement-tests/{registration}', [PlacementTestController::class, 'show'])->name('placement-tests.show');
        Route::post('/placement-tests/{registration}/schedule', [PlacementTestScheduleController::class, 'store'])->name('placement-tests.schedule');
        Route::post('/placement-tests/{registration}/result', [PlacementTestResultController::class, 'store'])->name('placement-tests.result.store');

        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

        Route::get('/instructors', [InstructorController::class, 'index'])->name('instructors.index');
        Route::get('/instructors/{instructor}', [InstructorController::class, 'show'])->name('instructors.show');

        Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/create', [ProgramController::class, 'create'])->name('programs.create');
        Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');

        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');

        Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');

        Route::get('/report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('/report-cards/create', [ReportCardController::class, 'create'])->name('report-cards.create');
        Route::post('/report-cards', [ReportCardController::class, 'store'])->name('report-cards.store');
        Route::get('/report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
        Route::get('/report-cards/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
        Route::put('/report-cards/{reportCard}', [ReportCardController::class, 'update'])->name('report-cards.update');
        Route::post('/report-cards/{reportCard}/publish', [ReportCardPublishController::class, 'store'])->name('report-cards.publish');

        Route::get('/reels', [ReelController::class, 'index'])->name('reels.index');
        Route::get('/reels/create', [ReelController::class, 'create'])->name('reels.create');
        Route::post('/reels', [ReelController::class, 'store'])->name('reels.store');
        Route::get('/reels/{reel}/edit', [ReelController::class, 'edit'])->name('reels.edit');
        Route::put('/reels/{reel}', [ReelController::class, 'update'])->name('reels.update');

        Route::get('/contents', [ContentController::class, 'index'])->name('contents.index');
        Route::get('/contents/create', [ContentController::class, 'create'])->name('contents.create');
        Route::post('/contents', [ContentController::class, 'store'])->name('contents.store');
        Route::get('/contents/{content}/edit', [ContentController::class, 'edit'])->name('contents.edit');
        Route::put('/contents/{content}', [ContentController::class, 'update'])->name('contents.update');

        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::get('/chatbot-logs', [ChatbotLogController::class, 'index'])->name('chatbot-logs.index');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

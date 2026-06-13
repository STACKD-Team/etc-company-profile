<?php

use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\PlacementTestClearController;
use App\Http\Controllers\Admin\PlacementTestController;
use App\Http\Controllers\Admin\PlacementTestResultController;
use App\Http\Controllers\Admin\PlacementTestScheduleController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::redirect('/placement-tests', '/admin/placement-test')->name('legacy.placement-tests.index');
        Route::redirect('/placement-tests/{registration}', '/admin/placement-test/{registration}')->name('legacy.placement-tests.show');
        Route::redirect('/students', '/admin/student')->name('legacy.students.index');
        Route::redirect('/students/{student}', '/admin/student/{student}')->name('legacy.students.show');
        Route::redirect('/instructors', '/admin/instructor')->name('legacy.instructors.index');
        Route::redirect('/instructors/{instructor}', '/admin/instructor/{instructor}')->name('legacy.instructors.show');
        Route::redirect('/programs', '/admin/program')->name('legacy.programs.index');
        Route::redirect('/programs/create', '/admin/program/create')->name('legacy.programs.create');
        Route::redirect('/programs/{program}', '/admin/program/{program}')->name('legacy.programs.show');
        Route::redirect('/programs/{program}/edit', '/admin/program/{program}/edit')->name('legacy.programs.edit');
        Route::redirect('/classes', '/admin/class')->name('legacy.classes.index');
        Route::redirect('/classes/create', '/admin/class/create')->name('legacy.classes.create');
        Route::redirect('/classes/{class}', '/admin/class/{class}')->name('legacy.classes.show');
        Route::redirect('/classes/{class}/edit', '/admin/class/{class}/edit')->name('legacy.classes.edit');
        Route::redirect('/course-classes', '/admin/class')->name('legacy.course-classes.index');
        Route::redirect('/course-classes/create', '/admin/class/create')->name('legacy.course-classes.create');
        Route::redirect('/course-classes/{class}', '/admin/class/{class}')->name('legacy.course-classes.show');
        Route::redirect('/course-classes/{class}/edit', '/admin/class/{class}/edit')->name('legacy.course-classes.edit');
        Route::redirect('/enrollments', '/admin/enrollment')->name('legacy.enrollments.index');
        Route::redirect('/enrollments/{enrollment}', '/admin/enrollment/{enrollment}')->name('legacy.enrollments.show');

        Route::get('/placement-test', [PlacementTestController::class, 'index'])->name('placement-test.index');
        Route::get('/placement-test/{registration}', [PlacementTestController::class, 'show'])->name('placement-test.show');
        Route::post('/placement-test/{registration}/schedule', [PlacementTestScheduleController::class, 'store'])->name('placement-test.schedule');
        Route::post('/placement-test/{registration}/result', [PlacementTestResultController::class, 'store'])->name('placement-test.result.store');
        Route::delete('/placement-test/{registration}/clear', PlacementTestClearController::class)->name('placement-test.clear');

        Route::get('/student', [StudentController::class, 'index'])->name('student.index');
        Route::get('/student/create', [StudentController::class, 'create'])->name('student.create');
        Route::post('/student', [StudentController::class, 'store'])->name('student.store');
        Route::get('/student/{student}', [StudentController::class, 'show'])->name('student.show');
        Route::get('/student/{student}/edit', [StudentController::class, 'edit'])->name('student.edit');
        Route::put('/student/{student}', [StudentController::class, 'update'])->name('student.update');
        Route::delete('/student/{student}', [StudentController::class, 'destroy'])->name('student.destroy');

        Route::get('/instructor', [InstructorController::class, 'index'])->name('instructor.index');
        Route::get('/instructor/create', [InstructorController::class, 'create'])->name('instructor.create');
        Route::post('/instructor', [InstructorController::class, 'store'])->name('instructor.store');
        Route::get('/instructor/{instructor}', [InstructorController::class, 'show'])->name('instructor.show');
        Route::get('/instructor/{instructor}/edit', [InstructorController::class, 'edit'])->name('instructor.edit');
        Route::put('/instructor/{instructor}', [InstructorController::class, 'update'])->name('instructor.update');
        Route::delete('/instructor/{instructor}', [InstructorController::class, 'destroy'])->name('instructor.destroy');

        Route::get('/program', [ProgramController::class, 'index'])->name('program.index');
        Route::get('/program/create', [ProgramController::class, 'create'])->name('program.create');
        Route::post('/program', [ProgramController::class, 'store'])->name('program.store');
        Route::get('/program/{program}', [ProgramController::class, 'show'])->name('program.show');
        Route::get('/program/{program}/edit', [ProgramController::class, 'edit'])->name('program.edit');
        Route::put('/program/{program}', [ProgramController::class, 'update'])->name('program.update');
        Route::delete('/program/{program}', [ProgramController::class, 'destroy'])->name('program.destroy');

        Route::get('/class', [ClassController::class, 'index'])->name('class.index');
        Route::get('/class/create', [ClassController::class, 'create'])->name('class.create');
        Route::post('/class', [ClassController::class, 'store'])->name('class.store');
        Route::get('/class/{class}', [ClassController::class, 'show'])->name('class.show');
        Route::get('/class/{class}/edit', [ClassController::class, 'edit'])->name('class.edit');
        Route::put('/class/{class}', [ClassController::class, 'update'])->name('class.update');
        Route::delete('/class/{class}', [ClassController::class, 'destroy'])->name('class.destroy');

        Route::resource('room', RoomController::class);

        Route::get('/enrollment', [EnrollmentController::class, 'index'])->name('enrollment.index');
        Route::post('/enrollment', [EnrollmentController::class, 'store'])->name('enrollment.store');
        Route::get('/enrollment/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollment.show');
        Route::get('/enrollment/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('enrollment.edit');
        Route::put('/enrollment/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollment.update');
        Route::delete('/enrollment/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollment.destroy');
    });

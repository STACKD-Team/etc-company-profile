<?php

use App\Http\Controllers\Instructor\ClassController;
use App\Http\Controllers\Instructor\DashboardController;
use App\Http\Controllers\Instructor\ReportCardController;
use App\Http\Controllers\Instructor\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('instructor')
    ->name('instructor.')
    ->middleware(['auth', 'role:instructor'])
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('/enrollments/{enrollment}/report-card/create', [ReportCardController::class, 'create'])->name('report-cards.create');
        Route::post('/enrollments/{enrollment}/report-card', [ReportCardController::class, 'store'])->name('report-cards.store');
        Route::get('/report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
        Route::get('/report-cards/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
        Route::put('/report-cards/{reportCard}', [ReportCardController::class, 'update'])->name('report-cards.update');
    });

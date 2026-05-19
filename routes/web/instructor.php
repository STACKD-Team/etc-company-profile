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
    });

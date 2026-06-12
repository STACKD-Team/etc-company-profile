<?php

use App\Http\Controllers\Instructor\ClassController;
use App\Http\Controllers\Instructor\DashboardController;
use App\Http\Controllers\Instructor\ProfileController;
use App\Http\Controllers\Instructor\ReportCardController;
use App\Http\Controllers\Instructor\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('instructor')
    ->name('instructor.')
    ->middleware(['auth', 'role:instructor'])
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/class', [ClassController::class, 'index'])->name('classes.index');
        Route::get('/class/{class}', [ClassController::class, 'show'])->name('classes.show');
        Route::get('/student', [StudentController::class, 'index'])->name('students.index');
        Route::get('/report-card', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('/enrollment/{enrollment}/report-card/create', [ReportCardController::class, 'create'])->name('report-cards.create');
        Route::post('/enrollment/{enrollment}/report-card', [ReportCardController::class, 'store'])->name('report-cards.store');
        Route::get('/report-card/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
        Route::get('/report-card/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
        Route::put('/report-card/{reportCard}', [ReportCardController::class, 'update'])->name('report-cards.update');
    });

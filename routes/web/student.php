<?php

use App\Http\Controllers\Student\ClassController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\HelpController;
use App\Http\Controllers\Student\LearningHistoryController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ReportCardController;
use App\Http\Controllers\Student\ReportCardDownloadController;
use Illuminate\Support\Facades\Route;

Route::prefix('student')
    ->name('student.')
    ->middleware(['auth', 'role:student'])
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])
            ->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::get('/classes', [ClassController::class, 'index'])
            ->name('classes.index');
        Route::get('/classes/{class}', [ClassController::class, 'show'])
            ->name('classes.show');
        Route::get('/learning-history', [LearningHistoryController::class, 'index'])
            ->name('learning-history.index');
        Route::get('/report-cards', [ReportCardController::class, 'index'])
            ->name('report-cards.index');
        Route::get('/report-cards/{reportCard}', [ReportCardController::class, 'show'])
            ->name('report-cards.show');
        Route::get('/report-cards/{reportCard}/download', ReportCardDownloadController::class)
            ->name('report-cards.download');
        Route::get('/help', [HelpController::class, 'index'])
            ->name('help.index');
    });

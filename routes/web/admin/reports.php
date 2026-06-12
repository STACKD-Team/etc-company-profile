<?php

use App\Http\Controllers\Admin\ReportCardExportController;
use App\Http\Controllers\Admin\StudentExportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::get('/exports/students', [StudentExportController::class, 'index'])->name('exports.students');
        Route::post('/exports/students', [StudentExportController::class, 'download'])->name('exports.students.download');
        Route::get('/exports/report-cards', [ReportCardExportController::class, 'index'])->name('exports.report-cards');
        Route::post('/exports/report-cards', [ReportCardExportController::class, 'download'])->name('exports.report-cards.download');
    });

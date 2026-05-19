<?php

use App\Http\Controllers\Admin\PlacementTestController;
use App\Http\Controllers\Admin\PlacementTestResultController;
use App\Http\Controllers\Admin\PlacementTestScheduleController;
use App\Http\Controllers\Admin\ReportCardController;
use App\Http\Controllers\Admin\ReportCardExportController;
use App\Http\Controllers\Admin\ReportCardPublishController;
use App\Http\Controllers\Admin\StudentExportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::get('/placement-tests', [PlacementTestController::class, 'index'])->name('placement-tests.index');
        Route::get('/placement-tests/{registration}', [PlacementTestController::class, 'show'])->name('placement-tests.show');
        Route::post('/placement-tests/{registration}/schedule', [PlacementTestScheduleController::class, 'store'])->name('placement-tests.schedule');
        Route::post('/placement-tests/{registration}/result', [PlacementTestResultController::class, 'store'])->name('placement-tests.result.store');

        Route::get('/report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('/report-cards/create', [ReportCardController::class, 'create'])->name('report-cards.create');
        Route::post('/report-cards', [ReportCardController::class, 'store'])->name('report-cards.store');
        Route::get('/report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
        Route::get('/report-cards/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
        Route::put('/report-cards/{reportCard}', [ReportCardController::class, 'update'])->name('report-cards.update');
        Route::post('/report-cards/{reportCard}/publish', [ReportCardPublishController::class, 'store'])->name('report-cards.publish');

        Route::get('/exports/students', [StudentExportController::class, 'index'])->name('exports.students');
        Route::post('/exports/students', [StudentExportController::class, 'download'])->name('exports.students.download');
        Route::get('/exports/report-cards', [ReportCardExportController::class, 'index'])->name('exports.report-cards');
        Route::post('/exports/report-cards', [ReportCardExportController::class, 'download'])->name('exports.report-cards.download');
    });

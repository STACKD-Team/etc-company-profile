<?php

use App\Http\Controllers\Admin\ReportCardExportController;
use App\Http\Controllers\Admin\ReportCardController;
use App\Http\Controllers\Admin\ReportCardPublishController;
use App\Http\Controllers\Admin\StudentExportController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::redirect('/report-cards', '/admin/report-card')->name('legacy.report-cards.index');
        Route::redirect('/report-cards/create', '/admin/report-card/create')->name('legacy.report-cards.create');
        Route::redirect('/report-cards/{reportCard}', '/admin/report-card/{reportCard}')->name('legacy.report-cards.show');
        Route::redirect('/report-cards/{reportCard}/edit', '/admin/report-card/{reportCard}/edit')->name('legacy.report-cards.edit');

        Route::get('/report-card', [ReportCardController::class, 'index'])->name('report-card.index');
        Route::get('/report-card/create', [ReportCardController::class, 'create'])->name('report-card.create');
        Route::post('/report-card', [ReportCardController::class, 'store'])->name('report-card.store');
        Route::get('/report-card/{reportCard}', [ReportCardController::class, 'show'])->name('report-card.show');
        Route::get('/report-card/{reportCard}/edit', [ReportCardController::class, 'edit'])->name('report-card.edit');
        Route::put('/report-card/{reportCard}', [ReportCardController::class, 'update'])->name('report-card.update');
        Route::delete('/report-card/{reportCard}', [ReportCardController::class, 'destroy'])->name('report-card.destroy');
        Route::post('/report-card/{reportCard}/publish', [ReportCardPublishController::class, 'store'])->name('report-card.publish');

        Route::get('/exports/students', [StudentExportController::class, 'index'])->name('exports.students');
        Route::post('/exports/students', [StudentExportController::class, 'download'])->name('exports.students.download');
        Route::get('/exports/report-cards', [ReportCardExportController::class, 'index'])->name('exports.report-cards');
        Route::post('/exports/report-cards', [ReportCardExportController::class, 'download'])->name('exports.report-cards.download');
    });

<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [PaymentVerificationController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.reject');
    });

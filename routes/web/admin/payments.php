<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::redirect('/payments', '/admin/payment')->name('legacy.payments.index');
        Route::redirect('/payments/{payment}', '/admin/payment/{payment}')->name('legacy.payments.show');

        Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('/payment/{payment}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{payment}/verify', [PaymentVerificationController::class, 'verify'])->name('payment.verify');
        Route::post('/payment/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payment.reject');
    });

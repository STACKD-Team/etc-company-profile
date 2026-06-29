<?php

use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::redirect('/payments', '/admin/payment')->name('legacy.payments.index');
        Route::redirect('/payments/{payment}', '/admin/payment/{payment}')->name('legacy.payments.show');

        Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('/payment/{payment}', [PaymentController::class, 'show'])->name('payment.show');
    });

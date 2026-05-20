<?php

use App\Http\Controllers\Public\RegistrationConfirmationController;
use App\Http\Controllers\Public\RegistrationController;
use App\Http\Controllers\Public\RegistrationPaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('registration')->name('registrations.')->group(function (): void {
    Route::get('/form/{program?}', [RegistrationController::class, 'create'])->name('create');
    Route::get('/payment/{registration}', [RegistrationPaymentController::class, 'show'])->name('payment.show');
    Route::get('/confirmation/{registration}', [RegistrationConfirmationController::class, 'show'])->name('confirmation.show');
});

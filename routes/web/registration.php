<?php

use App\Http\Controllers\Public\RegistrationConfirmationController;
use App\Http\Controllers\Public\RegistrationController;
use App\Http\Controllers\Public\RegistrationPaymentController;
use App\Http\Controllers\Public\RegistrationPaymentProofController;
use App\Http\Controllers\Public\RegistrationProgramController;
use App\Http\Controllers\Public\RegistrationReceiptController;
use App\Http\Controllers\Public\RegistrationStartController;
use Illuminate\Support\Facades\Route;

Route::prefix('registration')
    ->name('registrations.')
    ->group(function (): void {
        Route::get('/', [RegistrationStartController::class, 'index'])
            ->name('start');
        Route::get('/programs', [RegistrationProgramController::class, 'index'])
            ->name('programs.index');
        Route::get('/form/{program?}', [RegistrationController::class, 'create'])
            ->name('create');
        Route::post('/', [RegistrationController::class, 'store'])
            ->middleware('throttle:registration')
            ->name('store');
        Route::get('/payment/{registration}', [RegistrationPaymentController::class, 'show'])
            ->middleware('signed.optional')
            ->name('payment.show');
        Route::post('/payment/{registration}/proof', [RegistrationPaymentProofController::class, 'store'])
            ->middleware('throttle:upload')
            ->name('payment.proof.store');
        Route::post('/payment/{registration}/confirm', [RegistrationPaymentController::class, 'confirm'])
            ->middleware('throttle:payment')
            ->name('payment.confirm');
        Route::get('/confirmation/{registration}', [RegistrationConfirmationController::class, 'show'])
            ->middleware('signed.optional')
            ->name('confirmation.show');
        Route::get('/{registration}/receipt', [RegistrationReceiptController::class, 'download'])
            ->middleware('signed.optional')
            ->name('receipt.download');
    });

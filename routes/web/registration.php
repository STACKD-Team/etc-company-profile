<?php

use App\Http\Controllers\Public\RegistrationProgramController;
use Illuminate\Support\Facades\Route;

Route::prefix('registration')
    ->name('registrations.')
    ->group(function (): void {
        Route::get('/programs', [RegistrationProgramController::class, 'index'])
            ->name('programs.index');
    });

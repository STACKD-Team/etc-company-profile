<?php

use App\Http\Controllers\Admin\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::redirect('/registrations', '/admin/registration')->name('legacy.registrations.index');
        Route::redirect('/registrations/{registration}', '/admin/registration/{registration}')->name('legacy.registrations.show');
        Route::redirect('/registrations/{registration}/edit', '/admin/registration/{registration}/edit')->name('legacy.registrations.edit');

        Route::get('/registration', [RegistrationController::class, 'index'])->name('registration.index');
        Route::get('/registration/{registration}', [RegistrationController::class, 'show'])->name('registration.show');
        Route::get('/registration/{registration}/edit', [RegistrationController::class, 'edit'])->name('registration.edit');
        Route::put('/registration/{registration}', [RegistrationController::class, 'update'])->name('registration.update');
    });

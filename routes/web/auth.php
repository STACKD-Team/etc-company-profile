<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('auth.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login')->name('auth.login.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('auth.password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:password')->name('auth.password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('auth.password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('auth.password.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('auth.logout');

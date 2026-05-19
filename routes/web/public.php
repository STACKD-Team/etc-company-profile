<?php

use App\Http\Controllers\Public\ProgramController;
use Illuminate\Support\Facades\Route;

Route::prefix('programs')->name('public.programs.')->group(function (): void {
    Route::get('/{program}', [ProgramController::class, 'show'])->name('show');
});

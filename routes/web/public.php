<?php

use App\Http\Controllers\Public\ProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/programs', [ProgramController::class, 'index'])
    ->name('public.programs.index');

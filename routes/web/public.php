<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ChatbotController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\FacilityController;
use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProgramController;
use App\Http\Controllers\Public\ReelController;
use App\Http\Controllers\Public\ReelLikeController;
use App\Http\Controllers\Public\ReelViewController;
use App\Http\Controllers\Public\TeamController;
use Illuminate\Support\Facades\Route;

Route::name('public.')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
    Route::post('/chatbot/messages', [ChatbotController::class, 'store'])->middleware('throttle:chatbot')->name('chatbot.messages.store');
    Route::get('/reels', [ReelController::class, 'index'])->name('reels.index');
    Route::post('/reels/{reel}/views', [ReelViewController::class, 'store'])->middleware('throttle:reels')->name('reels.views.store');
    Route::post('/reels/{reel}/likes', [ReelLikeController::class, 'store'])->middleware('throttle:reels')->name('reels.likes.store');
    Route::get('/reels/{reel}', [ReelController::class, 'show'])->name('reels.show');
});

Route::get('/programs', [ProgramController::class, 'index'])
    ->name('public.programs.index');

Route::prefix('programs')->name('public.programs.')->group(function (): void {
    Route::get('/{program}', [ProgramController::class, 'show'])->name('show');
});

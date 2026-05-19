<?php

use App\Http\Controllers\Admin\ChatbotLogController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ReelController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/reels', [ReelController::class, 'index'])->name('reels.index');
    Route::get('/reels/create', [ReelController::class, 'create'])->name('reels.create');
    Route::post('/reels', [ReelController::class, 'store'])->name('reels.store');
    Route::get('/reels/{reel}/edit', [ReelController::class, 'edit'])->name('reels.edit');
    Route::put('/reels/{reel}', [ReelController::class, 'update'])->name('reels.update');

    Route::get('/contents', [ContentController::class, 'index'])->name('contents.index');
    Route::get('/contents/create', [ContentController::class, 'create'])->name('contents.create');
    Route::post('/contents', [ContentController::class, 'store'])->name('contents.store');
    Route::get('/contents/{content}/edit', [ContentController::class, 'edit'])->name('contents.edit');
    Route::put('/contents/{content}', [ContentController::class, 'update'])->name('contents.update');

    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::get('/chatbot-logs', [ChatbotLogController::class, 'index'])->name('chatbot-logs.index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

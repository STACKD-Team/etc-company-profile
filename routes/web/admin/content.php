<?php

use App\Http\Controllers\Admin\ChatbotLogController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ReelController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function (): void {
    Route::redirect('/reels', '/admin/reel')->name('legacy.reels.index');
    Route::redirect('/reels/create', '/admin/reel/create')->name('legacy.reels.create');
    Route::redirect('/reels/{reel}', '/admin/reel/{reel}')->name('legacy.reels.show');
    Route::redirect('/reels/{reel}/edit', '/admin/reel/{reel}/edit')->name('legacy.reels.edit');
    Route::redirect('/contents', '/admin/gallery')->name('legacy.contents.index');
    Route::redirect('/settings', '/admin/profile')->name('legacy.settings.index');
    Route::redirect('/contact-messages', '/admin/contact-message')->name('legacy.contact-messages.index');
    Route::redirect('/contact-messages/{contactMessage}', '/admin/contact-message/{contactMessage}')->name('legacy.contact-messages.show');
    Route::redirect('/chatbot-logs', '/admin/chatbot-log')->name('legacy.chatbot-logs.index');
    Route::redirect('/chatbot-logs/{chatbotLog}', '/admin/chatbot-log/{chatbotLog}')->name('legacy.chatbot-logs.show');

    Route::get('/reel', [ReelController::class, 'index'])->name('reel.index');
    Route::get('/reel/create', [ReelController::class, 'create'])->name('reel.create');
    Route::post('/reel', [ReelController::class, 'store'])->name('reel.store');
    Route::get('/reel/{reel}', [ReelController::class, 'show'])->name('reel.show');
    Route::get('/reel/{reel}/edit', [ReelController::class, 'edit'])->name('reel.edit');
    Route::put('/reel/{reel}', [ReelController::class, 'update'])->name('reel.update');

    foreach (['gallery', 'partner', 'testimonial', 'faq'] as $type) {
        Route::get('/'.$type, [ContentController::class, 'index'])->defaults('contentType', $type)->name($type.'.index');
        Route::get('/'.$type.'/create', [ContentController::class, 'create'])->defaults('contentType', $type)->name($type.'.create');
        Route::post('/'.$type, [ContentController::class, 'store'])->defaults('contentType', $type)->name($type.'.store');
        Route::get('/'.$type.'/{content}', [ContentController::class, 'show'])->defaults('contentType', $type)->name($type.'.show');
        Route::get('/'.$type.'/{content}/edit', [ContentController::class, 'edit'])->defaults('contentType', $type)->name($type.'.edit');
        Route::put('/'.$type.'/{content}', [ContentController::class, 'update'])->defaults('contentType', $type)->name($type.'.update');
    }

    Route::get('/contact-message', [ContactMessageController::class, 'index'])->name('contact-message.index');
    Route::get('/contact-message/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-message.show');
    Route::get('/chatbot-log', [ChatbotLogController::class, 'index'])->name('chatbot-log.index');
    Route::get('/chatbot-log/{chatbotLog}', [ChatbotLogController::class, 'show'])->name('chatbot-log.show');
    Route::get('/profile', [SettingController::class, 'index'])->name('profile.index');
    Route::put('/profile', [SettingController::class, 'update'])->name('profile.update');
});

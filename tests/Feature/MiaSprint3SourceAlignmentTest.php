<?php

use Illuminate\Support\Facades\Route;

test('mia sprint three canonical filament admin routes are registered', function () {
    collect([
        'filament.admin.pages.dashboard' => 'admin',
        'filament.admin.resources.registrations.index' => 'admin/registrations',
        'filament.admin.resources.payments.index' => 'admin/payments',
        'filament.admin.resources.programs.index' => 'admin/programs',
        'filament.admin.resources.program-promotions.index' => 'admin/program-promotions',
        'filament.admin.resources.gallery-items.index' => 'admin/gallery-items',
        'filament.admin.resources.partners.index' => 'admin/partners',
        'filament.admin.resources.chatbot-logs.index' => 'admin/chatbot-logs',
        'filament.admin.resources.contact-messages.index' => 'admin/contact-messages',
        'filament.admin.resources.settings.index' => 'admin/settings',
        'filament.admin.resources.rag-knowledge-sources.index' => 'admin/rag-knowledge-sources',
    ])->each(function (string $uri, string $routeName): void {
        expect(Route::has($routeName))->toBeTrue($routeName);
        expect(route($routeName, absolute: false))->toBe('/'.$uri);
    });
});

test('mia sprint three legacy blade admin route names stay under admin legacy', function () {
    collect([
        'admin.dashboard' => 'admin/legacy/dashboard',
        'admin.registrations.index' => 'admin/legacy/registrations',
        'admin.payments.index' => 'admin/legacy/payments',
        'admin.programs.index' => 'admin/legacy/programs',
        'admin.contents.index' => 'admin/legacy/contents',
        'admin.settings.index' => 'admin/legacy/settings',
    ])->each(function (string $uri, string $routeName): void {
        expect(Route::has($routeName))->toBeTrue($routeName);
        expect(route($routeName, absolute: false))->toBe('/'.$uri);
    });

    expect(route('admin.payments.verify', ['payment' => 1], false))->toBe('/admin/legacy/payments/1/verify')
        ->and(route('admin.payments.reject', ['payment' => 1], false))->toBe('/admin/legacy/payments/1/reject');
});

test('mia sprint three midtrans webhook route is registered', function () {
    expect(Route::has('payments.midtrans.notification'))->toBeTrue()
        ->and(route('payments.midtrans.notification', absolute: false))->toBe('/payments/midtrans/notification');
});

test('mia sprint three route inventory documents canonical and legacy route split', function () {
    $inventory = file_get_contents(base_path('context/WEB_ROUTES_ETC.md'));

    expect($inventory)->toContain('Canonical Filament Admin Routes')
        ->and($inventory)->toContain('Legacy Blade Admin Routes')
        ->and($inventory)->toContain('filament.admin.resources.registrations.index')
        ->and($inventory)->toContain('/admin/legacy/registrations')
        ->and($inventory)->toContain('payments.midtrans.notification');
});

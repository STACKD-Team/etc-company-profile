<?php

use Illuminate\Support\Facades\Route;

test('mia admin canonical blade routes are registered', function () {
    collect([
        'admin.dashboard' => 'admin/dashboard',
        'admin.registration.index' => 'admin/registration',
        'admin.payment.index' => 'admin/payment',
        'admin.program.index' => 'admin/program',
        'admin.gallery.index' => 'admin/gallery',
        'admin.partner.index' => 'admin/partner',
        'admin.chatbot-log.index' => 'admin/chatbot-log',
        'admin.contact-message.index' => 'admin/contact-message',
        'admin.profile.index' => 'admin/profile',
        'admin.room.index' => 'admin/room',
        'admin.faq.index' => 'admin/faq',
        'admin.testimonial.index' => 'admin/testimonial',
        'admin.placement-test.index' => 'admin/placement-test',
    ])->each(function (string $uri, string $routeName): void {
        expect(Route::has($routeName))->toBeTrue($routeName);
        expect(route($routeName, absolute: false))->toBe('/'.$uri);
    });
});

test('mia old plural admin URLs redirect to singular blade routes', function () {
    collect([
        'admin.legacy.registrations.index' => 'admin/registrations',
        'admin.legacy.payments.index' => 'admin/payments',
        'admin.legacy.programs.index' => 'admin/programs',
        'admin.legacy.contents.index' => 'admin/contents',
        'admin.legacy.settings.index' => 'admin/settings',
    ])->each(function (string $uri, string $routeName): void {
        expect(Route::has($routeName))->toBeTrue($routeName);
        expect(route($routeName, absolute: false))->toBe('/'.$uri);
    });

    expect(Route::has('admin.payment.verify'))->toBeFalse()
        ->and(Route::has('admin.payment.reject'))->toBeFalse();
});

test('mia sprint three midtrans webhook route is registered', function () {
    expect(Route::has('payments.midtrans.notification'))->toBeTrue()
        ->and(route('payments.midtrans.notification', absolute: false))->toBe('/payments/midtrans/notification');
});

test('mia route inventory documents blade canonical and redirect compatibility routes', function () {
    $inventory = file_get_contents(base_path('context/WEB_ROUTES_ETC.md'));

    expect($inventory)->toContain('Canonical Blade Admin Routes')
        ->and($inventory)->toContain('Plural Admin Redirect Compatibility Routes')
        ->and($inventory)->toContain('admin.registration.index')
        ->and($inventory)->toContain('/admin/registrations')
        ->and($inventory)->toContain('payments.midtrans.notification');
});

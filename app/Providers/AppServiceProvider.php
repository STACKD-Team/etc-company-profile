<?php

namespace App\Providers;

use App\Services\PublicDiscoveryService;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'primary' => [
                50 => '255, 230, 243',
                100 => '255, 204, 231',
                200 => '255, 153, 207',
                300 => '255, 102, 183',
                400 => '255, 51, 159',
                500 => '230, 0, 127',
                600 => '185, 0, 101',
                700 => '146, 0, 80',
                800 => '102, 0, 56',
                900 => '69, 0, 38',
                950 => '39, 0, 22',
            ],
        ]);

        foreach ($this->rateLimiters() as $name => $limit) {
            RateLimiter::for($name, function (Request $request) use ($limit): Limit {
                return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
            });
        }

        View::composer('components.public-discovery.footer', function ($view): void {
            $view->with('profileSettings', app(PublicDiscoveryService::class)->settings());
        });
    }

    /**
     * @return array<string, int>
     */
    protected function rateLimiters(): array
    {
        return [
            'contact' => 5,
            'chatbot' => 20,
            'reels' => 60,
            'login' => 5,
            'password' => 5,
            'registration' => 8,
            'upload' => 10,
            'payment' => 10,
        ];
    }
}

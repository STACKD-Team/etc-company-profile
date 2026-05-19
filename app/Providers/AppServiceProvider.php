<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        foreach ($this->rateLimiters() as $name => $limit) {
            RateLimiter::for($name, function (Request $request) use ($limit): Limit {
                return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
            });
        }
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
        ];
    }
}

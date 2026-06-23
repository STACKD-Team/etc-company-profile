<?php

namespace App\Providers;

use App\Models\CourseClass;
use App\Models\Registration;
use App\Models\ReportCard;
use App\Policies\CourseClassPolicy;
use App\Policies\RegistrationPolicy;
use App\Policies\ReportCardPolicy;
use App\Services\PublicDiscoveryService;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        $this->registerVendorFallbackAutoloaders();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(CourseClass::class, CourseClassPolicy::class);
        Gate::policy(Registration::class, RegistrationPolicy::class);
        Gate::policy(ReportCard::class, ReportCardPolicy::class);

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

    protected function registerVendorFallbackAutoloaders(): void
    {
        spl_autoload_register(static function (string $class): void {
            static $cloudinaryClassFiles = null;

            $prefixes = [
                'Midtrans\\' => [base_path('vendor/midtrans/midtrans-php/Midtrans')],
                'SnapBi\\' => [base_path('vendor/midtrans/midtrans-php/SnapBi')],
                'Composer\\Pcre\\' => [base_path('vendor/composer/pcre/src')],
                'MarkBaker\\Complex\\' => [base_path('vendor/markbaker/complex/classes/src')],
                'MarkBaker\\Matrix\\' => [base_path('vendor/markbaker/matrix/classes/src')],
                'PhpOffice\\Math\\' => [base_path('vendor/phpoffice/math/src/Math')],
                'PhpOffice\\PhpSpreadsheet\\' => [base_path('vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet')],
                'PhpOffice\\PhpWord\\' => [base_path('vendor/phpoffice/phpword/src/PhpWord')],
                'Psr\\SimpleCache\\' => [base_path('vendor/psr/simple-cache/src')],
                'ZipStream\\' => [base_path('vendor/maennchen/zipstream-php/src')],
                'Cloudinary\\' => [
                    base_path('vendor/cloudinary/cloudinary_php/src'),
                    base_path('vendor/cloudinary/transformation-builder-sdk/src'),
                ],
            ];

            foreach ($prefixes as $prefix => $directories) {
                if (! str_starts_with($class, $prefix)) {
                    continue;
                }

                $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))).'.php';

                foreach ($directories as $directory) {
                    $path = $directory.DIRECTORY_SEPARATOR.$relative;

                    if (is_file($path)) {
                        require_once $path;

                        return;
                    }
                }

                if ($prefix === 'Cloudinary\\') {
                    $cloudinaryClassFiles ??= self::cloudinaryClassFiles($directories);
                    $shortName = substr($class, (int) strrpos($class, '\\') + 1);
                    $path = $cloudinaryClassFiles[$shortName] ?? null;

                    if (is_string($path) && is_file($path)) {
                        require_once $path;

                        return;
                    }
                }
            }
        });
    }

    /**
     * @param array<int, string> $directories
     * @return array<string, string>
     */
    protected static function cloudinaryClassFiles(array $directories): array
    {
        $files = [];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if (! $file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                    continue;
                }

                $files[$file->getBasename('.php')] ??= $file->getPathname();
            }
        }

        return $files;
    }
}

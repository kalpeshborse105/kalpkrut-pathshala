<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\{SystemSetting, Institute, Company};

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
        $this->runHostingerPostPublishBootstrap();

        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('system_settings')) {
                    $settings = Cache::remember('system_settings_dict', 300, function () {
                        return SystemSetting::pluck('value', 'key')->toArray();
                    });
                    $institute = Cache::remember('default_institute', 300, function () {
                        return Institute::first();
                    });
                    $company = Cache::remember('default_company', 300, function () {
                        return Company::first();
                    });

                    $instituteName = $settings['institute_name'] ?? $institute?->name ?? 'Kalpkrut Pathshala';

                    $view->with([
                        'systemSettings' => $settings,
                        'appInstitute' => $institute,
                        'appCompany' => $company,
                        'appInstituteName' => $instituteName,
                    ]);
                }
            } catch (\Throwable $e) {
                $view->with([
                    'systemSettings' => [],
                    'appInstitute' => null,
                    'appCompany' => null,
                    'appInstituteName' => 'Kalpkrut Pathshala',
                ]);
            }
        });
    }

    /**
     * Hostinger Git publishes into public_html, then first request finalizes migrate/cache.
     */
    private function runHostingerPostPublishBootstrap(): void
    {
        if (!app()->environment('production')) {
            return;
        }

        $root = base_path();
        if (!str_contains(str_replace('\\', '/', $root), '/domains/')) {
            return;
        }

        $lock = $root.'/composer.lock';
        $marker = storage_path('framework/.hostinger_bootstrapped');
        if (!is_file($lock)) {
            return;
        }
        if (is_file($marker) && filemtime($marker) >= filemtime($lock)) {
            return;
        }

        try {
            foreach ([
                storage_path('app/public'),
                storage_path('framework/cache/data'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
                storage_path('logs'),
                base_path('bootstrap/cache'),
            ] as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
            }

            if (!is_link(public_path('storage'))) {
                try {
                    Artisan::call('storage:link', ['--force' => true]);
                } catch (\Throwable $e) {
                    // ignore if symlink already exists/unavailable
                }
            }

            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            @touch($marker);
        } catch (\Throwable $e) {
            // Keep serving even if one-time bootstrap fails.
            report($e);
        }
    }
}


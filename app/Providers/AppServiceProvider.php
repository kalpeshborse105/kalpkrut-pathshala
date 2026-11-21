<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
}


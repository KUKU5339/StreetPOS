<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        if (config('app.env') === 'production') {
            try {
                DB::connection()->getPdo();
                if (!Schema::hasTable('users')) {
                    Artisan::call('migrate', ['--force' => true]);
                    Log::info('migrate: executed');
                }
            } catch (\Throwable $e) {
                Log::error('bootstrap error: ' . $e->getMessage());
            }
        }
    }
}

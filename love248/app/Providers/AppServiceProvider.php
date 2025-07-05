<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Add custom URL generator for the Storage facade
        if (env('FILESYSTEM_DISK') === 'local') {
            \Illuminate\Support\Facades\URL::macro('storage', function ($path) {
                return url('/users/' . str_replace('users/', '', $path));
            });
        }

        // URL::forceScheme('https');
        Model::unguard();
    }
}

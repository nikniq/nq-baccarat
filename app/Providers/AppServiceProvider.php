<?php

namespace App\Providers;

use App\Socialite\NikniqProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Socialite::extend('nikniq', function ($app) {
            $config = $app['config']['services.nikniq'];

            return Socialite::buildProvider(NikniqProvider::class, $config);
        });
    }
}

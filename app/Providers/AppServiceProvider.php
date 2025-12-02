<?php

namespace App\Providers;

use App\Http\Contracts\HttpStreamClientContract;
use App\Http\Services\HttpStreamClient;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HttpStreamClientContract::class, HttpStreamClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addHours(1));
        Passport::refreshTokensExpireIn(now()->addHour(2));
    }
}

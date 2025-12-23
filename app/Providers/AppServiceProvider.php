<?php

namespace App\Providers;

use App\Http\Clients\HttpStreamClient;
use App\Http\Contracts\HttpStreamClientContract;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        RateLimiter::for("auth", function (Request $request) {
            return Limit::perMinute(3, 30)
            ->by($request->ip())
            ->response(function () {
                return response()->json([
                    "message" => "Too many requests. Please try again 2 mins later."
                ], Response::HTTP_TOO_MANY_REQUESTS);
            });
        });

        RateLimiter::for("api", function (Request $request) {
            return $request->user()
            ? Limit::perMinute(60)->by($request->user()->id)
            : Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for("public", function (Request $request) {
            return Limit::perMinute(3, 5)->by($request->ip());
        });
    }
}

<?php

namespace App\Providers;

use App\Http\Clients\HttpStreamClient;
use App\Http\Contracts\HttpStreamClientContract;
use App\Http\Contracts\ProductLowestPriceRepositoryContract;
use App\Http\Contracts\ProductServiceContract;
use App\Http\Repositories\CachedProductLowestPriceRepository;
use App\Http\Repositories\ProductLowestPriceRepository;
use App\Http\Services\Product\ProductService;
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
        $this->app->bind(ProductLowestPriceRepositoryContract::class, ProductLowestPriceRepository::class);
        $this->app->bind(ProductServiceContract::class, ProductService::class);
        $this->app->singleton(
            ProductLowestPriceRepositoryContract::class,
            function ($app) {
                return new CachedProductLowestPriceRepository($app->make(ProductLowestPriceRepository::class));
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(3, 30)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests. Please try again 2 mins later.',
                    ], Response::HTTP_TOO_MANY_REQUESTS);
                });
        });

        RateLimiter::for('api', function (Request $request) {
            return $request->user()
            ? Limit::perMinute(60)->by($request->user()->id)
            : Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(100, 5)->by($request->ip());
        });
    }
}

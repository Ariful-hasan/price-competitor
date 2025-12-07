<?php

namespace App\Http\Factories;


use App\Http\Contracts\ExtractProductPriceContract;
use App\Http\Services\Product\PriceExtractors\VersionOneProductExtractService;
use App\Http\Services\Product\PriceExtractors\VersionThreeProductExtractService;
use App\Http\Services\Product\PriceExtractors\VersionTwoProductExtractService;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class ProductPricesExtractorFactory
{
    public function __construct(private Container $container) 
    {
    }

    public function make(string $pointer): ExtractProductPriceContract
    {
        
        return match($pointer) {
            '/prices' => $this->container->make(VersionOneProductExtractService::class),
            '/competitor_data' => $this->container->make(VersionTwoProductExtractService::class),
            '/competitor_data/price_comparison' => $this->container->make(VersionThreeProductExtractService::class),
            default => throw new InvalidArgumentException("Unknown pointer: {$pointer}")
        };
    }
}
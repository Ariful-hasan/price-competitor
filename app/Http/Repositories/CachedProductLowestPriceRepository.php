<?php

namespace App\Http\Repositories;

use App\Http\Contracts\ProductLowestPriceRepositoryContract;
use App\Models\ProductLowestPrice;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CachedProductLowestPriceRepository implements ProductLowestPriceRepositoryContract
{
    private const string CACHE_KEY = 'product_lowest_price:';
    private const int CACHE_TTL = 3600;

    public function __construct(private readonly ProductLowestPriceRepository $repository)
    {
        
    }

    public function saveLowestPrice(
        int $productId, 
        string $vendorName,
        float $price, 
        Carbon $fetchAt,
        bool $isCacheOnly = false
    ) : ProductLowestPrice
    {
        // Update the cache so the next 'getProduct' call is fresh
        $product = ProductLowestPrice::make([
            'product_id' => $productId,
            'vendor_name' => $vendorName,
            'price' => $price,
            'fetched_at' => $fetchAt,
        ]);
        $product->updateTimestamps();

        if (!$isCacheOnly) {
            $product = $this->repository->saveLowestPrice($productId, $vendorName, $price, $fetchAt);
        }

        Cache::put(self::CACHE_KEY . $productId, $product, self::CACHE_TTL);
        
        return $product;
    }

    public function getProductList(): LengthAwarePaginator
    {
        return $this->repository->getProductList();
    }

    public function getProduct(int $productId): ProductLowestPrice
    {
        return Cache::remember(
            self::CACHE_KEY . $productId, 
            self::CACHE_TTL, 
            function () use ($productId) {
            return $this->repository->getProduct($productId);
        });
    }
}
<?php

namespace App\Http\Contracts;

use App\Models\ProductLowestPrice;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductLowestPriceRepositoryContract
{
    final public const int LIMIT = 20;
    
    public function getProduct(int $productId): ProductLowestPrice;

    public function getProductList(): LengthAwarePaginator;

    public function saveLowestPrice(int $productId, string $vendorName, float $price, Carbon $fetchAt, bool $isCacheOnly = false) : ProductLowestPrice;
}



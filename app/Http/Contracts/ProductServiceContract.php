<?php

namespace App\Http\Contracts;

use App\Http\Resources\ProductLowestPriceResource;
use App\Models\ProductLowestPrice;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface ProductServiceContract
{
    public function getLowestProductList(): AnonymousResourceCollection;

    public function getLowestPriceProductById(int $productId): ProductLowestPriceResource;

    public function storeLowestPriceProduct(array $data): ProductLowestPrice;
}

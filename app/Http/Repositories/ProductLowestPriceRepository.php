<?php

namespace App\Http\Repositories;

use App\Models\ProductLowestPrice;
use Carbon\Carbon;

class ProductLowestPriceRepository
{
    public function saveLowestPrice(int $productId, string $vendorName, float $price, Carbon $fetchAt) : ProductLowestPrice
    {
        return ProductLowestPrice::updateOrCreate(
            ['product_id' => $productId],
            [
                'product_id' => $productId,
                'vendor_name' => $vendorName,
                'price' => $price,
                'fetched_at' => $fetchAt
            ]
        );
    }
}
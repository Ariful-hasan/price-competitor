<?php

namespace App\Http\Repositories;

use App\Models\ProductLowestPrice;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductLowestPriceRepository
{
    private const int LIMIT =10;

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

    public function getProductList(): LengthAwarePaginator
    {
        return ProductLowestPrice::orderByDesc('product_id')->paginate(static::LIMIT);
    }

    public function getProduct(int $productId): ?ProductLowestPrice
    {
        return ProductLowestPrice::where('product_id', $productId)->first();
    }
}
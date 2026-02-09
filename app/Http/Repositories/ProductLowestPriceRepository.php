<?php

namespace App\Http\Repositories;

use App\Http\Contracts\ProductLowestPriceRepositoryContract;
use App\Models\ProductLowestPrice;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductLowestPriceRepository implements ProductLowestPriceRepositoryContract
{
    public function saveLowestPrice(int $productId, string $vendorName, float $price, Carbon $fetchAt, bool $isCacheOnly = false): ProductLowestPrice
    {
        return ProductLowestPrice::updateOrCreate(
            ['product_id' => $productId],
            [
                'product_id' => $productId,
                'vendor_name' => $vendorName,
                'price' => $price,
                'fetched_at' => $fetchAt,
            ]
        );
    }

    public function getProductList(): LengthAwarePaginator
    {
        return ProductLowestPrice::orderByDesc('product_id')->paginate(self::LIMIT);
    }

    public function getProduct(int $productId): ProductLowestPrice
    {
        return ProductLowestPrice::where('product_id', $productId)->firstOrFail();
    }
}

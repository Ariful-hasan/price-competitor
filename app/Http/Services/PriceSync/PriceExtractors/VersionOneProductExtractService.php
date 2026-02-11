<?php

namespace App\Http\Services\PriceSync\PriceExtractors;

use App\Http\Contracts\ExtractProductPriceContract;
use App\Http\DTOs\SyncProductPriceDTO;

class VersionOneProductExtractService implements ExtractProductPriceContract
{
    public function extractProduct(array $data, int $productId): SyncProductPriceDTO
    {
        return new SyncProductPriceDTO(
            vendorName: $data['vendor'],
            price: (float) $data['price'],
            productId: $productId
        );
    }
}

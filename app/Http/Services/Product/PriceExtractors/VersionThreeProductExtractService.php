<?php

namespace App\Http\Services\Product\PriceExtractors;

use App\Http\Contracts\ExtractProductPriceContract;
use App\Http\DTOs\SyncProductPriceDTO;

class VersionThreeProductExtractService implements ExtractProductPriceContract
{
    public function extractProduct(array $data): SyncProductPriceDTO
    {
        return new SyncProductPriceDTO(
            vendorName: $data['name'],
            price: (float)$data['pricing']?->current
        );
    }
}
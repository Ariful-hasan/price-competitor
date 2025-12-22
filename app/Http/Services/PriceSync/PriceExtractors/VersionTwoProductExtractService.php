<?php

namespace App\Http\Services\PriceSync\PriceExtractors;

use App\Http\Contracts\ExtractProductPriceContract;
use App\Http\DTOs\SyncProductPriceDTO;

class VersionTwoProductExtractService implements ExtractProductPriceContract
{
    public function extractProduct(array $data): SyncProductPriceDTO
    {
        return new SyncProductPriceDTO(
            vendorName: $data['name'],
            price: (float)$data['amount']
        );
    }
}
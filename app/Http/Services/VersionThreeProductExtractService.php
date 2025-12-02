<?php

namespace App\Http\Services;

use App\Http\Contracts\ExtractProductPriceContract;

class VersionThreeProductExtractService implements ExtractProductPriceContract
{
    public function extractProduct(array $data): array
    {
        return [
            'vendor_name' => $data['name'],
            'price' => (float)$data['pricing']?->current
        ];
    }
}
<?php

namespace App\Http\Services;

use App\Http\Contracts\ExtractProductPriceContract;

class VersionTwoProductExtractService implements ExtractProductPriceContract
{
    public function extractProduct(array $data): array
    {
        return [
            'vendor_name' => $data['name'], 
            'price' => $data['amount']
        ];
    }
}
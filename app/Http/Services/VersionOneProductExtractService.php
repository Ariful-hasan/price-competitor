<?php

namespace App\Http\Services;

use App\Http\Contracts\ExtractProductPriceContract;
use Ramsey\Uuid\Type\Decimal;

class VersionOneProductExtractService implements ExtractProductPriceContract
{
    public function extractProduct(array $data): array
    {
        return [
            'vendor_name' => $data['vendor'], 
            'price' => (float)$data['price']
        ];
    }
}
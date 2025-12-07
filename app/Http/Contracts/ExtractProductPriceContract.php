<?php 

namespace App\Http\Contracts;

use App\Http\DTOs\SyncProductPriceDTO;

interface ExtractProductPriceContract
{
    public function extractProduct(array $data): SyncProductPriceDTO;
}
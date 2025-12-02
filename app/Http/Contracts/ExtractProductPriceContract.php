<?php 

namespace App\Http\Contracts;

interface ExtractProductPriceContract
{
    public function extractProduct(array $data): array;
}
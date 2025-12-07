<?php

namespace App\Http\DTOs;

final class SyncProductPriceDTO
{
    public function __construct(
        public readonly string $vendorName,
        public readonly float $price
    ) {}
}

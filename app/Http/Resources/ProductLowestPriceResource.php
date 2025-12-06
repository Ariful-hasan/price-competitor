<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLowestPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id'  => $this->product_id,
            'vendor'      => $this->vendor_name,
            'price'       => $this->price,
            'fetched_at'  => $this->fetched_at->copy()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}

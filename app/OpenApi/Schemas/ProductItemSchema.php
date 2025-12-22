<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ProductItem",
    description: "Product price from vendor"
)]
class ProductItemSchema
{
    #[OA\Property(property: "product_id", type: "integer", example: 124)]
    public $product_id;
    
    #[OA\Property(property: "vendor", type: "string", example: "Target")]
    public $vendor;
    
    #[OA\Property(property: "price", type: "string", example: "17.49")]
    public $price;
    
    #[OA\Property(property: "fetched_at", type: "string", format: "date-time")]
    public $fetched_at;
}
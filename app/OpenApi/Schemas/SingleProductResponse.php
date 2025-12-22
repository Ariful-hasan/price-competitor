<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "SingleProductResponse")]
class SingleProductResponse
{
    #[OA\Property(property: "success", type: "boolean", example: true)]
    public $success;
    
    #[OA\Property(
        property: "data",
        ref: "#/components/schemas/ProductItem"
    )]
    public $data;
}
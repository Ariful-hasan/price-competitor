<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ProductListResponse")]
class ProductListResponse
{
    #[OA\Property(property: "success", type: "boolean", example: true)]
    public $success;
    
    #[OA\Property(
        property: "data",
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/ProductItem")
    )]
    public $data;
    
    #[OA\Property(property: "links", type: "object")]
    public $links;
    
    #[OA\Property(property: "meta", type: "object")]
    public $meta;
}
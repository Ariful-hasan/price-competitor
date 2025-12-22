<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class ProductPaths
{
    // GET /products (matches your route)
    #[OA\Get(
        path: "/api/products",
        operationId: "getProductsList",
        tags: ["ProductsList"],
        summary: "Get all products",
        description: "Returns paginated list of products with prices",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "page",
        in: "query",
        schema: new OA\Schema(type: "integer", default: 1)
    )]
    #[OA\Parameter(
        name: "per_page",
        in: "query",
        schema: new OA\Schema(type: "integer", default: 20, maximum: 100)
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: "#/components/schemas/ProductListResponse")
    )]
    #[OA\Response(response: 401, description: "Unauthorized")]
    #[OA\Response(response: 500, description: 'Exception', content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Something went wrong.'),
                new OA\Property(property: 'success', type: 'boolean', example: false)
            ]
        )
    )]
    public function index() {}
    

    // GET /products/{product_id} (matches your route)
    #[OA\Get(
        path: "/api/products/{product_id}",
        operationId: "getProduct",
        tags: ["Product"],
        summary: "Get single product",
        description: "Returns price details for a specific product",
        security: [["bearerAuth" => []]]
    )]
    #[OA\Parameter(
        name: "product_id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer"),
        description: "Product ID"
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: "#/components/schemas/SingleProductResponse")
    )]
    #[OA\Response(response: 404, description: "Product not found")]
    #[OA\Response(response: 401, description: "Unauthorized")]
    public function show() {}
}
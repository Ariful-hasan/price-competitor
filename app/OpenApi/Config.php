<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi]
#[OA\Info(
    title: "Product API",
    version: "1.0.0",
    description: "API for product price comparison"
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: "Local")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]
#[OA\Tag(name: "Products", description: "Product management")]
class Config {}
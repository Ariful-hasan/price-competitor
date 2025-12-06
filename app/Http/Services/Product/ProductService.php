<?php


namespace App\Http\Services\Product;

use App\Http\Repositories\ProductLowestPriceRepository;
use App\Http\Resources\ProductLowestPriceResource;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductService
{

    public function __construct(private ProductLowestPriceRepository $repository)
    {
        
    }

    public function getLowestProductList(): AnonymousResourceCollection
    {
        try {
            return ProductLowestPriceResource::collection($this->repository->getProductList());
        } catch (\Throwable $th) {
            Log::error('Failed to fetch product list: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            throw new Exception('Unable to fetch the product list.');
        }
    }

    public function getLowestPriceProductById(int $productId): ProductLowestPriceResource
    {
        $product = $this->repository->getProduct($productId);
        
        if (!$product) {
            throw new ModelNotFoundException('No data found for this id.');
        }

        return new ProductLowestPriceResource($product);
    }
}
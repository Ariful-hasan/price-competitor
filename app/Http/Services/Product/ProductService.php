<?php


namespace App\Http\Services\Product;

use App\Http\Repositories\ProductLowestPriceRepository;
use App\Http\Resources\ProductLowestPriceResource;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
        try {
            return new ProductLowestPriceResource($this->repository->getProduct($productId));
        } catch (\Throwable $th) {
            Log::error('Failed to fetch product : ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            $isNotFouud = $th instanceof ModelNotFoundException;

            throw new Exception(
                $isNotFouud ? 'Product not found.' : 'Unable to fetch the product list.',
                $isNotFouud ? Response::HTTP_NOT_FOUND : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
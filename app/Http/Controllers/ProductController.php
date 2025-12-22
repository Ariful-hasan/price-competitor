<?php

namespace App\Http\Controllers;

use App\Http\Services\Product\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;


class ProductController extends Controller
{
    public function __construct(private ProductService  $service)
    {
        $this->middleware('auth:api');
    }

    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            return $this->service
            ->getLowestProductList()
            ->additional(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?? 'Something went wrong.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $productId): JsonResource|JsonResponse
    {
        try {
            return $this->service
            ->getLowestPriceProductById($productId)
            ->additional(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?? 'Something went wrong.',
                ], 
                $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
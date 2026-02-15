<?php

namespace App\Http\Actions;

use App\Http\Contracts\HttpStreamClientContract;
use App\Http\Contracts\ProductLowestPriceRepositoryContract;
use App\Http\DTOs\SyncProductPriceDTO;
use App\Http\Factories\ProductPricesExtractorFactory;
use Carbon\Carbon;
use JsonMachine\Items;

class SyncProductPriceStream
{
    private const string URL = 'url';

    private const string POINTER = 'json_pointer';

    private const string PRODUCT_ID = 'product_id';

    // This tracks the lowest price found *during this specific stream*
    private ?SyncProductPriceDTO $lowestInStream = null;

    public function __construct(
        private readonly HttpStreamClientContract $httpClient,
        public readonly ProductPricesExtractorFactory $productPricesExtractorFactory,
        private readonly ProductLowestPriceRepositoryContract $repository
    ) {}

    /**
     * Summary of __invoke
     * @param array $config
     * @param Carbon $fetchAt
     * @return void
     */
    public function __invoke(array $config, Carbon $fetchAt): void
    {
        $stream = $this->httpClient->stream($config[static::URL]);
        $extractor = $this->productPricesExtractorFactory->make($config[static::POINTER]);
        $items = Items::fromStream($stream, ['pointer' => $config[static::POINTER]]);

        foreach ($items as $item) {
            $dto = $extractor->extractProduct((array) $item, (int) $config[static::PRODUCT_ID]);
            $this->updateLocalLowest($dto);
        }

        $this->persistFinalResult($fetchAt);
    }

    /**
     * Summary of updateLocalLowest
     * @param SyncProductPriceDTO $dto
     * @return void
     */
    public function updateLocalLowest(SyncProductPriceDTO $dto): void
    {
        if (! $this->lowestInStream || $dto->price < $this->lowestInStream->price) {
            $this->lowestInStream = $dto;
        }
    }

    /**
     * Summary of persistFinalResult
     * @param Carbon $fetchAt
     * @return void
     */
    public function persistFinalResult(Carbon $fetchAt): void
    {
        if (! $this->lowestInStream || $this->repository->getProduct(
            $this->lowestInStream->productId
        )?->price <= $this->lowestInStream->price) {
            return;
        }

        $this->repository->saveLowestPrice(
            $this->lowestInStream->productId,
            $this->lowestInStream->vendorName,
            $this->lowestInStream->price,
            $fetchAt,
            true
        );

        // Reset for the next stream
        $this->lowestInStream = null;
    }
}

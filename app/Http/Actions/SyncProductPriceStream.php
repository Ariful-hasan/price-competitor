<?php

namespace App\Http\Actions;

use App\Http\Contracts\HttpStreamClientContract;
use App\Http\Contracts\ProductLowestPriceRepositoryContract;
use App\Http\DTOs\SyncProductPriceDTO;
use App\Http\Factories\ProductPricesExtractorFactory;
use Carbon\Carbon;
use \JsonMachine\Items;

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
     * Streams and processes product prices from a remote JSON feed, tracking the lowest price found.
     * 
     * This method streams a potentially large JSON feed from the URL provided in the $config array,
     * extracts product price information, identifies the lowest price during the stream, and 
     * persists the result using the repository. The extraction logic is delegated to an extractor 
     * built by the ProductPricesExtractorFactory based on the supplied JSON pointer.
     *
     * @param array $config  Configuration for the stream, including:
     *                       - 'url': The URL to fetch the remote feed from.
     *                       - 'json_pointer': JSON pointer path for extractor.
     *                       - 'product_id': The product ID to filter/extract.
     * @param Carbon $fetchAt  The timestamp to associate with the fetch operation.
     * 
     * @return void
     */
    public function __invoke(array $config, Carbon $fetchAt): void
    {
        $stream = $this->httpClient->stream($config[static::URL]);
        $extractor = $this->productPricesExtractorFactory->make($config[static::POINTER]);
        $items = Items::fromStream($stream, ['pointer' => $config[static::POINTER]]);

        foreach ($items as $item) {
            $dto = $extractor->extractProduct((array)$item, (int)$config[static::PRODUCT_ID]);
            $this->updateLocalLowest($dto);
        }

        $this->persistFinalResult($fetchAt);
    }


    /**
     * Updates the currently tracked lowest price DTO found during this stream.
     *
     * If no lowest price has been found yet, or if the given DTO has a lower price
     * than the current lowest, this replaces the stored DTO. Otherwise, no changes
     * are made.
     *
     * @param SyncProductPriceDTO $dto The DTO representing a product's price to be considered.
     * @return void
     */
    public function updateLocalLowest(SyncProductPriceDTO $dto): void
    {
        if (!$this->lowestInStream || $dto->price < $this->lowestInStream->price) {
            $this->lowestInStream = $dto;
        }
    }

    
    /**
     * Persists the lowest price product found during the current stream.
     *
     * If a lowest price product was found, this method saves it using the repository,
     * associating it with the given fetch timestamp. After persisting, the local
     * reference to the lowest price product is cleared in preparation for the next stream.
     *
     * @param Carbon $fetchAt The timestamp indicating when the prices were fetched.
     * @return void
     */
    public function persistFinalResult(Carbon $fetchAt): void
    {
        if (!$this->lowestInStream || $this->repository->getProduct(
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
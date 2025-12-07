<?php

namespace App\Http\Services\Product;


use App\Http\Factories\ProductPricesExtractorFactory;
use App\Http\Contracts\ExtractProductPriceContract;
use App\Http\Contracts\HttpStreamClientContract;
use App\Http\DTOs\SyncProductPriceDTO;
use App\Http\Repositories\ProductLowestPriceRepository;
use Carbon\Carbon;
use \JsonMachine\Items;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class PriceSyncService
{
    private const string URL = 'url';
    private const string POINTER = 'json_pointer';
    private const string PRODUCT_ID = 'product_id';
    private const string CACHE_KEY = 'competitor:product:';
    private const int CACHE_TTL = 3600;

    public function __construct(
        private HttpStreamClientContract $httpClient,
        public ProductPricesExtractorFactory $productPricesExtractorFactory,
        public ProductLowestPriceRepository $productLowestPriceRepository
    ) {}

    /**
     * Fetch All competitor prices.
     *
     * @param array $fetchUrls
     * @param Carbon $fetchAt
     * @return void
     */
    public function syncAllPrices(array $fetchUrls, Carbon $fetchAt): void
    {
        foreach ($fetchUrls as $key) {
            $prices = $this->fetchAndCalculatePrices($key);
            
            if ($prices instanceof SyncProductPriceDTO) {
                $this->productLowestPriceRepository->saveLowestPrice(
                    (int) $key[static::PRODUCT_ID], 
                    $prices->vendorName, 
                    $prices->price, 
                    $fetchAt
                );
            }
        }
    }

    /**
     * Fetch prices through HttpStream.
     *
     * @param array $config
     * @return array
     */
    private function fetchAndCalculatePrices(array $config): ?SyncProductPriceDTO
    {
        $stream = $this->httpClient->stream($config[static::URL]);
        $extractor = $this->productPricesExtractorFactory->make($config[static::POINTER]);

        return $this->processStream($stream, $extractor, $config);
    }

    /**
     * Process a stream to extract product prices.
     *
     * @param mixed $stream
     * @param ExtractProductPriceContract $extractor
     * @param array $config
     * @return array
     * @throws InvalidArgumentException
     */
    private function processStream(mixed $stream, ExtractProductPriceContract $extractor, array $config): ?SyncProductPriceDTO
    {

        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be Guzzle Stream or PHP resource. Got: ' . gettype($stream));
        }

        $prices = null;
        $items = Items::fromStream($stream, ['pointer' => $config[static::POINTER]]);

        foreach ($items as $item) {
            $syncProductPriceDto = $extractor->extractProduct((array)$item);
            $prices = $this->calculateProductLowestPrice(
                (int)$config[static::PRODUCT_ID],
                $syncProductPriceDto
            );
        }
        
        return $prices;
    }

    /**
     * Calculate the lowest price in cache.
     *
     * @param integer $productId
     * @param array $item
     * @return array
     */
    private function calculateProductLowestPrice(int $productId, SyncProductPriceDTO $dto): SyncProductPriceDTO
    {
        $data = Cache::has(static::CACHE_KEY . $productId) 
        ? unserialize(Cache::get(static::CACHE_KEY . $productId))
        : null;

        if (!$data) {
            Cache::set(static::CACHE_KEY . $productId, serialize($dto), static::CACHE_TTL);

            return $$dto;
        }

        if ($data->price > $dto->price) {
            Cache::set(static::CACHE_KEY . $productId, serialize($dto) , static::CACHE_TTL);
        }

        return $dto;
    }
}

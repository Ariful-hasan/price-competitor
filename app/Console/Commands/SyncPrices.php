<?php

namespace App\Console\Commands;

use App\Http\Actions\SyncProductPriceStream;
use App\Http\Contracts\ProductServiceContract;
use App\Http\Services\PriceSync\PriceSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stream big JSON feed and sync lowest prices';

    /**
     * Execute the console command.
     *
     * @param PriceSyncService $priceSyncService
     * @return void
     */
    public function handle(ProductServiceContract $productService): void
    {
        $this->info('Start Price Synchronizing.');
        $fetchAt = Carbon::now();
        $productIds = [];

        /**
         * store product prices to cache
         */
        foreach (config('competitor-apis') as $key) {
            try {
                app(SyncProductPriceStream::class)($key, $fetchAt);
                $productIds[] = $key['product_id'];
            } catch (Throwable $th) {
                Log::error("Sync failed for Product {$key['product_id']}: {$th->getMessage()}");
                $this->error("Sync failed: {$th->getMessage()}");
            }
        }
        
        /**
         * store lowest price product to database
         */
        foreach (array_unique($productIds) as $productId) {
            $product = $productService->getLowestPriceProductById($productId);
            $productService->storeLowestPriceProduct([
                'product_id' => $product->product_id,
                'vendor_name' => $product->vendor_name,
                'price' => $product->price,
                'fetch_at' => $fetchAt,
            ]);
        }

        $this->info('Price sync completed successfully.');
    }
}

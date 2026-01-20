<?php

namespace App\Console\Commands;

use App\Http\Actions\SyncProductPriceStream;
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
    public function handle(): void
    {
        $this->info('Start Price Synchronizing.');
        $fetchAt = Carbon::now();

        foreach (config('competitor-apis') as $key) {
            try {
                app(SyncProductPriceStream::class)($key, $fetchAt);
                $this->info('Price sync completed successfully.');
            } catch (Throwable $th) {
                Log::error("Sync failed for Product {$key['product_id']}: {$th->getMessage()}");
                $this->error("Sync failed: {$th->getMessage()}");
            }
        }
    }
}

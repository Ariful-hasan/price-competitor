<?php

namespace App\Console\Commands;

use App\Http\Services\PriceSyncService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

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
    public function handle(PriceSyncService $priceSyncService): void
    {
        try {
            $this->info('Start Price Synchronizing.');
            $priceSyncService->syncAllPrices(config('competitor-apis'), Carbon::now());
            $this->info('Price sync completed successfully.');
        } catch (Exception $e) {
            $this->error("Sync failed: {$e->getMessage()}");
            
        }
    }
}

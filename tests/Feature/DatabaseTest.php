<?php

namespace Tests\Feature;

use App\Models\ProductLowestPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure that the `product_lowest_prices` table can be written to and
     * read from using the Eloquent model and that the trusted Laravel
     * helpers such as `assertDatabaseHas` behave as expected.
     */
    public function test_product_lowest_price_can_be_created_and_retrieved(): void
    {
        // create a record using the factory so all default values are filled
        $created = ProductLowestPrice::factory()->create([
            'product_id' => 123456,
            'vendor'     => 'unit-test-vendor',
            'price'      => 42.50,
        ]);

        // the record should exist in the database
        $this->assertDatabaseHas('product_lowest_prices', [
            'product_id' => 123456,
            'vendor'     => 'unit-test-vendor',
            'price'      => 42.50,
        ]);

        // fetch it back through Eloquent to verify the attributes
        $fetched = ProductLowestPrice::where('product_id', 123456)->first();
        $this->assertNotNull($fetched);
        $this->assertEquals('unit-test-vendor', $fetched->vendor);
        $this->assertEquals(42.50, $fetched->price);
        $this->assertEquals($created->id, $fetched->id);
    }

    /**
     * The test database connection should be available and able to return
     * a PDO instance. This ensures the configuration is correct and that
     * migrations can run.
     */
    public function test_database_connection_is_available(): void
    {
        $pdo = DB::connection()->getPdo();
        $this->assertNotNull($pdo, 'PDO instance should not be null on the default connection');
    }

    /**
     * The products table must exist in the test database. This guards
     * against migrations not being executed or schema drift in tests.
     */
    public function test_product_lowest_prices_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('product_lowest_prices'), 'product_lowest_prices table should exist');
    }
}

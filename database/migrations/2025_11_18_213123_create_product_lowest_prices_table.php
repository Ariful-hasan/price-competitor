<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_lowest_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->index();
            $table->decimal('price', 8, 2);
            $table->string('vendor_name');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_lowest_prices');
    }
};

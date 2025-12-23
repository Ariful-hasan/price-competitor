<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductLowestPrice>
 */
class ProductLowestPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => $this->faker->randomDigitNotZero(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'vendor_name' => $this->faker->company(),
            'fetched_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}

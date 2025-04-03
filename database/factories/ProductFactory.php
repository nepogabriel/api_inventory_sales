<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost_price = $this->faker->randomFloat(2, 100, 2000);
        $sale_price = $cost_price + $this->faker->randomFloat(2, 50, 1000);

        return [
            'sku' => Str::uuid(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'cost_price' => $cost_price,
            'sale_price' => $sale_price,
        ];
    }
}

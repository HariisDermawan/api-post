<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        return [
            'product_category_id' => ProductCategory::query()->value('id')
                ?? ProductCategory::query()->create([
                    'name' => 'General',
                    'description' => 'General products',
                ])->id,
            'image' => null,
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 0, 1000000),
            'stock' => fake()->numberBetween(0, 1000),
        ];
    }
}

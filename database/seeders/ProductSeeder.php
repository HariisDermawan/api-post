<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::query()->firstOrCreate(
            ['name' => 'General'],
            ['description' => 'General products']
        );

        Product::factory(10)->create();
    }
}

<?php

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function productCategory(): ProductCategory
{
    return ProductCategory::query()->create([
        'name' => 'Electronics',
        'description' => 'Electronic products',
    ]);
}

test('an authenticated user can create a product with an image', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $category = productCategory();

    $response = $this->post('/api/v1/products', [
        'product_category_id' => $category->id,
        'image' => UploadedFile::fake()->image('keyboard.jpg'),
        'name' => 'Mechanical Keyboard',
        'price' => '1250000.00',
        'stock' => 20,
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.product_category_id', $category->id)
        ->assertJsonPath('data.name', 'Mechanical Keyboard')
        ->assertJsonPath('data.price', '1250000.00');

    $image = $response->json('data.image');

    $this->assertDatabaseHas('products', [
        'name' => 'Mechanical Keyboard',
        'product_category_id' => $category->id,
    ]);
    Storage::disk('public')->assertExists($image);
});

test('an authenticated user can list update and delete a product', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $product = Product::factory()->create(['name' => 'Mouse', 'stock' => 5]);

    $this->get('/api/v1/products?search=Mou')
        ->assertOk()
        ->assertJsonPath('data.items.0.id', $product->id);

    $this->patch("/api/v1/products/{$product->id}", [
        'name' => 'Wireless Mouse',
        'stock' => 8,
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Wireless Mouse')
        ->assertJsonPath('data.stock', 8);

    $this->delete("/api/v1/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

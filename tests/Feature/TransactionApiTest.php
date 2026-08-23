<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function transactionCustomer(): Customer
{
    return Customer::query()->create([
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
    ]);
}

test('an authenticated user can create a transaction and stock is decremented', function () {
    $this->actingAs(User::factory()->create());
    $customer = transactionCustomer();
    $product = Product::factory()->create([
        'name' => 'Keyboard',
        'price' => '100000.00',
        'stock' => 10,
    ]);

    $response = $this->post('/api/v1/transactions', [
        'customer_id' => $customer->id,
        'tax' => '10000.00',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.customer_id', $customer->id)
        ->assertJsonPath('data.subtotal', '200000.00')
        ->assertJsonPath('data.tax', '10000.00')
        ->assertJsonPath('data.total', '210000.00')
        ->assertJsonPath('data.items.0.product_id', $product->id)
        ->assertJsonPath('data.items.0.price', '100000.00')
        ->assertJsonPath('data.items.0.quantity', 2)
        ->assertJsonPath('data.items.0.subtotal', '200000.00');

    $this->assertDatabaseHas('transactions', [
        'customer_id' => $customer->id,
        'subtotal' => '200000.00',
        'total' => '210000.00',
    ]);
    $this->assertDatabaseHas('transaction_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'subtotal' => '200000.00',
    ]);
    $this->assertSame(8, $product->fresh()->stock);
});

test('a transaction is rejected when the stock is insufficient', function () {
    $this->actingAs(User::factory()->create());
    $customer = transactionCustomer();
    $product = Product::factory()->create(['stock' => 3]);

    $response = $this->post('/api/v1/transactions', [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5],
        ],
    ]);

    $response->assertStatus(422);
    $response->assertInvalid("items.{$product->id}.quantity");

    $this->assertDatabaseCount('transactions', 0);
    $this->assertDatabaseCount('transaction_items', 0);
    $this->assertSame(3, $product->fresh()->stock);
});

test('an authenticated user can list and show transactions', function () {
    $this->actingAs(User::factory()->create());
    $customer = transactionCustomer();
    $product = Product::factory()->create();

    Product::query()->whereKey($product->id)->update(['stock' => 10]);
    $this->post('/api/v1/transactions', [
        'customer_id' => $customer->id,
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertCreated();
    $id = $this->get('/api/v1/transactions')->json('data.items.0.id');

    $this->get('/api/v1/transactions')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.pagination.total', 1);

    $this->get("/api/v1/transactions/{$id}")
        ->assertOk()
        ->assertJsonPath('data.customer.name', 'Budi Santoso');
});

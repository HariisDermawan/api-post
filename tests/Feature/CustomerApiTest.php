<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function customer(): Customer
{
    return Customer::query()->create([
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
    ]);
}

test('an authenticated user can create a customer', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->post('/api/v1/customers', [
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Budi Santoso')
        ->assertJsonPath('data.phone', '081234567890');

    $this->assertDatabaseHas('customers', [
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
    ]);
});

test('an authenticated user can list update and delete a customer', function () {
    $this->actingAs(User::factory()->create());
    $customer = customer();

    $this->get('/api/v1/customers?search=Bud')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.items.0.id', $customer->id);

    $this->patch("/api/v1/customers/{$customer->id}", [
        'name' => 'Budi Setiawan',
        'phone' => '089876543210',
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Budi Setiawan')
        ->assertJsonPath('data.phone', '089876543210');

    $this->delete("/api/v1/customers/{$customer->id}")
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

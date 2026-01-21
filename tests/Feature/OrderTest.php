<?php

declare(strict_types=1);

use App\Models\User;
use Infrastructure\Persistence\Models\ProductModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->user = User::factory()->create();
    $this->user->assignRole('seller');
    $this->token = auth('api')->login($this->user);
});

test('can create order with items', function () {
    $product = ProductModel::create([
        'name' => 'Widget', 'sku' => 'WDG-001',
        'price_cents' => 1500, 'stock' => 20,
    ]);

    $response = $this->withToken($this->token)->postJson('/api/v1/orders', [
        'items' => [['product_id' => $product->id, 'quantity' => 2]],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', 'draft');
});

test('cannot create order with insufficient stock', function () {
    $product = ProductModel::create([
        'name' => 'Widget', 'sku' => 'WDG-002',
        'price_cents' => 1500, 'stock' => 1,
    ]);

    $response = $this->withToken($this->token)->postJson('/api/v1/orders', [
        'items' => [['product_id' => $product->id, 'quantity' => 5]],
    ]);

    $response->assertStatus(422);
});

test('can transition order status', function () {
    $product = ProductModel::create([
        'name' => 'Widget', 'sku' => 'WDG-003',
        'price_cents' => 1500, 'stock' => 20,
    ]);

    $create = $this->withToken($this->token)->postJson('/api/v1/orders', [
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $orderId = $create->json('data.id');

    $response = $this->withToken($this->token)
        ->patchJson("/api/v1/orders/{$orderId}/transition", ['status' => 'confirmed']);

    $response->assertOk()
        ->assertJsonPath('data.status', 'confirmed');
});

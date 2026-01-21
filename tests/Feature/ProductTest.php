<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    $this->token = auth('api')->login($this->user);
});

test('can create product', function () {
    $response = $this->withToken($this->token)->postJson('/api/v1/products', [
        'name' => 'New Product',
        'sku' => 'NP-001',
        'price_cents' => 2500,
        'stock' => 100,
        'description' => 'A test product',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.sku', 'NP-001');
});

test('cannot create duplicate sku', function () {
    $this->withToken($this->token)->postJson('/api/v1/products', [
        'name' => 'Product A', 'sku' => 'DUP-001', 'price_cents' => 1000, 'stock' => 5,
    ]);

    $response = $this->withToken($this->token)->postJson('/api/v1/products', [
        'name' => 'Product B', 'sku' => 'DUP-001', 'price_cents' => 2000, 'stock' => 10,
    ]);

    $response->assertStatus(422);
});

test('can list products with pagination', function () {
    $this->withToken($this->token)->postJson('/api/v1/products', [
        'name' => 'Listed', 'sku' => 'LST-001', 'price_cents' => 500, 'stock' => 1,
    ]);

    $response = $this->withToken($this->token)->getJson('/api/v1/products?page=1&per_page=10');

    $response->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

test('can delete product', function () {
    $create = $this->withToken($this->token)->postJson('/api/v1/products', [
        'name' => 'ToDelete', 'sku' => 'DEL-001', 'price_cents' => 100, 'stock' => 0,
    ]);

    $id = $create->json('data.id');
    $response = $this->withToken($this->token)->deleteJson("/api/v1/products/{$id}");
    $response->assertStatus(204);
});

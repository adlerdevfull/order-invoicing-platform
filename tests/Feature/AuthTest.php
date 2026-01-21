<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('user can register', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role' => 'seller',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['user', 'token', 'token_type']]);
});

test('user can login', function () {
    User::factory()->create([
        'email' => 'login@test.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'login@test.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['token', 'token_type', 'expires_in']]);
});

test('login fails with wrong credentials', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'wrong@test.com',
        'password' => 'wrong',
    ]);

    $response->assertStatus(401);
});

test('protected route requires token', function () {
    $response = $this->getJson('/api/v1/auth/me');
    $response->assertStatus(401);
});

<?php

declare(strict_types=1);

use Domain\Product\Entities\Product;

test('product has stock', function () {
    $product = new Product(1, 'Test', 'SKU-001', 1000, 10);
    expect($product->hasStock(5))->toBeTrue();
    expect($product->hasStock(15))->toBeFalse();
});

test('reserve stock decreases quantity', function () {
    $product = new Product(1, 'Test', 'SKU-001', 1000, 10);
    $product->reserveStock(3);
    expect($product->stock)->toBe(7);
});

test('reserve stock throws on insufficient', function () {
    $product = new Product(1, 'Test', 'SKU-001', 1000, 2);
    $product->reserveStock(5);
})->throws(\DomainException::class);

test('release stock increases quantity', function () {
    $product = new Product(1, 'Test', 'SKU-001', 1000, 5);
    $product->releaseStock(3);
    expect($product->stock)->toBe(8);
});

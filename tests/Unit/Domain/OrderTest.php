<?php

declare(strict_types=1);

use Domain\Order\Entities\{Order, OrderItem};
use Domain\Order\Enums\OrderStatus;
use Domain\Order\ValueObjects\Money;

test('order calculates totals with items', function () {
    $order = new Order(id: null, userId: 1);
    $order->setItems([
        new OrderItem(null, 1, 2, new Money(1000)), // 20.00
        new OrderItem(null, 2, 1, new Money(500)),  // 5.00
    ]);

    expect($order->subtotal->amount)->toBe(2500);
    expect($order->tax->amount)->toBe(525); // 21% of 2500
    expect($order->shipping->amount)->toBe(500); // < 50€ = 5€ shipping
});

test('free shipping over 50 euros', function () {
    $order = new Order(id: null, userId: 1);
    $order->setItems([
        new OrderItem(null, 1, 10, new Money(1000)), // 100.00
    ]);

    expect($order->shipping->amount)->toBe(0);
});

test('order transition works', function () {
    $order = new Order(id: 1, userId: 1, status: OrderStatus::Draft);
    $order->transitionTo(OrderStatus::Confirmed);
    expect($order->status)->toBe(OrderStatus::Confirmed);
});

test('invalid transition throws', function () {
    $order = new Order(id: 1, userId: 1, status: OrderStatus::Draft);
    $order->transitionTo(OrderStatus::Shipped);
})->throws(\DomainException::class);

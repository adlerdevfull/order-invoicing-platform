<?php

declare(strict_types=1);

use Domain\Order\ValueObjects\Money;

test('creates money from float', function () {
    $money = Money::fromFloat(10.50);
    expect($money->amount)->toBe(1050);
    expect($money->currency)->toBe('EUR');
});

test('adds two money values', function () {
    $a = new Money(1000);
    $b = new Money(500);
    $result = $a->add($b);
    expect($result->amount)->toBe(1500);
});

test('multiplies money by quantity', function () {
    $money = new Money(250);
    $result = $money->multiply(3);
    expect($result->amount)->toBe(750);
});

test('applies percentage', function () {
    $money = new Money(10000);
    $tax = $money->applyPercentage(0.21);
    expect($tax->amount)->toBe(2100);
});

test('converts to float', function () {
    $money = new Money(1999);
    expect($money->toFloat())->toBe(19.99);
});

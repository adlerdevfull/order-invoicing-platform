<?php

declare(strict_types=1);

use Domain\Order\Enums\OrderStatus;

test('draft can transition to confirmed', function () {
    expect(OrderStatus::Draft->canTransitionTo(OrderStatus::Confirmed))->toBeTrue();
});

test('draft can transition to cancelled', function () {
    expect(OrderStatus::Draft->canTransitionTo(OrderStatus::Cancelled))->toBeTrue();
});

test('draft cannot transition to paid', function () {
    expect(OrderStatus::Draft->canTransitionTo(OrderStatus::Paid))->toBeFalse();
});

test('confirmed can transition to paid', function () {
    expect(OrderStatus::Confirmed->canTransitionTo(OrderStatus::Paid))->toBeTrue();
});

test('shipped cannot transition', function () {
    expect(OrderStatus::Shipped->canTransitionTo(OrderStatus::Cancelled))->toBeFalse();
});

test('cancelled cannot transition', function () {
    expect(OrderStatus::Cancelled->canTransitionTo(OrderStatus::Draft))->toBeFalse();
});

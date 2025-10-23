<?php

declare(strict_types=1);

namespace Application\Order\Commands;

final readonly class CreateOrderCommand
{
    public function __construct(
        public int $userId,
        public array $items, // [{product_id, quantity}]
        public int $discountCents = 0,
    ) {}
}

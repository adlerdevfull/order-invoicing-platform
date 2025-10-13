<?php

declare(strict_types=1);

namespace Domain\Order\Entities;

use Domain\Order\ValueObjects\Money;

final class OrderItem
{
    public function __construct(
        public ?int $id,
        public int $productId,
        public int $quantity,
        public Money $unitPrice,
    ) {}

    public function total(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}

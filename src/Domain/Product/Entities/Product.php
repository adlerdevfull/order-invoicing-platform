<?php

declare(strict_types=1);

namespace Domain\Product\Entities;

final class Product
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $sku,
        public int $priceInCents,
        public int $stock,
        public string $description = '',
    ) {}

    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function reserveStock(int $quantity): void
    {
        if (!$this->hasStock($quantity)) {
            throw new \DomainException("Insufficient stock for product {$this->sku}");
        }
        $this->stock -= $quantity;
    }

    public function releaseStock(int $quantity): void
    {
        $this->stock += $quantity;
    }
}

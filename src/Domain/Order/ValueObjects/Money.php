<?php

declare(strict_types=1);

namespace Domain\Order\ValueObjects;

final readonly class Money
{
    public function __construct(
        public int $amount, // cents
        public string $currency = 'EUR',
    ) {}

    public static function fromFloat(float $value, string $currency = 'EUR'): self
    {
        return new self((int) round($value * 100), $currency);
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(int $quantity): self
    {
        return new self($this->amount * $quantity, $this->currency);
    }

    public function applyPercentage(float $percentage): self
    {
        return new self((int) round($this->amount * $percentage), $this->currency);
    }
}

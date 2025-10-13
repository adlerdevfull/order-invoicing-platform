<?php

declare(strict_types=1);

namespace Domain\Order\Entities;

use Domain\Order\Enums\OrderStatus;
use Domain\Order\ValueObjects\Money;

final class Order
{
    /** @var OrderItem[] */
    private array $items = [];

    public function __construct(
        public ?int $id,
        public int $userId,
        public OrderStatus $status = OrderStatus::Draft,
        public ?Money $subtotal = null,
        public ?Money $tax = null,
        public ?Money $shipping = null,
        public ?Money $discount = null,
        public ?Money $total = null,
    ) {}

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
        $this->recalculate();
    }

    /** @return OrderItem[] */
    public function items(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
        $this->recalculate();
    }

    public function transitionTo(OrderStatus $newStatus): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw new \DomainException(
                "Cannot transition from {$this->status->value} to {$newStatus->value}"
            );
        }
        $this->status = $newStatus;
    }

    private function recalculate(): void
    {
        $subtotal = new Money(0);
        foreach ($this->items as $item) {
            $subtotal = $subtotal->add($item->total());
        }

        $this->subtotal = $subtotal;
        $this->tax = $subtotal->applyPercentage(0.21); // IVA general
        $this->shipping = $subtotal->amount > 5000 ? new Money(0) : new Money(500);
        $this->discount = $this->discount ?? new Money(0);
        $this->total = $this->subtotal->add($this->tax)->add($this->shipping)
            ->add(new Money(-$this->discount->amount));
    }
}

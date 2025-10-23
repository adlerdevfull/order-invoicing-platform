<?php

declare(strict_types=1);

namespace Application\Order\Commands;

use Domain\Order\Entities\Order;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Repositories\OrderRepositoryInterface;

final readonly class TransitionOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
    ) {}

    public function handle(int $orderId, OrderStatus $newStatus): Order
    {
        $order = $this->orders->findById($orderId)
            ?? throw new \DomainException("Order not found");

        $order->transitionTo($newStatus);

        return $this->orders->save($order);
    }
}

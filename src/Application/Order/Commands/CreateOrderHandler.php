<?php

declare(strict_types=1);

namespace Application\Order\Commands;

use Domain\Order\Entities\{Order, OrderItem};
use Domain\Order\Repositories\OrderRepositoryInterface;
use Domain\Order\ValueObjects\Money;
use Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class CreateOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private ProductRepositoryInterface $products,
    ) {}

    public function handle(CreateOrderCommand $command): Order
    {
        $order = new Order(id: null, userId: $command->userId);
        $items = [];

        foreach ($command->items as $itemData) {
            $product = $this->products->findById($itemData['product_id'])
                ?? throw new \DomainException("Product {$itemData['product_id']} not found");

            $product->reserveStock($itemData['quantity']);
            $this->products->save($product);

            $items[] = new OrderItem(
                id: null,
                productId: $product->id,
                quantity: $itemData['quantity'],
                unitPrice: new Money($product->priceInCents),
            );
        }

        $order->setItems($items);

        if ($command->discountCents > 0) {
            $order->discount = new Money($command->discountCents);
        }

        return $this->orders->save($order);
    }
}

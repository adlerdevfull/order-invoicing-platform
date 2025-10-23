<?php

declare(strict_types=1);

namespace Application\Product\Commands;

use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepositoryInterface;

final readonly class ProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    public function create(array $data): Product
    {
        $existing = $this->products->findBySku($data['sku']);
        if ($existing) {
            throw new \DomainException("Product with SKU {$data['sku']} already exists");
        }

        $product = new Product(
            id: null,
            name: $data['name'],
            sku: $data['sku'],
            priceInCents: $data['price_cents'],
            stock: $data['stock'] ?? 0,
            description: $data['description'] ?? '',
        );

        return $this->products->save($product);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->products->findById($id)
            ?? throw new \DomainException("Product not found");

        if (isset($data['name'])) $product->name = $data['name'];
        if (isset($data['description'])) $product->description = $data['description'];
        if (isset($data['price_cents'])) $product->priceInCents = $data['price_cents'];
        if (isset($data['stock'])) $product->stock = $data['stock'];

        return $this->products->save($product);
    }

    public function delete(int $id): void
    {
        $this->products->findById($id)
            ?? throw new \DomainException("Product not found");

        $this->products->delete($id);
    }
}

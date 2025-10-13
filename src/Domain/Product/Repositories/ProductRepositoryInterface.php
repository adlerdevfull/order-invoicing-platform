<?php

declare(strict_types=1);

namespace Domain\Product\Repositories;

use Domain\Product\Entities\Product;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findBySku(string $sku): ?Product;
    /** @return Product[] */
    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array;
    public function count(array $filters = []): int;
    public function save(Product $product): Product;
    public function delete(int $id): void;
}

<?php

declare(strict_types=1);

namespace Domain\Order\Repositories;

use Domain\Order\Entities\Order;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    /** @return Order[] */
    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array;
    public function count(array $filters = []): int;
    public function save(Order $order): Order;
}

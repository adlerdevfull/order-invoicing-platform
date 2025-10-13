<?php

declare(strict_types=1);

namespace Domain\Invoice\Repositories;

use Domain\Invoice\Entities\Invoice;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;
    public function findByOrderId(int $orderId): ?Invoice;
    /** @return Invoice[] */
    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array;
    public function count(array $filters = []): int;
    public function save(Invoice $invoice): Invoice;
}

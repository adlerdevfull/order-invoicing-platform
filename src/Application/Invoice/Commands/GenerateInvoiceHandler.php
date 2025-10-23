<?php

declare(strict_types=1);

namespace Application\Invoice\Commands;

use Domain\Invoice\Entities\Invoice;
use Domain\Invoice\Enums\TaxType;
use Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use Domain\Order\Repositories\OrderRepositoryInterface;
use Domain\Order\Enums\OrderStatus;

final readonly class GenerateInvoiceHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoices,
        private OrderRepositoryInterface $orders,
    ) {}

    public function handle(int $orderId, TaxType $taxType = TaxType::General): Invoice
    {
        $order = $this->orders->findById($orderId)
            ?? throw new \DomainException("Order not found");

        if ($order->status !== OrderStatus::Paid) {
            throw new \DomainException("Invoice can only be generated for paid orders");
        }

        if ($this->invoices->findByOrderId($orderId)) {
            throw new \DomainException("Invoice already exists for this order");
        }

        $invoice = Invoice::generate($orderId, $order->subtotal, $taxType);

        return $this->invoices->save($invoice);
    }
}

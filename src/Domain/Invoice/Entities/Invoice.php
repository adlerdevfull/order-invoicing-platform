<?php

declare(strict_types=1);

namespace Domain\Invoice\Entities;

use Domain\Invoice\Enums\TaxType;
use Domain\Order\ValueObjects\Money;

final class Invoice
{
    public function __construct(
        public ?int $id,
        public int $orderId,
        public string $number,
        public string $identificationKey,
        public TaxType $taxType,
        public Money $netAmount,
        public Money $taxAmount,
        public Money $totalAmount,
        public ?string $digitalSignature = null,
        public ?\DateTimeImmutable $issuedAt = null,
    ) {}

    public static function generate(
        int $orderId,
        Money $netAmount,
        TaxType $taxType = TaxType::General,
    ): self {
        $taxAmount = $netAmount->applyPercentage($taxType->rate());
        $total = $netAmount->add($taxAmount);

        $invoice = new self(
            id: null,
            orderId: $orderId,
            number: self::generateNumber(),
            identificationKey: self::generateKey(),
            taxType: $taxType,
            netAmount: $netAmount,
            taxAmount: $taxAmount,
            totalAmount: $total,
            issuedAt: new \DateTimeImmutable(),
        );

        $invoice->sign();
        return $invoice;
    }

    private static function generateNumber(): string
    {
        return 'INV-' . date('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private static function generateKey(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function sign(): void
    {
        $payload = implode('|', [
            $this->number,
            $this->identificationKey,
            $this->totalAmount->amount,
            $this->issuedAt->format('Y-m-d\TH:i:s'),
        ]);
        $this->digitalSignature = hash('sha256', $payload);
    }
}
